<?php

declare(strict_types=1);

use ComplexHeart\Domain\Model\Traits\HasTypeCheck;
use ComplexHeart\Domain\Model\ValueObjects\StringValue;

test('HasTypeCheck::isValueTypeValid should return true for valid primitive types', function () {
    $object = new class () {
        use HasTypeCheck;

        public function testValidation(mixed $value, string $type): bool
        {
            return $this->isValueTypeValid($value, $type);
        }
    };

    expect($object->testValidation(42, 'integer'))->toBeTrue()
        ->and($object->testValidation(3.14, 'float'))->toBeTrue()
        ->and($object->testValidation('hello', 'string'))->toBeTrue()
        ->and($object->testValidation(true, 'boolean'))->toBeTrue()
        ->and($object->testValidation([1, 2, 3], 'array'))->toBeTrue()
        ->and($object->testValidation(new stdClass(), 'object'))->toBeTrue()
        ->and($object->testValidation(fn () => null, 'callable'))->toBeTrue();
})
    ->group('Unit');

test('HasTypeCheck::isValueTypeValid should return false for invalid primitive types', function () {
    $object = new class () {
        use HasTypeCheck;

        public function testValidation(mixed $value, string $type): bool
        {
            return $this->isValueTypeValid($value, $type);
        }
    };

    expect($object->testValidation('42', 'integer'))->toBeFalse()
        ->and($object->testValidation(42, 'string'))->toBeFalse()
        ->and($object->testValidation([1, 2], 'string'))->toBeFalse()
        ->and($object->testValidation(new stdClass(), 'array'))->toBeFalse();
})
    ->group('Unit');

test('HasTypeCheck::isValueTypeValid should handle mixed type', function () {
    $object = new class () {
        use HasTypeCheck;

        public function testValidation(mixed $value, string $type): bool
        {
            return $this->isValueTypeValid($value, $type);
        }
    };

    expect($object->testValidation(42, 'mixed'))->toBeTrue()
        ->and($object->testValidation('string', 'mixed'))->toBeTrue()
        ->and($object->testValidation(null, 'mixed'))->toBeTrue()
        ->and($object->testValidation([], 'mixed'))->toBeTrue();
})
    ->group('Unit');

test('HasTypeCheck::isValueTypeValid should validate class instances', function () {
    $object = new class () {
        use HasTypeCheck;

        public function testValidation(mixed $value, string $type): bool
        {
            return $this->isValueTypeValid($value, $type);
        }
    };

    $stringValue = new class ('test') extends StringValue {
    };

    expect($object->testValidation($stringValue, StringValue::class))->toBeTrue()
        ->and($object->testValidation(new stdClass(), StringValue::class))->toBeFalse()
        ->and($object->testValidation($stringValue, stdClass::class))->toBeFalse();
})
    ->group('Unit');

test('HasTypeCheck::isValueTypeNotValid should return opposite of isValueTypeValid', function () {
    $object = new class () {
        use HasTypeCheck;

        public function testValidation(mixed $value, string $type): bool
        {
            return $this->isValueTypeNotValid($value, $type);
        }
    };

    expect($object->testValidation(42, 'string'))->toBeTrue()
        ->and($object->testValidation(42, 'integer'))->toBeFalse()
        ->and($object->testValidation('hello', 'string'))->toBeFalse()
        ->and($object->testValidation('hello', 'integer'))->toBeTrue();
})
    ->group('Unit');

test('HasTypeCheck should work with objects implementing interfaces', function () {
    $object = new class () {
        use HasTypeCheck;

        public function testValidation(mixed $value, string $type): bool
        {
            return $this->isValueTypeValid($value, $type);
        }
    };

    $countable = new ArrayObject([1, 2, 3]);

    expect($object->testValidation($countable, 'Countable'))->toBeTrue()
        ->and($object->testValidation($countable, ArrayObject::class))->toBeTrue()
        ->and($object->testValidation(new stdClass(), 'Countable'))->toBeFalse();
})
    ->group('Unit');
