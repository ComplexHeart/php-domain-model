<?php

declare(strict_types=1);

namespace ComplexHeart\Domain\Model\Test\Sample\Models;

use ComplexHeart\Contracts\Domain\Model\Aggregate;
use ComplexHeart\Contracts\Domain\Model\Identifier;
use ComplexHeart\Domain\Model\Exceptions\InvariantViolation;
use ComplexHeart\Domain\Model\Test\Sample\Events\OrderCreated;
use ComplexHeart\Domain\Model\Traits\IsAggregate;
use ComplexHeart\Domain\Model\ValueObjects\DateTimeValue as Timestamp;

/**
 * Class Order
 *
 * @author Unay Santisteban <usantisteban@othercode.es>
 * @package ComplexHeart\Domain\Model\Test\Sample\Models
 */
final class Order implements Aggregate
{
    use IsAggregate;

    public readonly Reference $reference; // @phpstan-ignore-line

    public readonly string $name; // @phpstan-ignore-line

    public readonly OrderLines $lines; // @phpstan-ignore-line

    public readonly Timestamp $created; // @phpstan-ignore-line

    public function __construct(Reference $reference, string $name, OrderLines $lines, Timestamp $created)
    {
        $this->initialize([
            'reference' => $reference,
            'name' => $name,
            'lines' => $lines,
            'created' => $created
        ]);

        $this->registerDomainEvent(new OrderCreated($this));
    }

    public static function create(int $number, string $name): Order
    {
        $created = Timestamp::now();

        return new Order(
            reference: Reference::fromTimestamp($created, $number),
            name: $name,
            lines: OrderLines::empty(),
            created: $created
        );
    }

    public function id(): Identifier
    {
        return $this->reference;
    }

    /**
     * Adds a new OrderLine to the Order.
     *
     * @throws InvariantViolation
     */
    public function addOrderLine(OrderLine $line): Order
    {
        $this->lines->add($line);

        return $this;
    }

    public function withName(string $name): self
    {
        return $this->withOverrides(['name' => $name]);
    }

    public function __toString(): string
    {
        return $this->reference->value();
    }
}