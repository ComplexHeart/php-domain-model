<?php

declare(strict_types=1);

namespace ComplexHeart\Domain\Model\ValueObjects;

use ComplexHeart\Contracts\Domain\Model\ValueObject;
use ComplexHeart\Domain\Model\Traits\IsValueObject;

/**
 * Class Value
 *
 * @author Unay Santisteban <usantisteban@othercode.es>
 * @package ComplexHeart\Domain\Model\ValueObjects
 */
abstract class Value implements ValueObject
{
    use IsValueObject;
}