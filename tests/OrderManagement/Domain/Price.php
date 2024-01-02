<?php

declare(strict_types=1);

namespace ComplexHeart\Domain\Model\Test\OrderManagement\Domain;

use ComplexHeart\Domain\Contracts\Model\ValueObject;
use ComplexHeart\Domain\Model\IsValueObject;
use ComplexHeart\Domain\Model\Test\OrderManagement\Domain\Errors\InvalidPriceError;

/**
 * Class Price
 *
 * @property float $amount
 * @property string $currency
 *
 * @author Unay Santisteban <usantisteban@othercode.es>
 * @package ComplexHeart\Domain\Model\Test\OrderManagement\Models
 */
final class Price implements ValueObject
{
    use IsValueObject;

    public function __construct(
        private float $amount,
        private string $currency
    ) {
        $this->check();
    }

    protected function invariantHandler(array $violations): void
    {
        throw new InvalidPriceError("Invalid Price values: ".implode(",", $violations));
    }

    protected function invariantAmountMustBeGreaterThanZero(): bool
    {
        return $this->amount >= 0;
    }

    protected function invariantCurrencyMustBeHaveThreeCharacters(): bool
    {
        return strlen($this->currency) == 3;
    }

    public function applyDiscount(float $discount): self
    {
        return $this->withOverrides([
            'amount' => $this->amount - ($this->amount * ($discount / 100))
        ]);
    }

    public function __toString(): string
    {
        return "$this->amount $this->currency";
    }
}