<?php

declare(strict_types=1);

namespace ComplexHeart\Domain\Model\Exceptions;

use ComplexHeart\Domain\Model\Exceptions\Contracts\Aggregatable;
use ComplexHeart\Domain\Model\Exceptions\Contracts\AggregatesErrors;
use ComplexHeart\Domain\Model\Exceptions\Traits\CanAggregateErrors;
use Exception;

/**
 * Class InvariantViolation
 *
 * @author Unay Santisteban <usantisteban@othercode.io>
 */
class InvariantViolation extends Exception implements Aggregatable, AggregatesErrors
{
    use CanAggregateErrors;
}
