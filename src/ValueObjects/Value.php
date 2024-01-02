<?php

declare(strict_types=1);

namespace ComplexHeart\Domain\Model\ValueObjects;

use ComplexHeart\Domain\Contracts\Model\ValueObject;
use ComplexHeart\Domain\Model\IsValueObject;

/**
 * Class Value
 *
 * @author Unay Santisteban <usantisteban@othercode.io>
 * @package ComplexHeart\Domain\Model\ValueObjects
 */
abstract class Value implements ValueObject
{
    use IsValueObject;
}
