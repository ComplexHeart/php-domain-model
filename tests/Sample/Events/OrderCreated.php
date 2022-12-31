<?php

declare(strict_types=1);

namespace ComplexHeart\Domain\Model\Test\Sample\Events;

use ComplexHeart\Contracts\Domain\Model\Aggregate;
use ComplexHeart\Contracts\Domain\ServiceBus\Event;
use ComplexHeart\Domain\Model\Traits\HasAttributes;
use ComplexHeart\Domain\Model\ValueObjects\UUIDValue as ID;
use ComplexHeart\Domain\Model\ValueObjects\DateTimeValue as Timestamp;

/**
 * Class OrderCreated
 *
 * @author Unay Santisteban <usantisteban@othercode.es>
 * @package ComplexHeart\Domain\Model\Test\Sample\Events
 */
class OrderCreated implements Event
{
    use HasAttributes;

    private readonly ID $id;  // @phpstan-ignore-line

    private readonly string $name; // @phpstan-ignore-line

    private readonly Timestamp $timestamp;  // @phpstan-ignore-line

    private readonly Aggregate $payload;  // @phpstan-ignore-line

    public function __construct(Aggregate $aggregate, ?ID $id = null, ?Timestamp $timestamp = null)
    {
        $this->hydrate([
            'id' => is_null($id) ? ID::random() : $id,
            'name' => 'order.created',
            'payload' => $aggregate,
            'timestamp' => is_null($timestamp) ? new Timestamp() : $timestamp,
        ]);
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
        return $this->payload->values();
    }

    public function aggregateId(): string
    {
        return $this->payload->id()->value();
    }
}