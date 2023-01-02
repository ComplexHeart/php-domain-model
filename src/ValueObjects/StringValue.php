<?php

declare(strict_types=1);

namespace ComplexHeart\Domain\Model\ValueObjects;

use InvalidArgumentException;
use ComplexHeart\Domain\Model\Exceptions\InvariantViolation;

/**
 * Class StringValue
 *
 * @author Unay Santisteban <usantisteban@othercode.es>
 * @package ComplexHeart\Domain\Model\ValueObjects
 */
abstract class StringValue extends Value
{
    /**
     * Check the min length or the string.
     *
     * @var int
     */
    protected int $_minLength = 0;

    /**
     * Check the max length of the string.
     *
     * @var int
     */
    protected int $_maxLength = 0;

    /**
     * The pattern to match for the given string.
     *
     * @var string
     */
    protected string $_pattern = '';

    /**
     * The value storage.
     *
     * @var string
     */
    protected string $value;

    /**
     * StringValue constructor.
     *
     * @param  string  $value
     */
    public function __construct(string $value)
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
        $length = strlen($this->value);
        if ($this->_minLength > 0 && $length < $this->_minLength) {
            throw new InvariantViolation(
                "Min length $this->_minLength is required, given {$length}"
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
        $length = strlen($this->value);
        if ($this->_maxLength > 0 && $length > $this->_maxLength) {
            throw new InvariantViolation(
                "Max length $this->_maxLength exceeded, given {$length}"
            );
        }

        return true;
    }

    /**
     * Check if the value match the given regex expresion.
     *
     * @return bool
     */
    protected function invariantValueMustMatchRegexPattern(): bool
    {
        if (!empty($this->_pattern)
            && preg_match($this->_pattern, $this->value) !== 1
        ) {
            throw new InvalidArgumentException(
                "Invalid value, $this->value does not match pattern {$this->_pattern}"
            );
        }

        return true;
    }

    /**
     * Returns the actual string value.
     *
     * @return string
     */
    public function value(): string
    {
        return $this->value;
    }

    /**
     * To string method... string is a string...
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->value();
    }
}
