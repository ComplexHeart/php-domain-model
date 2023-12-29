<?php

declare(strict_types=1);

use ComplexHeart\Domain\Model\Errors\ImmutabilityError;
use ComplexHeart\Domain\Model\Test\OrderManagement\Domain\Errors\InvalidPriceError;
use ComplexHeart\Domain\Model\Test\OrderManagement\Domain\Price;

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