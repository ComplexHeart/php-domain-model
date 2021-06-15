<?php

declare(strict_types=1);

namespace ComplexHeart\DomainModel;

use  Illuminate\Support\Collection;
use ComplexHeart\DomainModel\Exceptions\InvariantViolation;
use ComplexHeart\DomainModel\Traits\HasInvariants;

/**
 * Class TypedCollection
 *
 * @author Unay Santisteban <usantisteban@othercode.es>
 * @package ComplexHeart\DomainModel\Domain
 */
class TypedCollection extends Collection
{
    use HasInvariants;

    /**
     * The type of each key in the collection.
     *
     * @var string
     */
    protected string $keyType = 'mixed';

    /**
     * The type of each item in the collection.
     *
     * @var string
     */
    protected string $valueType = 'mixed';

    /**
     * TypedCollection constructor.
     *
     * @param  array  $items
     */
    public function __construct(array $items = [])
    {
        parent::__construct($items);
        $this->check();
    }

    /**
     * Invariant: All items must be of the same type.
     *
     * - If $typeOf is primitive check the type with gettype().
     * - If $typeOf is a class, check if the item is an instance of it.
     *
     * @return bool
     * @throws InvariantViolation
     */
    protected function invariantItemsMustMatchTheRequiredType(): bool
    {
        if ($this->valueType !== 'mixed') {
            $primitives = ['integer', 'boolean', 'float', 'string', 'array', 'object', 'callable'];
            $check = in_array($this->valueType, $primitives)
                ? fn($value): bool => gettype($value) !== $this->valueType
                : fn($value): bool => !($value instanceof $this->valueType);

            foreach ($this->items as $item) {
                if ($check($item)) {
                    throw new InvariantViolation("All items must be type of {$this->valueType}");
                }
            }
        }

        return true;
    }

    /**
     * Invariant: Check the collection keys to match the required type.
     *
     * Supported types:
     *  - string
     *  - integer
     *
     * @return bool
     * @throws InvariantViolation
     */
    protected function invariantKeysMustMatchTheRequiredType(): bool
    {
        if ($this->keyType !== 'mixed') {
            $supported = ['string', 'integer'];
            if (!in_array($this->keyType, $supported)) {
                throw new InvariantViolation(
                    "Unsupported key type, must be one of ".implode(', ', $supported)
                );
            }

            foreach ($this->items as $index => $item) {
                if (gettype($index) !== $this->keyType) {
                    throw new InvariantViolation("All keys must be type of {$this->keyType}");
                }
            }
        }
        return true;
    }
}
