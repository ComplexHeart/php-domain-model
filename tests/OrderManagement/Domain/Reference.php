<?php

declare(strict_types=1);

namespace ComplexHeart\Domain\Model\Test\OrderManagement\Domain;

use ComplexHeart\Domain\Contracts\Model\Identifier;
use ComplexHeart\Domain\Model\Exceptions\InvariantViolation;
use ComplexHeart\Domain\Model\ValueObjects\DateTimeValue as Timestamp;
use ComplexHeart\Domain\Model\ValueObjects\StringValue;

/**
 * Class Reference
 *
 * @author Unay Santisteban <usantisteban@othercode.io>
 * @package ComplexHeart\Domain\Model\Test\OrderManagement\Models
 */
final class Reference extends StringValue implements Identifier
{
    protected int $_maxLength = 17;

    protected int $_minLength = 3;

    protected string $_pattern = '/F[0-9]{4}(\.[0-9]{2}){2}\-[0-9]{1,6}/';

    /**
     * @return bool
     * @throws InvariantViolation
     */
    protected function invariantMustStartWithFCharacter(): bool
    {
        if (!str_starts_with($this->value(), 'F')) {
            throw new InvariantViolation('Reference must start with F character.');
        }

        return true;
    }

    /**
     * Create a new Reference instance from the timestamp.
     *
     * @param  Timestamp  $timestamp
     * @param  int  $number
     * @return Reference
     */
    public static function fromTimestamp(Timestamp $timestamp, int $number): Reference
    {
        return new Reference(strtr('F{timestamp}-{number}', [
            '{timestamp}' => $timestamp->format('Y.m.d'),
            '{number}' => $number,
        ]));
    }

}
