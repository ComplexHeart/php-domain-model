<?php

declare(strict_types=1);

namespace ComplexHeart\Domain\Model\Test\OrderManagement\Domain\Events;

use ComplexHeart\Domain\Contracts\ServiceBus\Event;
use ComplexHeart\Domain\Model\Test\OrderManagement\Domain\OrderLine;
use ComplexHeart\Domain\Model\Test\OrderManagement\Domain\Order;
use ComplexHeart\Domain\Model\ValueObjects\DateTimeValue as Timestamp;
use ComplexHeart\Domain\Model\ValueObjects\UUIDValue as ID;

/**
 * Class OrderCreated
 *
 * @author Unay Santisteban <usantisteban@othercode.io>
 * @package ComplexHeart\Domain\Model\Test\OrderManagement\Events
 */
class OrderCreated implements Event
{
    private ID $id;

    private string $name = 'order_management.order.created';

    private array $payload;

    private Timestamp $timestamp;

    public function __construct(Order $order, ?ID $id = null, ?Timestamp $timestamp = null)
    {
        $this->id = is_null($id) ? ID::random() : $id;
        $this->payload = [
            'id' => $order->id()->value(),
            'customer' => [
                'id' => $order->customer->id,
                'name' => $order->customer->name,
            ],
            'orderLines' => $order->lines->map(fn(OrderLine $line) => $line->values()),
            'tags' => $order->tags->values()['value'],
            'created' => $order->created->toIso8601String(),
        ];
        $this->timestamp = is_null($timestamp) ? new Timestamp() : $timestamp;
    }

    public function eventId(): string
    {
        return $this->id->value();
    }

    public function eventName(): string
    {
        return $this->name;
    }

    public function occurredOn(): string
    {
        return (string) (((float) ($this->timestamp->getTimestamp().'.'.$this->timestamp->format('u'))) * 1000);
    }

    public function payload(): array
    {
        return $this->payload;
    }

    public function aggregateId(): string
    {
        return $this->payload['id'];
    }
}