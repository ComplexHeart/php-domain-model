<?php

declare(strict_types=1);

namespace ComplexHeart\Domain\Model\Test\OrderManagement\Domain;

use ComplexHeart\Domain\Contracts\Model\Aggregate;
use ComplexHeart\Domain\Contracts\Model\Identifier;
use ComplexHeart\Domain\Model\Exceptions\InvariantViolation;
use ComplexHeart\Domain\Model\IsAggregate;
use ComplexHeart\Domain\Model\Test\OrderManagement\Domain\Events\OrderCreated;
use ComplexHeart\Domain\Model\ValueObjects\DateTimeValue as Timestamp;

/**
 * Class Order
 *
 * @author Unay Santisteban <usantisteban@othercode.io>
 * @package ComplexHeart\Domain\Model\Test\OrderManagement\Models
 */
final class Order implements Aggregate
{
    use IsAggregate;

    public function __construct(
        public Reference $reference,
        public Customer $customer,
        public OrderLines $lines,
        public Tags $tags,
        public Timestamp $created
    ) {
        $this->check();
    }

    public static function create(int $number, array $customer): Order
    {
        $created = Timestamp::now();
        $order = new Order(
            reference: Reference::fromTimestamp($created, $number),
            customer: new Customer(...$customer),
            lines: OrderLines::empty(),
            tags: new Tags(),
            created: $created
        );

        $order->registerDomainEvent(new OrderCreated($order));

        return $order;
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
    public function addOrderLine(OrderLine $line): self
    {
        $this->lines->add($line);

        return $this;
    }

    public function withName(string $name): self
    {
        $this->customer->name = $name;
        return $this;
    }

    public function customerName(): string
    {
        return $this->customer->name;
    }

    public function __toString(): string
    {
        return $this->reference->value();
    }
}