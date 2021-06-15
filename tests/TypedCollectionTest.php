<?php

declare(strict_types=1);

use ComplexHeart\Domain\Model\TypedCollection;
use ComplexHeart\Domain\Model\Exceptions\InvariantViolation;

test('Successfully instantiate a TypedCollection.', function () {
    $c = new class (['foo', 'bar']) extends TypedCollection {
        protected string $valueType = 'string';
    };

    expect($c)->toBeInstanceOf(TypedCollection::class);
}
);

test('Fail with wrong primitive value item type.', function () {
    new class ([1, '2']) extends TypedCollection {
        protected string $valueType = 'integer';
    };
}
)->throws(InvariantViolation::class);

test('Fail with wrong class value item types.', function () {
    new class ([new stdClass(), '2']) extends TypedCollection {
        protected string $valueType = stdClass::class;
    };
}
)->throws(InvariantViolation::class);

test('Fail due to unsupported key type.', function () {
    new class (['foo', 'bar']) extends TypedCollection {
        protected string $keyType = 'boolean';
    };
}
)->throws(InvariantViolation::class);

test('Fail due to wrong key type.', function () {
    new class (['foo', 'bar']) extends TypedCollection {
        protected string $keyType = 'string';
    };
}
)->throws(InvariantViolation::class);

