<?php

declare(strict_types=1);

namespace ComplexHeart\Domain\Model\ValueObjects;

use ComplexHeart\Contracts\Domain\Model\Identifier;
use Exception;
use Ramsey\Uuid\Uuid;

/**
 * Class UUIDValue
 *
 * @author Unay Santisteban <usantisteban@othercode.es>
 * @package ComplexHeart\Domain\Model\ValueObjects
 */
class UUIDValue extends Value implements Identifier
{
    private const RANDOM = 'random';

    /**
     * The uuid string value.
     *
     * @var string
     */
    protected string $value;

    /**
     * UUIDValue constructor.
     *
     * @param  string  $value
     */
    final public function __construct(string $value)
    {
        $this->initialize(
            ['value' => ($value === self::RANDOM) ? Uuid::uuid4()->toString() : $value]
        );
    }

    /**
     * Check if the value is a valid uuid.
     *
     * @return bool
     */
    protected function invariantMustBeValidUniversallyUniqueIdentifier(): bool
    {
        return Uuid::isValid($this->value());
    }

    /**
     * Return the value.
     *
     * @return string
     */
    public function value(): string
    {
        return $this->get('value');
    }

    /**
     * Generate a random UUIDValue.
     *
     * @return static
     * @throws Exception
     */
    public static function random(): self
    {
        return new static(self::RANDOM);
    }

    /**
     * Check if the given identifier is the same than the current one.
     *
     * @param  Identifier  $other
     *
     * @return bool
     */
    public function is(Identifier $other): bool
    {
        return $this->equals($other);
    }

    /**
     * To string method.
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->value();
    }
}
