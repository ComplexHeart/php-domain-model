<?php

declare(strict_types=1);

namespace ComplexHeart\Domain\Model\Traits;

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
 * @author Unay Santisteban <usantisteban@othercode.es>
 * @package ComplexHeart\Domain\Model\Traits
 */
trait IsValueObject
{
    use IsModel;
    use HasEquality;

    /**
     * Represents the object as String.
     *
     * @return string
     */
    abstract public function __toString(): string;
}
