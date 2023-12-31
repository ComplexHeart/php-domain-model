<?php

declare(strict_types=1);

namespace ComplexHeart\Domain\Model\Test\OrderManagement\Domain;

use ComplexHeart\Contracts\Domain\Model\Entity;
use ComplexHeart\Contracts\Domain\Model\Identifier;
use ComplexHeart\Domain\Model\IsEntity;
use ComplexHeart\Domain\Model\ValueObjects\UUIDValue;

/**
 * Class Customer
 *
 * @author Unay Santisteban <usantisteban@othercode.io>
 * @package ComplexHeart\Domain\Model\Test\OrderManagement\Domain
 */
final class Customer implements Entity
{
    use IsEntity;

    public function __construct(
        public readonly UUIDValue $id,
        public string $name,
    ) {
        $this->check();
    }

    public function id(): Identifier
    {
        return $this->id;
    }

    public function __toString(): string
    {
        return "$this->name ($this->id)";
    }
}
