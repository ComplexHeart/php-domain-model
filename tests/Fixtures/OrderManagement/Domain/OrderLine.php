<?php

declare(strict_types=1);

namespace ComplexHeart\Domain\Model\Test\Fixtures\OrderManagement\Domain;

use ComplexHeart\Domain\Contracts\Model\ValueObject;
use ComplexHeart\Domain\Model\IsValueObject;

/**
 * Class OrderLine
 *
 * @author Unay Santisteban <usantisteban@othercode.io>
 * @package ComplexHeart\Domain\Model\Test\OrderManagement\Models
 */
final class OrderLine implements ValueObject
{
    use IsValueObject;

    public string $concept;

    public int $quantity;

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
