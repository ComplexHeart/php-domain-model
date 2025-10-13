<?php

declare(strict_types=1);

namespace ComplexHeart\Domain\Model;

use ComplexHeart\Domain\Model\Traits\HasDomainEvents;

/**
 * Trait IsAggregate
 *
 * > Aggregate is a cluster of domain objects that can be treated as a single unit.
 * > -- Martin Fowler
 *
 * @see https://martinfowler.com/bliki/DDD_Aggregate.html
 * @see https://martinfowler.com/bliki/EvansClassification.html
 *
 * @author Unay Santisteban <usantisteban@othercode.io>
 */
trait IsAggregate
{
    use HasDomainEvents;
    use IsEntity;

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
