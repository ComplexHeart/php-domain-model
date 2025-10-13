<?php

declare(strict_types=1);

use ComplexHeart\Domain\Model\Exceptions\InstantiationException;
use ComplexHeart\Domain\Model\IsModel;
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

test('make() should accept named parameters', function () {
    $email = Email::make(value: 'test@example.com');

    expect($email)->toBeInstanceOf(Email::class)
        ->and((string) $email)->toBe('test@example.com');
});

test('make() should accept named parameters in any order', function () {
    $money = Money::make(currency: 'USD', amount: 100);

    expect($money)->toBeInstanceOf(Money::class)
        ->and((string) $money)->toBe('100 USD');
});

test('make() should mix named and positional parameters', function () {
    // First positional, rest named
    $model = ComplexModel::make(1, name: 'Test', description: 'Desc', tags: []);

    expect($model)->toBeInstanceOf(ComplexModel::class);
});

test('make() should skip optional parameters with named params', function () {
    // Skip optional 'label' parameter
    $value = FlexibleValue::make(value: 42);

    expect($value)->toBeInstanceOf(FlexibleValue::class)
        ->and((string) $value)->toBe('42');
});

test('make() should use default values for omitted named params', function () {
    // FlexibleValue has label with default null
    $value = FlexibleValue::make(value: 'test');

    expect($value)->toBeInstanceOf(FlexibleValue::class);
});

test('make() should throw error for missing required named parameter', function () {
    Money::make(amount: 100);
})->throws(InvalidArgumentException::class, 'missing required parameter: currency');

test('make() should validate types with named parameters', function () {
    Email::make(value: 123);
})->throws(TypeError::class, 'parameter "value" must be of type string, int given');

test('make() should handle nullable types with named parameters', function () {
    $model = ComplexModel::make(id: 1, name: 'Test', description: null, tags: []);

    expect($model)->toBeInstanceOf(ComplexModel::class);
});

test('make() should handle union types with named parameters', function () {
    $money1 = Money::make(amount: 100, currency: 'USD');
    $money2 = Money::make(amount: 99.99, currency: 'EUR');

    expect($money1)->toBeInstanceOf(Money::class)
        ->and($money2)->toBeInstanceOf(Money::class);
});

test('make() should validate union types with named parameters', function () {
    Money::make(amount: 'invalid', currency: 'USD');
})->throws(TypeError::class, 'parameter "amount" must be of type int|float');

test('new() should work as alias for make()', function () {
    $email = Email::new('test@example.com');

    expect($email)->toBeInstanceOf(Email::class)
        ->and((string) $email)->toBe('test@example.com');
});

test('new() should support positional parameters', function () {
    $money = Money::new(100, 'USD');

    expect($money)->toBeInstanceOf(Money::class)
        ->and((string) $money)->toBe('100 USD');
});

test('new() should support named parameters', function () {
    $email = Email::new(value: 'user@example.com');

    expect($email)->toBeInstanceOf(Email::class)
        ->and((string) $email)->toBe('user@example.com');
});

test('new() should support named parameters in any order', function () {
    $money = Money::new(currency: 'EUR', amount: 99.99);

    expect($money)->toBeInstanceOf(Money::class)
        ->and((string) $money)->toBe('99.99 EUR');
});

test('new() should throw TypeError for invalid types', function () {
    Email::new(123);
})->throws(TypeError::class, 'parameter "value" must be of type string, int given');

test('new() should validate union types', function () {
    Money::new(['invalid'], 'USD');
})->throws(TypeError::class, 'parameter "amount" must be of type int|float');

// InstantiationException tests
test('InstantiationException should be thrown when make() is called on class without constructor', function () {
    (new class () {
        use IsModel;

        public function __toString(): string
        {
            return 'no-constructor';
        }
    })::make();
})
    ->throws(InstantiationException::class, 'must have a constructor')
    ->group('Unit');

test('InstantiationException should extend RuntimeException', function () {
    $exception = new InstantiationException('test message');

    expect($exception)->toBeInstanceOf(RuntimeException::class)
        ->and($exception->getMessage())->toBe('test message');
})
    ->group('Unit');

test('InstantiationException should support error codes', function () {
    $exception = new InstantiationException('test message', 500);

    expect($exception->getCode())->toBe(500);
})
    ->group('Unit');

test('InstantiationException should support previous exceptions', function () {
    $previous = new Exception('Previous error');
    $exception = new InstantiationException('test message', 0, $previous);

    expect($exception->getPrevious())->toBe($previous);
})
    ->group('Unit');

// Model tests
test('Model values should be mapped by custom function successfully', function () {
    $money = Money::make(100, 'USD');

    $values = $money->values(fn ($attribute) => "-->$attribute");

    expect($values['amount'])->toStartWith('-->')
        ->and($values['currency'])->toStartWith('-->');
})
    ->group('Unit');
