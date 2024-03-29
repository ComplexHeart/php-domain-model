<?php

declare(strict_types=1);

namespace ComplexHeart\Domain\Model\ValueObjects;

use ComplexHeart\Domain\Model\Exceptions\InvariantViolation;

/**
 * Class IntegerValue
 *
 * @method int value()
 *
 * @author Unay Santisteban <usantisteban@othercode.io>
 * @package ComplexHeart\Domain\Model\ValueObjects
 */
abstract class IntegerValue extends Value
{

    /**
     * Define the max value of the integer.
     *
     * @var int
     */
    protected int $_maxValue = PHP_INT_MAX;

    /**
     * Define the min value of the integer.
     *
     * @var int
     */
    protected int $_minValue = PHP_INT_MIN;

    /**
     * The value storage.
     *
     * @var int
     */
    protected int $value;

    /**
     * IntegerValue constructor.
     *
     * @param  int  $value
     */
    public function __construct(int $value)
    {
        $this->initialize(['value' => $value]);
    }

    /**
     * Check if the value meets a minimum length.
     *
     * @return bool
     * @throws InvariantViolation
     */
    protected function invariantValueMinLengthMustBeValid(): bool
    {
        if ($this->value < $this->_minValue) {
            throw new InvariantViolation(
                "Min value {$this->_minValue} is required, given {$this->value}"
            );
        }

        return true;
    }

    /**
     * Check if the value meets a maximum length.
     *
     * @return bool
     * @throws InvariantViolation
     */
    protected function invariantValueMaxLengthMustBeValid(): bool
    {
        if ($this->value > $this->_maxValue) {
            throw new InvariantViolation(
                "Max length {$this->_maxValue} exceeded, given {$this->value}"
            );
        }

        return true;
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
