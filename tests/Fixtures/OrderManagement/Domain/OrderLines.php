<?php

declare(strict_types=1);

namespace ComplexHeart\Domain\Model\Test\Fixtures\OrderManagement\Domain;

use ComplexHeart\Domain\Model\TypedCollection;

/**
 * Class LineItems
 *
 * @author Unay Santisteban <usantisteban@othercode.io>
 * @package ComplexHeart\Domain\Model\Test\OrderManagement\Models
 */
final class OrderLines extends TypedCollection
{
    protected string $keyType = 'integer';

    protected string $valueType = OrderLine::class;
}
