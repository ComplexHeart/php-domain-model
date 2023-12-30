<?php

declare(strict_types=1);

namespace ComplexHeart\Domain\Model\Test\OrderManagement\Domain;

use ComplexHeart\Domain\Model\ValueObjects\ArrayValue;

final class Tags extends ArrayValue
{
    protected int $_minItems = 0;

    protected int $_maxItems = 10;

    protected string $_valueType = 'string';
}