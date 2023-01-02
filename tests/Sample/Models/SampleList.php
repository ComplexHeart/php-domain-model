<?php

declare(strict_types=1);

namespace ComplexHeart\Domain\Model\Test\Sample\Models;

use ComplexHeart\Domain\Model\ValueObjects\ArrayValue;

final class SampleList extends ArrayValue
{
    protected int $_minItems = 1;

    protected int $_maxItems = 10;

    protected string $_valueType = 'string';
}