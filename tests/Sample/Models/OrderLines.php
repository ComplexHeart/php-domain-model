<?php

declare(strict_types=1);

namespace ComplexHeart\Domain\Model\Test\Sample\Models;

use ComplexHeart\Domain\Model\TypedCollection;

/**
 * Class LineItems
 *
 * @author Unay Santisteban <usantisteban@othercode.es>
 * @package ComplexHeart\Domain\Model\Test\Sample\Models
 */
final class OrderLines extends TypedCollection
{
    protected string $keyType = 'integer';

    protected string $valueType = OrderLine::class;
}