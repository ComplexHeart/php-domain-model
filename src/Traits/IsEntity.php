<?php

declare(strict_types=1);

namespace ComplexHeart\Domain\Model\Traits;

use function Lambdish\Phunctional\filter;

/**
 * Trait IsEntity
 *
 * > Objects that have a distinct identity that runs through time and different representations.
 * > -- Martin Fowler
 *
 * @see https://martinfowler.com/bliki/EvansClassification.html
 *
 * @author Unay Santisteban <usantisteban@othercode.es>
 * @package ComplexHeart\Domain\Model\Traits
 */
trait IsEntity
{
    use IsModel {
        withOverrides as private overrideAttributes;
    }
    use HasIdentity;
    use HasEquality {
        HasIdentity::hash insteadof HasEquality;
    }

    /**
     * Creates a new instance overriding the given attributes excluding the id,
     * as the Entities are identified by the ID if it changes we will have a
     * different entity.
     *
     * @param  array<string, mixed>  $overrides
     *
     * @return static
     */
    protected function withOverrides(array $overrides): static
    {
        return $this->overrideAttributes(
            filter(fn($value, string $key): bool => $key !== 'id', $overrides)
        );
    }
}
