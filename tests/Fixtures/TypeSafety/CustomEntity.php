<?php

declare(strict_types=1);

namespace ComplexHeart\Domain\Model\Test\Fixtures\TypeSafety;

use ComplexHeart\Domain\Contracts\Model\Entity;
use ComplexHeart\Domain\Contracts\Model\Identifier;
use ComplexHeart\Domain\Model\IsEntity;
use ComplexHeart\Domain\Model\ValueObjects\UUIDValue;

/**
 * Test fixture for entity with auto-check disabled
 */
final class CustomEntity implements Entity
{
    use IsEntity;

    public function __construct(
        private readonly UUIDValue $entityId,
        private string $name
    ) {
        // No auto-check because disabled
    }

    protected function shouldAutoCheckInvariants(): bool
    {
        return false; // Disabled for testing
    }

    protected function invariantNameNotEmpty(): bool
    {
        return $this->name !== '';
    }

    public function id(): Identifier
    {
        return $this->entityId;
    }

    public function __toString(): string
    {
        return $this->name;
    }
}
