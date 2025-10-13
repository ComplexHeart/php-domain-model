<?php

declare(strict_types=1);

namespace ComplexHeart\Domain\Model\Contracts;

/**
 * Interface Aggregatable
 *
 * Marker interface for exceptions that can be aggregated during invariant validation.
 *
 * Exceptions implementing this interface will be collected and aggregated when
 * multiple invariants fail. Exceptions NOT implementing this interface will be
 * thrown immediately, stopping invariant checking.
 *
 * @author Unay Santisteban <usantisteban@othercode.io>
 * @package ComplexHeart\Domain\Model\Contracts
 */
interface Aggregatable
{
}
