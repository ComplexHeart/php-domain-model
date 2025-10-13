<?php

declare(strict_types=1);

namespace ComplexHeart\Domain\Model\Test\Fixtures\TypeSafety;

use ComplexHeart\Domain\Contracts\Model\ValueObject;
use ComplexHeart\Domain\Model\IsValueObject;

/**
 * Test fixture for value object with multiple parameters and union types
 */
final class Money implements ValueObject
{
    use IsValueObject;

    public function __construct(
        private readonly int|float $amount,
        private readonly string $currency
    ) {
        // Auto-check will happen via make()
    }

    protected function invariantPositiveAmount(): bool
    {
        return $this->amount > 0;
    }

    protected function invariantValidCurrency(): bool
    {
        return strlen($this->currency) === 3;
    }

    public function __toString(): string
    {
        return sprintf('%s %s', $this->amount, $this->currency);
    }
}
