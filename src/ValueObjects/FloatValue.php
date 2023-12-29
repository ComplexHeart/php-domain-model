<?php

declare(strict_types=1);

namespace ComplexHeart\Domain\Model\ValueObjects;

/**
 * Class FloatValue
 *
 * @method float value()
 *
 * @author Unay Santisteban <usantisteban@othercode.io>
 * @package ComplexHeart\Domain\Model\ValueObjects
 */
abstract class FloatValue extends Value
{
    /**
     * The value storage.
     *
     * @var float
     */
    protected float $value;

    /**
     * FloatValue constructor.
     *
     * @param  float  $value
     */
    public function __construct(float $value)
    {
        $this->initialize(['value' => $value]);
    }

    /**
     * To string value.
     *
     * @return string
     */
    public function __toString(): string
    {
        return (string)$this->value();
    }
}
