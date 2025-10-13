<?php

declare(strict_types=1);

namespace ComplexHeart\Domain\Model\Traits;

/**
 * Trait HasTypeValidation
 *
 * @author Unay Santisteban <usantisteban@othercode.io>
 * @package ComplexHeart\Domain\Model\Traits
 */
trait HasTypeCheck
{
    /**
     * Assert that the given value type match the required validType.
     *
     * @param  mixed  $value
     * @param  string  $validType
     *
     * @return bool
     */
    protected function isValueTypeValid(mixed $value, string $validType): bool
    {
        if ($validType === 'mixed') {
            return true;
        }

        $primitives = ['integer', 'boolean', 'float', 'string', 'array', 'object', 'callable'];
        $validation = in_array($validType, $primitives)
            ? fn ($value): bool => gettype($value) === $validType
            : fn ($value): bool => $value instanceof $validType;

        return $validation($value);
    }

    /**
     * Assert that the given value type NOT match the required validType.
     *
     * @param  mixed  $value
     * @param  string  $validType
     *
     * @return bool
     */
    protected function isValueTypeNotValid(mixed $value, string $validType): bool
    {
        return !$this->isValueTypeValid($value, $validType);
    }
}
