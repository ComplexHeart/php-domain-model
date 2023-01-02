<?php

declare(strict_types=1);

namespace ComplexHeart\Domain\Model\ValueObjects;

use ReflectionClass;

/**
 * Class EnumValue
 *
 * @method mixed value()
 *
 * @author Unay Santisteban <usantisteban@othercode.es>
 * @package ComplexHeart\Domain\Model\ValueObjects
 */
abstract class EnumValue extends Value
{
    /**
     * The Enum value.
     *
     * @var mixed
     */
    protected mixed $value;

    /**
     * Internal cache.
     *
     * @var array
     */
    protected static array $cache = [];

    /**
     * EnumValue constructor.
     *
     * @param  mixed  $value
     */
    public function __construct(mixed $value)
    {
        $this->initialize(['value' => $value]);
    }

    /**
     * Returns the cached constant data of the class.
     *
     * @return array
     */
    private static function cache(): array
    {
        if (!isset(static::$cache[static::class])) {
            $reflection = new ReflectionClass(static::class);
            static::$cache[static::class] = $reflection->getConstants();
        }

        return static::$cache[static::class];
    }

    /**
     * Check if the given value is in the list of allowed values.
     *
     * @return bool
     */
    protected function invariantValueMustBeOneOfAllowed(): bool
    {
        return static::isValid($this->value());
    }

    /**
     * Check if the given value is valid for the current enum.
     *
     * @param  mixed  $value
     *
     * @return bool
     */
    public static function isValid(mixed $value): bool
    {
        return in_array($value, static::getValues(), true);
    }

    /**
     * Return the available labels.
     *
     * @return string[]
     */
    public static function getLabels(): array
    {
        return array_keys(self::cache());
    }

    /**
     * Return the available values.
     *
     * @return string[]
     */
    public static function getValues(): array
    {
        return array_values(self::cache());
    }

    /**
     * Return the string representation of the object.
     *
     * @return string
     */
    public function __toString(): string
    {
        return (string)$this->value;
    }
}
