<?php

declare(strict_types=1);

namespace ComplexHeart\Domain\Model\Traits;

use ComplexHeart\Domain\Model\Exceptions\InstantiationException;
use RuntimeException;
use Doctrine\Instantiator\Instantiator;
use Doctrine\Instantiator\Exception\ExceptionInterface;

/**
 * Trait IsModel
 *
 * @author Unay Santisteban <usantisteban@othercode.es>
 * @package ComplexHeart\Domain\Model\Traits
 */
trait IsModel
{
    use HasAttributes;
    use HasInvariants;

    /**
     * Initialize the Model. Just as the constructor will do.
     *
     * @param  array<int|string, mixed>  $source
     * @param  callable|null  $onFail
     *
     * @return static
     */
    protected function initialize(array $source, callable $onFail = null): static
    {
        $this->hydrate($this->mapSource($source));
        $this->check($onFail);

        return $this;
    }

    /**
     * Restore the instance without calling __constructor of the model.
     *
     * @return static
     *
     * @throws RuntimeException
     */
    final public static function make(): static
    {
        try {
            return (new Instantiator())
                ->instantiate(static::class)
                ->initialize(func_get_args());
        } catch (ExceptionInterface $e) {
            throw new InstantiationException($e->getMessage(), $e->getCode(), $e);
        }
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

    /**
     * Map the given source with the actual attributes by position, if
     * the provided array is already mapped (assoc) return it directly.
     *
     * @param  array  $source
     *
     * @return array
     */
    final protected function mapSource(array $source): array
    {
        // check if the array is indexed or associative.
        $isIndexed = fn($source): bool => ([] !== $source) && array_keys($source) === range(0, count($source) - 1);

        return $isIndexed($source)
            // combine the attributes keys with the provided source values.
            ? array_combine(array_slice(static::attributes(), 0, count($source)), $source)
            // return the already mapped array source.
            : $source;
    }
}
