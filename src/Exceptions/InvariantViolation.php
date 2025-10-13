<?php

declare(strict_types=1);

namespace ComplexHeart\Domain\Model\Exceptions;

use ComplexHeart\Domain\Model\Contracts\Aggregatable;
use Exception;

/**
 * Class InvariantViolation
 *
 * @author Unay Santisteban <usantisteban@othercode.io>
 * @package ComplexHeart\Domain\Model\Exceptions
 */
class InvariantViolation extends Exception implements Aggregatable
{
    /**
     * @var array<int, string> List of all violation messages
     */
    private array $violations = [];

    /**
     * Create an invariant violation exception from one or more violations.
     *
     * @param array<int, string> $violations
     * @param int $code
     * @param Exception|null $previous
     * @return self
     */
    public static function fromViolations(array $violations, int $code = 0, ?Exception $previous = null): self
    {
        $count = count($violations);

        // Format message based on count
        if ($count === 1) {
            $message = $violations[0];
        } else {
            $message = sprintf(
                "Multiple invariant violations (%d):\n- %s",
                $count,
                implode("\n- ", $violations)
            );
        }

        $exception = new self($message, $code, $previous);
        $exception->violations = $violations;
        return $exception;
    }

    /**
     * Check if this exception has multiple violations.
     *
     * @return bool
     */
    public function hasMultipleViolations(): bool
    {
        return count($this->violations) > 1;
    }

    /**
     * Get all violation messages.
     *
     * @return array<int, string>
     */
    public function getViolations(): array
    {
        return $this->violations;
    }

    /**
     * Get the count of violations.
     *
     * @return int
     */
    public function getViolationCount(): int
    {
        return count($this->violations);
    }
}
