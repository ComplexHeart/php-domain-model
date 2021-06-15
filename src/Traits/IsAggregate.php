<?php

declare(strict_types=1);

namespace ComplexHeart\Domain\Model\Traits;

/**
 * Trait IsAggregate
 *
 * > Aggregate is a cluster of domain objects that can be treated as a single unit.
 * > -- Martin Fowler
 *
 * @see https://martinfowler.com/bliki/DDD_Aggregate.html
 * @see https://martinfowler.com/bliki/EvansClassification.html
 *
 * @author Unay Santisteban <usantisteban@othercode.es>
 * @package ComplexHeart\Domain\Model\Traits
 */
trait IsAggregate
{
    use HasDomainEvents;
    use IsEntity {
        withOverrides as private overrideEntity;
    }

    /**
     * Creates a new instance overriding the given attributes and
     * propagating the domain events to the new instance.
     *
     * @param  array<string, mixed>  $overrides
     *
     * @return static
     */
    protected function withOverrides(array $overrides)
    {
        $new = $this->overrideEntity($overrides);

        foreach ($this->_domainEvents as $event) {
            $new->registerDomainEvent($event);
        }

        return $new;
    }

    /**
     * This method is called by var_dump() when dumping an object to
     * get the properties that should be shown.
     *
     * @return array<string, mixed>
     */
    public function __debugInfo(): array
    {
        return array_merge(
            $this->values(),
            ['domainEvents' => $this->_domainEvents]
        );
    }
}
