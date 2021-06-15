<?php

declare(strict_types=1);

namespace ComplexHeart\DomainModel\ValueObjects;

use Carbon\CarbonImmutable;
use ComplexHeart\Contracts\Domain\Model\ValueObject;
use ComplexHeart\DomainModel\Traits\HasEquality;
use DateTimeZone;
use Exception;

/**
 * Class DateTimeValue
 *
 * @author Unay Santisteban <usantisteban@othercode.es>
 * @package ComplexHeart\DomainModel\ValueObjects
 */
class DateTimeValue extends CarbonImmutable implements ValueObject
{
    use HasEquality;

    /**
     * DateTimeValue constructor.
     *
     * @param  string|null  $time
     * @param  DateTimeZone|string|null  $tz
     *
     * @throws Exception
     */
    public function __construct($time = null, $tz = null)
    {
        parent::__construct($time, $tz);
        $this->settings(['toStringFormat' => 'c']);
    }

    /**
     * Return the value as string.
     *
     * @return string
     */
    protected function value(): string
    {
        return $this->toIso8601String();
    }

    /**
     * Return the attribute values.
     *
     * @return string[]
     */
    public function values(): array
    {
        return ['value' => $this->value()];
    }
}
