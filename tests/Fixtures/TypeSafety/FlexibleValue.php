<?php

declare(strict_types=1);

namespace ComplexHeart\Domain\Model\Test\Fixtures\TypeSafety;

use ComplexHeart\Domain\Contracts\Model\ValueObject;
use ComplexHeart\Domain\Model\IsValueObject;

/**
 * Test fixture for complex union type scenarios
 */
final class FlexibleValue implements ValueObject
{
    use IsValueObject;

    public function __construct(
        private readonly int|float|string $value,
        private readonly string|null $label = null
    ) {
        // Auto-check will happen via make()
    }

    public function __toString(): string
    {
        return $this->label ?? (string) $this->value;
    }
}
