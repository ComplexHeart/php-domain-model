<?php

declare(strict_types=1);

namespace ComplexHeart\Domain\Model\Exceptions\Traits;

use ComplexHeart\Domain\Model\Exceptions\Contracts\Aggregatable;
use Exception;
use Throwable;

use function Lambdish\Phunctional\map;

/**
 * Trait CanAggregateErrors
 *
 * Provides the implementation for exceptions that can aggregate multiple errors.
 *
 * This trait implements the complete logic for:
 * - Creating exceptions from multiple error messages
 * - Storing error messages
 * - Formatting aggregated messages
 * - Providing access to individual errors
 *
 * Use this trait along with AggregatesErrors interface to create
 * exceptions that can hold multiple error messages.
 *
 * @author Unay Santisteban <usantisteban@othercode.io>
 */
trait CanAggregateErrors
{
    /**
     * @var array<int, Throwable&Aggregatable>
     */
    private array $errors = [];

    /**
     * Create an exception from one or more error messages.
     *
     * @param array<int, Throwable&Aggregatable> $errors
     * @param int $code
     * @param Exception|null $previous
     * @return static
     */
    public static function fromErrors(array $errors, int $code = 0, ?Exception $previous = null): static
    {
        $messages = map(fn (Throwable $e): string => $e->getMessage(), $errors);

        $count = count($messages);

        // Format message based on count
        if ($count === 1) {
            $message = $messages[0];
        } else {
            $message = sprintf("Multiple errors (%d):\n- %s", $count, implode("\n- ", $messages));
        }

        // @phpstan-ignore-next-line (Safe usage - trait designed for Exception classes with standard constructor)
        $exception = new static($message, $code, $previous);
        $exception->errors = $errors;
        return $exception;
    }

    /**
     * Check if this exception has multiple errors.
     *
     * @return bool
     */
    public function hasMultipleErrors(): bool
    {
        return count($this->errors) > 1;
    }

    /**
     * Get all error messages.
     *
     * @return array<int, Throwable&Aggregatable>
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Get the count of errors.
     *
     * @return int
     */
    public function getErrorCount(): int
    {
        return count($this->errors);
    }
}
