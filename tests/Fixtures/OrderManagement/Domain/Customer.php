<?php

declare(strict_types=1);

namespace ComplexHeart\Domain\Model\Test\Fixtures\OrderManagement\Domain;

use ComplexHeart\Domain\Contracts\Model\Identifier;
use ComplexHeart\Domain\Contracts\Model\Entity;
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
