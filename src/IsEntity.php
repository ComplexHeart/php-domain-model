<?php

declare(strict_types=1);

namespace ComplexHeart\Domain\Model;

use ComplexHeart\Domain\Model\Traits\HasEquality;
use ComplexHeart\Domain\Model\Traits\HasIdentity;

/**
 * Trait IsEntity
 *
 * > Objects that have a distinct identity that runs through time and different representations.
 * > -- Martin Fowler
 *
 * @see https://martinfowler.com/bliki/EvansClassification.html
 *
 * @author Unay Santisteban <usantisteban@othercode.io>
 */
trait IsEntity
{
    use IsModel;
    use HasIdentity;
    use HasEquality {
        HasIdentity::hash insteadof HasEquality;
    }

    /**
     * Entities have automatic invariant checking enabled by default.
     *
     * @return bool
     */
    protected function shouldAutoCheckInvariants(): bool
    {
        return true;
    }
}
