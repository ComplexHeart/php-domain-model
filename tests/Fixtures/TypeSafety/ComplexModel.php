<?php

declare(strict_types=1);

namespace ComplexHeart\Domain\Model\Test\Fixtures\TypeSafety;

use ComplexHeart\Domain\Model\IsModel;

/**
 * Test fixture for model with mixed types (nullable, arrays)
 */
final class ComplexModel
{
    use IsModel;

    public function __construct(
        private readonly int $id,
        private readonly string $name,
        private readonly ?string $description,
        private readonly array $tags,
    ) {
    }
}
