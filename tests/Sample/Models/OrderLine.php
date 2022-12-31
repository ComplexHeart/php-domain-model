<?php

declare(strict_types=1);

namespace ComplexHeart\Domain\Model\Test\Sample\Models;


use ComplexHeart\Contracts\Domain\Model\ValueObject;
use ComplexHeart\Domain\Model\Traits\IsValueObject;

/**
 * Class OrderLine
 *
 * @author Unay Santisteban <usantisteban@othercode.es>
 * @package ComplexHeart\Domain\Model\Test\Sample\Models
 */
final class OrderLine implements ValueObject
{
    use IsValueObject;

    public readonly string $concept; // @phpstan-ignore-line

    public readonly int $quantity; // @phpstan-ignore-line

    public function __construct(string $concept, int $quantity)
    {
        $this->initialize([
            'concept' => $concept,
            'quantity' => $quantity
        ]);
    }

    public function __toString(): string
    {
        return "$this->concept x$this->quantity";
    }
}