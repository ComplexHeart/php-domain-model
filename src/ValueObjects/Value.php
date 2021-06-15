<?php

declare(strict_types=1);

namespace ComplexHeart\DomainModel\ValueObjects;

use ComplexHeart\Contracts\Domain\Model\ValueObject;
use ComplexHeart\DomainModel\Traits\IsValueObject;

/**
 * Class Value
 *
 * @author Unay Santisteban <usantisteban@othercode.es>
 * @package ComplexHeart\DomainModel\ValueObjects
 */
abstract class Value implements ValueObject
{
    use IsValueObject;
}