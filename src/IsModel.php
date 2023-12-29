<?php

declare(strict_types=1);

namespace ComplexHeart\Domain\Model;

use ComplexHeart\Domain\Model\Exceptions\InstantiationException;
use ComplexHeart\Domain\Model\Traits\HasAttributes;
use ComplexHeart\Domain\Model\Traits\HasInvariants;
use Doctrine\Instantiator\Exception\ExceptionInterface;
use Doctrine\Instantiator\Instantiator;
use RuntimeException;

/**
 * Trait IsModel
 *
 * @author Unay Santisteban <usantisteban@othercode.io>
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
        // check if the array is indexed or associative.
        $isIndexed = fn($source): bool => ([] !== $source) && array_keys($source) === range(0, count($source) - 1);

        $source = $isIndexed($source)
            // combine the attributes keys with the provided source values.
            ? array_combine(array_slice(static::attributes(), 0, count($source)), $source)
            // return the already mapped array source.
            : $source;


        $this->hydrate($source);
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
}
