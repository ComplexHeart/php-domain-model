<?php

declare(strict_types=1);

use ComplexHeart\Domain\Model\Errors\ImmutabilityError;
use ComplexHeart\Domain\Model\Exceptions\InvariantViolation;
use ComplexHeart\Domain\Model\Test\Fixtures\OrderManagement\Domain\Errors\InvalidPriceError;
use ComplexHeart\Domain\Model\Test\Fixtures\OrderManagement\Domain\Price;
use ComplexHeart\Domain\Model\Traits\HasInvariants;

test('Object with HasImmutability should throw ImmutabilityError for any update properties attempts.', function () {
    $price = new Price(100.0, 'EUR');
    $price->amount = 0.0;
})
    ->group('Unit')
    ->throws(ImmutabilityError::class);

test('Object with HasImmutability should expose primitive values.', function () {
    $price = new Price(100.0, 'EUR');
    expect($price->amount)->toBeFloat();
    expect($price->currency)->toBeString();
})
    ->group('Unit');

test('Object with HasImmutability should return new instance with override values.', function () {
    $price = new Price(100.0, 'EUR');
    $newPrice = $price->applyDiscount(10.0);

    expect($newPrice)->toBeInstanceOf(Price::class);
    expect($newPrice->amount)->toBe(90.0);
})
    ->group('Unit');

test('Object with HasInvariants should support custom invariant handler.', function () {
    new Price(-10.0, 'EURO');
})
    ->group('Unit')
    ->throws(InvalidPriceError::class);

test('Object with HasInvariants should execute custom invariant handler as closure.', function () {
    new class () {
        use HasInvariants;

        public function __construct()
        {
            $this->check(fn (array $violations) => throw new ValueError('From custom Handler'));
        }

        protected function invariantAlwaysFail(): bool
        {
            return false;
        }
    };
})
    ->group('Unit')
    ->throws(ValueError::class);

test('Object with HasInvariants should throw exception with list of exceptions', function () {
    new class () {
        use HasInvariants;

        public function __construct()
        {
            $this->check();
        }

        protected function invariantAlwaysFailOne(): bool
        {
            return false;
        }

        protected function invariantAlwaysFailTwo(): bool
        {
            return false;
        }
    };
})
    ->group('Unit')
    ->throws(InvariantViolation::class, 'always fail one, always fail two');
