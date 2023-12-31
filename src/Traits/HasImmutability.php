<?php

declare(strict_types=1);

namespace ComplexHeart\Domain\Model\Traits;

use ComplexHeart\Domain\Model\Errors\ImmutabilityError;

/**
 * Trait HasImmutability
 *
 * Non-canonical way to enforce immutability of an object. Will be
 * removed in the future as is not an intuitive and explicit way to
 * have immutability in PHP. For PHP >= 8.1 use readonly keyword.
 *
 * @author Unay Santisteban <usantisteban@othercode.io>
 * @package ComplexHeart\Domain\Model\Traits
 * @deprecated Will be removed in future versions.
 */
trait HasImmutability
{
    /**
     * Enforces the immutability by blocking any attempts of update any property.
     *
     * @param  string  $name
     * @param $_
     * @return void
     */
    final public function __set(string $name, $_): void
    {
        $class = static::class;
        throw new ImmutabilityError("Cannot modify property $name from immutable $class object.");
    }

    /**
     * Accessor for the protected or private properties.
     *
     * @param  string  $name
     * @return mixed
     */
    final public function __get(string $name): mixed
    {
        return method_exists($this, 'get')
            ? $this->get($name)
            : $this->{$name};
    }

    /**
     * Creates a new instance overriding the given values.
     *
     * @param  array<string, mixed>  $overrides
     *
     * @return static
     */
    protected function withOverrides(array $overrides): static
    {
        return self::make(...array_values(array_merge($this->values(), $overrides)));
    }
}
