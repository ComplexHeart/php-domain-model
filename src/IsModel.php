<?php

declare(strict_types=1);

namespace ComplexHeart\Domain\Model;

use ComplexHeart\Domain\Model\Traits\HasAttributes;
use ComplexHeart\Domain\Model\Traits\HasInvariants;
use InvalidArgumentException;
use ReflectionClass;
use ReflectionMethod;
use ReflectionNamedType;
use ReflectionUnionType;
use RuntimeException;
use TypeError;

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
     * @throws InvalidArgumentException When required parameters are missing
     * @throws TypeError When parameter types don't match
     */
    final public static function make(mixed ...$params): static
    {
        $reflection = new ReflectionClass(static::class);
        $constructor = $reflection->getConstructor();

        if (!$constructor) {
            throw new RuntimeException(
                sprintf('%s must have a constructor to use make()', static::class)
            );
        }

        // Handle named parameters if provided
        if (self::hasNamedParameters($params)) {
            $params = self::mapNamedToPositional($constructor, $params);
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
     * Alias for make() method - more idiomatic for domain objects.
     *
     * Example: Customer::new(id: $id, name: 'John Doe')
     *
     * @param mixed ...$params Constructor parameters
     * @return static
     * @throws InvalidArgumentException When required parameters are missing
     * @throws TypeError When parameter types don't match
     */
    final public static function new(mixed ...$params): static
    {
        return static::make(...$params);
    }

    /**
     * Validate parameters match constructor signature.
     *
     * @param ReflectionMethod $constructor
     * @param array<int, mixed> $params
     * @return void
     * @throws InvalidArgumentException
     * @throws TypeError
     */
    private static function validateConstructorParameters(
        ReflectionMethod $constructor,
        array $params
    ): void {
        $constructorParams = $constructor->getParameters();
        $required = $constructor->getNumberOfRequiredParameters();

        // Check parameter count
        if (count($params) < $required) {
            $missing = array_slice($constructorParams, count($params), $required - count($params));
            $names = array_map(fn ($p) => $p->getName(), $missing);
            throw new InvalidArgumentException(
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

            if ($type === null) {
                continue; // No type hint
            }

            $isValid = false;
            $expectedTypes = '';

            if ($type instanceof ReflectionNamedType) {
                // Single type
                $isValid = self::validateType($value, $type->getName(), $type->allowsNull());
                $expectedTypes = $type->getName();
            } elseif ($type instanceof ReflectionUnionType) {
                // Union type (e.g., int|float|string)
                $isValid = self::validateUnionType($value, $type);
                $expectedTypes = implode('|', array_map(
                    fn($t) => $t instanceof ReflectionNamedType ? $t->getName() : 'mixed',
                    $type->getTypes()
                ));
            } else {
                continue; // Intersection types or other complex types not supported yet
            }

            if (!$isValid) {
                throw new TypeError(
                    sprintf(
                        '%s::make() parameter "%s" must be of type %s, %s given',
                        basename(str_replace('\\', '/', static::class)),
                        $param->getName(),
                        $expectedTypes,
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
     * Validate a value matches one of the types in a union type.
     *
     * @param mixed $value
     * @param ReflectionUnionType $unionType
     * @return bool
     */
    private static function validateUnionType(mixed $value, ReflectionUnionType $unionType): bool
    {
        // Check if null is allowed in the union
        $allowsNull = $unionType->allowsNull();

        if ($value === null) {
            return $allowsNull;
        }

        // Try to match against each type in the union
        foreach ($unionType->getTypes() as $type) {
            if (!$type instanceof ReflectionNamedType) {
                continue; // Skip non-named types (shouldn't happen in practice)
            }

            $typeName = $type->getName();

            // Skip 'null' type as we already handled it
            if ($typeName === 'null') {
                continue;
            }

            // If value matches this type, union is satisfied
            if (self::validateType($value, $typeName, false)) {
                return true;
            }
        }

        // Value didn't match any type in the union
        return false;
    }

    /**
     * Check if parameters include named parameters.
     *
     * @param array<int|string, mixed> $params
     * @return bool
     */
    private static function hasNamedParameters(array $params): bool
    {
        if (empty($params)) {
            return false;
        }

        // Named parameters have string keys
        // Positional parameters have sequential integer keys [0, 1, 2, ...]
        return array_keys($params) !== range(0, count($params) - 1);
    }

    /**
     * Map named parameters to positional parameters based on constructor signature.
     *
     * Supports three scenarios:
     * 1. Pure named parameters: make(value: 'test')
     * 2. Pure positional parameters: make('test')
     * 3. Mixed parameters: make(1, name: 'test', description: 'desc')
     *
     * @param ReflectionMethod $constructor
     * @param array<int|string, mixed> $params
     * @return array<int, mixed>
     * @throws InvalidArgumentException When required named parameter is missing
     */
    private static function mapNamedToPositional(
        ReflectionMethod $constructor,
        array $params
    ): array {
        $positional = [];
        $constructorParams = $constructor->getParameters();

        foreach ($constructorParams as $index => $param) {
            $name = $param->getName();

            // Check if parameter was provided positionally (by index)
            if (array_key_exists($index, $params)) {
                $positional[$index] = $params[$index];
            }
            // Check if parameter was provided by name
            elseif (array_key_exists($name, $params)) {
                $positional[$index] = $params[$name];
            }
            // Check if parameter has a default value
            elseif ($param->isDefaultValueAvailable()) {
                $positional[$index] = $param->getDefaultValue();
            }
            // Check if parameter is required
            elseif (!$param->isOptional()) {
                throw new InvalidArgumentException(
                    sprintf(
                        '%s::make() missing required parameter: %s',
                        basename(str_replace('\\', '/', static::class)),
                        $name
                    )
                );
            }
            // else: optional parameter without default (e.g., nullable), will be handled by PHP
        }

        return $positional;
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
