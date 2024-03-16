<?php

declare(strict_types=1);

namespace ComplexHeart\Domain\Model\Traits;

use Closure;

use function Lambdish\Phunctional\map;

/**
 * Trait HasAttributes
 *
 * @author Unay Santisteban <usantisteban@othercode.io>
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
            fn(string $item): bool => !str_starts_with($item, '_')
        );
    }

    /**
     * Return the attribute values.
     * Properties starting with "_" will be considered as internal use only.
     *
     * @return array<string, mixed>
     */
    final public function values(Closure $fn = null): array
    {
        $allowed = static::attributes();

        $attributes = array_intersect_key(
            get_object_vars($this),
            array_combine($allowed, $allowed)
        );

        if (is_callable($fn)) {
            return map($fn, $attributes);
        }

        return $attributes;
    }

    /**
     * Populate the object recursively.
     *
     * @param  iterable<string, mixed>  $source
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
    final protected function get(string $attribute): mixed
    {
        if (in_array($attribute, static::attributes())) {
            $method = $this->getStringKey($attribute, 'get', 'Value');

            return method_exists($this, $method)
                ? $this->{$method}($this->{$attribute})
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
    final protected function set(string $attribute, mixed $value): void
    {
        if (in_array($attribute, $this->attributes())) {
            $method = $this->getStringKey($attribute, 'set', 'Value');

            $this->{$attribute} = method_exists($this, $method)
                ? $this->{$method}($value)
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
     * Dynamic method to access each attribute as method, i.e:
     *  $user->name() will access the private attribute name.
     *
     * @param  string  $attribute
     * @param  array<int, mixed>  $_
     * @return mixed|null
     * @deprecated will be removed in version 3.0
     */
    public function __call(string $attribute, array $_): mixed
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
