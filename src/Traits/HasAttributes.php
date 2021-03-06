<?php

declare(strict_types=1);

namespace ComplexHeart\Domain\Model\Traits;

use function Lambdish\Phunctional\map;

/**
 * Trait HasAttributes
 *
 * @author Unay Santisteban <usantisteban@othercode.es>
 * @package ComplexHeart\Domain\Model\Traits
 */
trait HasAttributes
{
    /**
     * Return the list of attributes of the current class.
     * Properties starting with "_" will be considered as internal use only.
     *
     * @return array<string>
     */
    final public static function attributes(): array
    {
        return array_filter(
            array_keys(get_class_vars(static::class)),
            fn(string $item): bool => strpos($item, '_') !== 0
        );
    }

    /**
     * Return the attribute values.
     * Properties starting with "_" will be considered as internal use only.
     *
     * @return array<string, mixed>
     */
    final public function values(): array
    {
        $allowed = static::attributes();

        return array_intersect_key(
            get_object_vars($this),
            array_combine($allowed, $allowed)
        );
    }

    /**
     * Populate the object recursively.
     *
     * @param  iterable  $source
     */
    final protected function hydrate(iterable $source): void
    {
        foreach ($source as $key => $value) {
            $this->set($key, $value);
        }
    }

    /**
     * Get the required attribute value.
     *
     * @param  string  $attribute
     *
     * @return mixed|null
     */
    final protected function get(string $attribute)
    {
        if (in_array($attribute, static::attributes())) {
            $method = $this->getStringKey($attribute, 'get', 'Value');

            return ($this->canCall($method))
                ? call_user_func_array([$this, $method], [$this->{$attribute}])
                : $this->{$attribute};
        }

        return null;
    }

    /**
     * Set an attribute value.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     */
    final protected function set(string $attribute, $value): void
    {
        if (in_array($attribute, $this->attributes())) {
            $method = $this->getStringKey($attribute, 'set', 'Value');

            $this->{$attribute} = ($this->canCall($method))
                ? call_user_func_array([$this, $method], [$value])
                : $value;
        }
    }

    /**
     * Return the required string key.
     * - $prefix     = 'get'
     * - $id         = 'Name'
     * - $suffix     = 'Value'
     * will be: getNameValue
     *
     * @param  string  $prefix
     * @param  string  $id
     * @param  string  $suffix
     *
     * @return string
     */
    protected function getStringKey(string $id, string $prefix = '', string $suffix = ''): string
    {
        return sprintf(
            '%s%s%s',
            $prefix,
            implode('', map(fn(string $chunk): string => ucfirst($chunk), explode('_', $id))),
            $suffix
        );
    }

    /**
     * Check if the required method name is callable.
     *
     * @param  string  $method
     *
     * @return bool
     */
    protected function canCall(string $method): bool
    {
        return method_exists($this, $method);
    }

    /**
     * Dynamic method to access each attribute as method, i.e:
     *  $user->name() will access the private attribute name.
     *
     * @param  string  $attribute
     * @param  array  $values
     *
     * @return mixed|null
     */
    public function __call(string $attribute, array $values)
    {
        return $this->get($attribute);
    }

    /**
     * This method is called by var_dump() when dumping an object to
     * get the properties that should be shown.
     *
     * @return array<string, mixed>
     */
    public function __debugInfo(): array
    {
        return $this->values();
    }
}
