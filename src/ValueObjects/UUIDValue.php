<?php

declare(strict_types=1);

namespace ComplexHeart\Domain\Model\ValueObjects;

use ComplexHeart\Domain\Contracts\Model\Identifier;
use Exception;
use Ramsey\Uuid\Codec\TimestampFirstCombCodec;
use Ramsey\Uuid\Generator\CombGenerator;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidFactory;

/**
 * Class UUIDValue
 *
 * @author Unay Santisteban <usantisteban@othercode.io>
 * @package ComplexHeart\Domain\Model\ValueObjects
 */
class UUIDValue extends Value implements Identifier
{
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
        $this->initialize(['value' => $value]);
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
    public static function random(bool $ordered = true): self
    {
        if ($ordered) {
            $factory = new UuidFactory();

            $factory->setRandomGenerator(new CombGenerator(
                $factory->getRandomGenerator(),
                $factory->getNumberConverter()
            ));

            $factory->setCodec(new TimestampFirstCombCodec(
                $factory->getUuidBuilder()
            ));

            $uuid = $factory->uuid4();
        } else {
            $uuid = Uuid::uuid4();
        }

        return new static($uuid->toString());
    }

    /**
     * Check if the given identifier is the same as the current one.
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
