<?php

declare(strict_types=1);

namespace ComplexHeart\Domain\Model;

use ComplexHeart\Domain\Model\Exceptions\InvariantViolation;
use ComplexHeart\Domain\Model\Traits\HasTypeCheck;
use ComplexHeart\Domain\Model\Traits\HasInvariants;
use Illuminate\Support\Collection;

/**
 * Class TypedCollection
 *
 * @author Unay Santisteban <usantisteban@othercode.io>
 * @package ComplexHeart\Domain\Model\Domain
 *
 * @template TKey of array-key
 * @template-covariant TValue
 * @extends Collection<TKey, TValue>
 */
class TypedCollection extends Collection
{
    use HasInvariants;
    use HasTypeCheck;

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
     * @param  array<TKey, TValue>  $items
     */
    public function __construct(array $items = [])
    {
        parent::__construct($items);
        $this->check();
    }

    /**
     * Assert that the key type is compliant with the collection definition.
     *
     * @param  mixed  $key
     *
     * @throws InvariantViolation
     */
    protected function checkKeyType(mixed $key): void
    {
        $supported = ['string', 'integer'];
        if (!in_array($this->keyType, $supported)) {
            throw new InvariantViolation(
                "Unsupported key type $this->keyType, must be one of ".implode(', ', $supported)
            );
        }

        if ($this->isValueTypeNotValid($key, $this->keyType)) {
            throw new InvariantViolation("All keys in the collection must be type of $this->keyType");
        }
    }

    /**
     * Assert that the item type is compliant with the collection definition.
     *
     * @param  mixed  $item
     *
     * @throws InvariantViolation
     */
    protected function checkValueType(mixed $item): void
    {
        if ($this->isValueTypeNotValid($item, $this->valueType)) {
            throw new InvariantViolation("All items in the collection must be type of $this->valueType");
        }
    }

    /**
     * Check the keys and values of the collection to match the required type.
     *
     * Supported types for keys:
     *  - string
     *  - integer
     *
     * Values can have any type:
     * - If $type is primitive check the type with gettype().
     * - If $type is a class, check if the item is an instance of it.
     *
     * @return bool
     * @throws InvariantViolation
     */
    protected function invariantKeysAndValuesMustMatchTheRequiredType(): bool
    {
        if ($this->keyType === 'mixed' && $this->valueType === 'mixed') {
            return true;
        }

        foreach ($this->items as $key => $item) {
            if ($this->keyType !== 'mixed') {
                $this->checkKeyType($key);
            }

            if ($this->valueType !== 'mixed') {
                $this->checkValueType($item);
            }
        }

        return true;
    }

    /**
     * Push one or more items onto the end of the collection.
     *
     * @param  mixed  $values  [optional]
     *
     * @return static
     * @throws InvariantViolation
     */
    public function push(...$values): static
    {
        foreach ($values as $value) {
            $this->checkValueType($value);
        }

        return parent::push(...$values);
    }

    /**
     * Offset to set.
     *
     * @param  mixed  $key
     * @param  mixed  $value
     *
     * @throws InvariantViolation
     */
    public function offsetSet($key, $value): void
    {
        if ($this->keyType !== 'mixed') {
            $this->checkKeyType($key);
        }

        $this->checkValueType($value);

        parent::offsetSet($key, $value);
    }

    /**
     * Push an item onto the beginning of the collection.
     *
     * @param  mixed  $value
     * @param  int|string  $key
     *
     * @return static
     * @throws InvariantViolation
     */
    public function prepend($value, $key = null): static
    {
        if ($this->keyType !== 'mixed') {
            $this->checkKeyType($key);
        }

        $this->checkValueType($value);

        return parent::prepend($value, $key);
    }

    /**
     * Add an item to the collection.
     *
     * @param  mixed  $item
     *
     * @return static
     * @throws InvariantViolation
     */
    public function add(mixed $item): static
    {
        $this->checkValueType($item);

        return parent::add($item);
    }

    /**
     * Get the values of a given key.
     *
     * @param  string|int|array<array-key, string>  $value
     * @param  string|null  $key
     * @return Collection<TKey, TValue>
     */
    public function pluck($value, $key = null): Collection
    {
        return $this->toBase()->pluck($value, $key);
    }

    /**
     * Get the keys of the collection items.
     *
     * @return Collection<TKey, TValue>
     */
    public function keys(): Collection
    {
        return $this->toBase()->keys();
    }
}
