<?php

declare(strict_types=1);

namespace ComplexHeart\Domain\Model\Traits;

use ComplexHeart\Domain\Contracts\ServiceBus\Event;
use ComplexHeart\Domain\Contracts\ServiceBus\EventBus;

/**
 * Trait HasDomainEvents
 *
 * @see https://martinfowler.com/eaaDev/DomainEvent.html
 *
 * @author Unay Santisteban <usantisteban@othercode.io>
 * @package ComplexHeart\Domain\Model\Traits
 */
trait HasDomainEvents
{
    /**
     * List of registered domain events.
     *
     * @var Event[]
     */
    private array $_domainEvents = [];

    /**
     * Publish the registered Domain Events.
     *
     * @param  EventBus  $eventBus
     * @return void
     */
    final public function publishDomainEvents(EventBus $eventBus): void
    {
        $eventBus->publish(...$this->_domainEvents);
        $this->_domainEvents = [];
    }

    /**
     * Register a new DomainEvent.
     *
     * @param  Event  $domainEvent
     */
    final protected function registerDomainEvent(Event $domainEvent): void
    {
        $this->_domainEvents[] = $domainEvent;
    }
}
