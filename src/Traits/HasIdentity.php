<?php

declare(strict_types=1);

namespace ComplexHeart\Domain\Model\Traits;

use ComplexHeart\Contracts\Domain\Model\Identifier;

/**
 * Trait HasIdentity
 *
 * @author Unay Santisteban <usantisteban@othercode.io>
 * @package ComplexHeart\Domain\Model\Traits
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
