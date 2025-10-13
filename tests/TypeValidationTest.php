<?php

declare(strict_types=1);

use ComplexHeart\Domain\Model\Test\Fixtures\TypeSafety\Email;
use ComplexHeart\Domain\Model\Test\Fixtures\TypeSafety\Money;
use ComplexHeart\Domain\Model\Test\Fixtures\TypeSafety\ComplexModel;
use ComplexHeart\Domain\Model\Test\Fixtures\TypeSafety\FlexibleValue;

test('make() should create instance with valid types', function () {
    $email = Email::make('test@example.com');

    expect($email)->toBeInstanceOf(Email::class)
        ->and((string) $email)->toBe('test@example.com');
});

test('make() should throw TypeError for invalid string type', function () {
    Email::make(123);
})->throws(TypeError::class, 'parameter "value" must be of type string, int given');

test('make() should throw InvalidArgumentException for missing required parameters', function () {
    Email::make();
})->throws(InvalidArgumentException::class, 'missing required parameters: value');

test('make() should accept int or float for union type', function () {
    $money1 = Money::make(100, 'USD');
    $money2 = Money::make(99.99, 'EUR');

    expect($money1)->toBeInstanceOf(Money::class)
        ->and($money2)->toBeInstanceOf(Money::class);
});

test('make() should throw TypeError for invalid union type', function () {
    Money::make('invalid', 'USD');
})->throws(TypeError::class);

test('make() should handle nullable types correctly', function () {
    $model = ComplexModel::make(1, 'Test', null, []);

    expect($model)->toBeInstanceOf(ComplexModel::class);
});

test('make() should handle array types correctly', function () {
    $model = ComplexModel::make(1, 'Test', 'Description', ['tag1', 'tag2']);

    expect($model)->toBeInstanceOf(ComplexModel::class);
});

test('make() should throw TypeError for invalid array type', function () {
    ComplexModel::make(1, 'Test', 'Description', 'not-an-array');
})->throws(TypeError::class, 'parameter "tags" must be of type array, string given');

test('make() error message shows simple class name', function () {
    try {
        Email::make(123);
    } catch (TypeError $e) {
        expect($e->getMessage())->toContain('Email::make()')
            ->and($e->getMessage())->not->toContain('ComplexHeart\\Domain\\Model');
    }
});

test('make() error message shows parameter name', function () {
    try {
        Money::make('invalid', 'USD');
    } catch (TypeError $e) {
        expect($e->getMessage())->toContain('parameter "amount"');
    }
});

test('make() error message shows actual type given', function () {
    try {
        Email::make(123);
    } catch (TypeError $e) {
        expect($e->getMessage())->toContain('int given');
    }
});

test('direct constructor bypasses type validation', function () {
    // Direct constructor call with wrong type will fail at PHP level
    new Email(123);
})->throws(TypeError::class);

test('make() should accept int for int|float union type', function () {
    $money = Money::make(100, 'USD');

    expect($money)->toBeInstanceOf(Money::class)
        ->and((string) $money)->toBe('100 USD');
});

test('make() should accept float for int|float union type', function () {
    $money = Money::make(99.99, 'EUR');

    expect($money)->toBeInstanceOf(Money::class)
        ->and((string) $money)->toBe('99.99 EUR');
});

test('make() should accept int for int|float|string union type', function () {
    $value = FlexibleValue::make(42);

    expect($value)->toBeInstanceOf(FlexibleValue::class)
        ->and((string) $value)->toBe('42');
});

test('make() should accept float for int|float|string union type', function () {
    $value = FlexibleValue::make(3.14);

    expect($value)->toBeInstanceOf(FlexibleValue::class)
        ->and((string) $value)->toBe('3.14');
});

test('make() should accept string for int|float|string union type', function () {
    $value = FlexibleValue::make('text');

    expect($value)->toBeInstanceOf(FlexibleValue::class)
        ->and((string) $value)->toBe('text');
});

test('make() should reject invalid type for union type', function () {
    Money::make(['not', 'valid'], 'USD');
})->throws(TypeError::class, 'parameter "amount" must be of type int|float');

test('make() should handle nullable union types', function () {
    $value1 = FlexibleValue::make(42, 'Label');
    $value2 = FlexibleValue::make(42, null);

    expect($value1)->toBeInstanceOf(FlexibleValue::class)
        ->and($value2)->toBeInstanceOf(FlexibleValue::class);
});

test('make() should accept null for nullable union type', function () {
    $value = FlexibleValue::make('test', null);

    expect($value)->toBeInstanceOf(FlexibleValue::class);
});

test('make() union type error shows all possible types', function () {
    try {
        FlexibleValue::make(['array']);
    } catch (TypeError $e) {
        // Union type order depends on PHP's internal representation
        expect($e->getMessage())->toMatch('/int\|float\|string|string\|int\|float/')
            ->and($e->getMessage())->toContain('array given');
    }
});
