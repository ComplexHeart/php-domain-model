<?php

declare(strict_types=1);

namespace ComplexHeart\Domain\Model\Traits;

/**
 * Trait HasEquality
 *
 * @author Unay Santisteban <usantisteban@othercode.es>
 * @package ComplexHeart\Domain\Model\Traits
 */
trait HasEquality
{
    /**
     * Compare $this object with $other object. If the class is not
     * the same directly return false, compare value equality hash
     * otherwise.
     *
     * @param  object  $other
     *
     * @return bool
     */
    public function equals(object $other): bool
    {
        if (!($other instanceof static)) {
            return false;
        }

        return $this->hash() === $other->hash();
    }

    /**
     * Computes the equality hash, by default it just compare
     * the string representation of the object.
     *
     * @return string
     */
    protected function hash(): string
    {
        return hash('sha256', $this->__toString());
    }

    /**
     * Handle how the object is converted to string.
     *
     * @return string
     */
    abstract function __toString();
}
