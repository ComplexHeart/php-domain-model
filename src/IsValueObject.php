<?php

declare(strict_types=1);

namespace ComplexHeart\Domain\Model;

use ComplexHeart\Domain\Model\Traits\HasEquality;
use ComplexHeart\Domain\Model\Traits\HasImmutability;

/**
 * Trait IsValueObject
 *
 * > A small simple object, like money or a date range, whose equality isn't based on identity.
 * > -- Martin Fowler
 *
 * Value Objects have automatic invariant checking enabled by default when using the make() factory method.
 * For direct constructor usage, you must manually call $this->check() at the end of your constructor.
 *
 * @see https://martinfowler.com/eaaCatalog/valueObject.html
 * @see https://martinfowler.com/bliki/ValueObject.html
 * @see https://martinfowler.com/bliki/EvansClassification.html
 *
 * @author Unay Santisteban <usantisteban@othercode.io>
 */
trait IsValueObject
{
    use IsModel;
    use HasEquality;
    use HasImmutability;

    /**
     * Value Objects have automatic invariant checking enabled by default.
     *
     * @return bool
     */
    protected function shouldAutoCheckInvariants(): bool
    {
        return true;
    }

    /**
     * Represents the object as String.
     *
     * @return string
     */
    abstract public function __toString(): string;
}
