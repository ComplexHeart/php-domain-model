<?php

declare(strict_types=1);

namespace ComplexHeart\Domain\Model\Exceptions\Contracts;

use Exception;
use Throwable;

/**
 * Interface AggregatesErrors
 *
 * Marks exceptions/errors that can hold and aggregate multiple error messages.
 *
 * Exceptions/Errors implementing this interface must provide a static factory method
 * to create instances from an array of error messages. This allows the
 * invariant system to aggregate multiple errors into a single exception.
 *
 * @author Unay Santisteban <usantisteban@othercode.io>
 */
interface AggregatesErrors
{
    /**
     * Create an exception instance from one or more error messages.
     *
     * @param array<int, Throwable&Aggregatable> $errors
     * @param int $code
     * @param Exception|null $previous
     * @return static
     */
    public static function fromErrors(array $errors, int $code = 0, ?Exception $previous = null): static;
}
