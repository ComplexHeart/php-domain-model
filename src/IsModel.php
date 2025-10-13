<?php

declare(strict_types=1);

namespace ComplexHeart\Domain\Model;

use ComplexHeart\Domain\Model\Traits\HasAttributes;
use ComplexHeart\Domain\Model\Traits\HasInvariants;

/**
 * Trait IsModel
 *
 * Provides type-safe object instantiation with automatic invariant checking.
 *
 * Key improvements in this version:
 * - Type-safe make() method with validation
 * - Automatic invariant checking after construction
 * - Constructor as single source of truth
 * - Better error messages
 *
 * @author Unay Santisteban <usantisteban@othercode.io>
 */
trait IsModel
{
    use HasAttributes;
    use HasInvariants;

    /**
     * Create instance with type-safe validation.
     *
     * This method:
     * 1. Validates parameter types against constructor signature
     * 2. Creates instance through constructor (type-safe)
     * 3. Invariants are checked automatically after construction
     *
     * @param mixed ...$params Constructor parameters
     * @return static
     * @throws \InvalidArgumentException When required parameters are missing
     * @throws \TypeError When parameter types don't match
     */
    final public static function make(mixed ...$params): static
    {
        $reflection = new \ReflectionClass(static::class);
        $constructor = $reflection->getConstructor();

        if (!$constructor) {
            throw new \RuntimeException(
                sprintf('%s must have a constructor to use make()', static::class)
            );
        }

        // Validate parameters against constructor signature
        // array_values ensures we have a proper indexed array
        self::validateConstructorParameters($constructor, array_values($params));

        // Create instance through constructor (PHP handles type enforcement)
        // @phpstan-ignore-next-line - new static() is safe here as we validated the constructor
        $instance = new static(...$params);

        // Auto-check invariants if enabled
        $instance->autoCheckInvariants();

        return $instance;
    }

    /**
     * Validate parameters match constructor signature.
     *
     * @param \ReflectionMethod $constructor
     * @param array<int, mixed> $params
     * @return void
     * @throws \InvalidArgumentException
     * @throws \TypeError
     */
    private static function validateConstructorParameters(
        \ReflectionMethod $constructor,
        array $params
    ): void {
        $constructorParams = $constructor->getParameters();
        $required = $constructor->getNumberOfRequiredParameters();

        // Check parameter count
        if (count($params) < $required) {
            $missing = array_slice($constructorParams, count($params), $required - count($params));
            $names = array_map(fn ($p) => $p->getName(), $missing);
            throw new \InvalidArgumentException(
                sprintf(
                    '%s::make() missing required parameters: %s',
                    basename(str_replace('\\', '/', static::class)),
                    implode(', ', $names)
                )
            );
        }

        // Validate types for each parameter
        foreach ($constructorParams as $index => $param) {
            if (!isset($params[$index])) {
                continue; // Optional parameter not provided
            }

            $value = $params[$index];
            $type = $param->getType();

            if (!$type instanceof \ReflectionNamedType) {
                continue; // No type hint or union type
            }

            $typeName = $type->getName();
            $isValid = self::validateType($value, $typeName, $type->allowsNull());

            if (!$isValid) {
                throw new \TypeError(
                    sprintf(
                        '%s::make() parameter "%s" must be of type %s, %s given',
                        basename(str_replace('\\', '/', static::class)),
                        $param->getName(),
                        $typeName,
                        get_debug_type($value)
                    )
                );
            }
        }
    }

    /**
     * Validate a value matches expected type.
     *
     * @param mixed $value
     * @param string $typeName
     * @param bool $allowsNull
     * @return bool
     */
    private static function validateType(mixed $value, string $typeName, bool $allowsNull): bool
    {
        if ($value === null) {
            return $allowsNull;
        }

        return match($typeName) {
            'int' => is_int($value),
            'float' => is_float($value) || is_int($value), // Allow int for float
            'string' => is_string($value),
            'bool' => is_bool($value),
            'array' => is_array($value),
            'object' => is_object($value),
            'callable' => is_callable($value),
            'iterable' => is_iterable($value),
            'mixed' => true,
            default => $value instanceof $typeName
        };
    }

    /**
     * Determine if invariants should be checked automatically after construction.
     *
     * Override this method in your class to disable auto-check:
     *
     * protected function shouldAutoCheckInvariants(): bool
     * {
     *     return false;
     * }
     *
     * @return bool
     */
    protected function shouldAutoCheckInvariants(): bool
    {
        return false; // Disabled by default for backward compatibility
    }

    /**
     * Called after construction to auto-check invariants.
     *
     * This method is automatically called after the constructor completes
     * if shouldAutoCheckInvariants() returns true.
     *
     * @return void
     */
    private function autoCheckInvariants(): void
    {
        if ($this->shouldAutoCheckInvariants()) {
            $this->check();
        }
    }

    /**
     * Initialize the Model (legacy method - DEPRECATED).
     *
     * @deprecated Use constructor with make() factory method instead.
     *             This method will be removed in v1.0.0
     *
     * @param  array<int|string, mixed>  $source
     * @param  string|callable  $onFail
     * @return static
     */
    protected function initialize(array $source, string|callable $onFail = 'invariantHandler'): static
    {
        $this->hydrate($this->prepareAttributes($source));
        $this->check($onFail);

        return $this;
    }

    /**
     * Transform an indexed array into assoc array (legacy method - DEPRECATED).
     *
     * @deprecated This method will be removed in v1.0.0
     * @param  array<int|string, mixed>  $source
     * @return array<string, mixed>
     */
    private function prepareAttributes(array $source): array
    {
        // check if the array is indexed or associative.
        $isIndexed = fn ($source): bool => ([] !== $source) && array_keys($source) === range(0, count($source) - 1);

        /** @var array<string, mixed> $source */
        return $isIndexed($source)
            // combine the attributes keys with the provided source values.
            ? array_combine(array_slice(static::attributes(), 0, count($source)), $source)
            // return the already mapped array source.
            : $source;
    }
}
