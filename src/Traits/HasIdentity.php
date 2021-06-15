<?php

declare(strict_types=1);

namespace ComplexHeart\DomainModel\Traits;



use ComplexHeart\Contracts\Domain\Model\Identifier;

/**
 * Trait HasIdentity
 *
 * @author Unay Santisteban <usantisteban@othercode.es>
 * @package ComplexHeart\DomainModel\Traits
 */
trait HasIdentity
{
    /**
     * Return the id instance.
     *
     * @return Identifier
     */
    abstract public function id(): Identifier;

    /**
     * Return the computed hash to evaluate if the given object
     * is the same as the other.
     *
     * @return string
     */
    protected function hash(): string
    {
        return $this->id()->value();
    }
}
