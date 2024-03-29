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
 * @see https://martinfowler.com/eaaCatalog/valueObject.html
 * @see https://martinfowler.com/bliki/ValueObject.html
 * @see https://martinfowler.com/bliki/EvansClassification.html
 *
 * @author Unay Santisteban <usantisteban@othercode.io>
 * @package ComplexHeart\Domain\Model\Traits
 */
trait IsValueObject
{
    use IsModel;
    use HasEquality;
    use HasImmutability;

    /**
     * Represents the object as String.
     *
     * @return string
     */
    abstract public function __toString(): string;
}
