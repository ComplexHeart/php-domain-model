<?php

declare(strict_types=1);

use ComplexHeart\Domain\Model\Exceptions\InvariantViolation;
use ComplexHeart\Domain\Model\ValueObjects\StringValue;


final class ProductName extends StringValue
{
    protected int $_maxLength = 30;
    protected int $_minLength = 3;

    protected function invariantMustStartWithStringProduct(): bool
    {
        if (strpos($this->value(), 'PR') !== 0) {
            throw new InvariantViolation('Product Name must start with PR chars');
        }

        return true;
    }
}

test('Should create a valid Value Object.', function () {
    $vo = new ProductName('PR sample');
    expect($vo)->toEqual('PR sample');
    expect((string)$vo)->toEqual('PR sample');
})->group('Unit');

test('Should return true on equal ValueObjects.', function () {
    $vo = new ProductName('PR sample');
    expect($vo->equals(new ProductName('PR sample')))->toBeTrue();
})->group('Unit');

test('Should return false on not equal ValueObjects.', function () {
    $vo = new ProductName('PR sample');
    expect($vo->equals(new ProductName('PR diff')))->toBeFalse();
})->group('Unit');

test('Should throw exception on min length invariant violation.', function () {
    new class('a') extends StringValue {
        protected int $_minLength = 5;
    };

})
    ->throws(InvariantViolation::class)
    ->group('Unit');

test('Should throw exception on max length invariant violation.', function () {
    new class('this is long') extends StringValue {
        protected int $_maxLength = 5;
    };
})
    ->throws(InvariantViolation::class)
    ->group('Unit');

test('Should throw exception on regex invariant violation.', function () {
    new class('INVALID') extends StringValue {
        protected string $_pattern = '[a-z]';
    };
})
    ->throws(InvariantViolation::class)
    ->group('Unit');