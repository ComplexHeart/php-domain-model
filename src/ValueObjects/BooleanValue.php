<?php

declare(strict_types=1);

namespace ComplexHeart\DomainModel\ValueObjects;

/**
 * Class BooleanValue
 *
 * @method bool value()
 *
 * @author Unay Santisteban <usantisteban@othercode.es>
 * @package ComplexHeart\DomainModel\ValueObjects
 */
abstract class BooleanValue extends Value
{
    /**
     * The string representation of the boolean value.
     *
     * @var array<string, string>
     */
    protected array $_strings = [
        'true'  => 'true',
        'false' => 'false',
    ];

    /**
     * The value storage.
     *
     * @var bool
     */
    protected bool $value;

    /**
     * BoolValueObject constructor.
     *
     * @param  bool  $value
     */
    public function __construct(bool $value)
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
        return $this->value()
            ? $this->_strings['true']
            : $this->_strings['false'];
    }
}
