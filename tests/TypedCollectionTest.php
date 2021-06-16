<?php

declare(strict_types=1);

use ComplexHeart\Domain\Model\TypedCollection;
use ComplexHeart\Domain\Model\Exceptions\InvariantViolation;

test('Instantiate a TypedCollection[mixed].', function () {
    $c = new TypedCollection(['a', 1, 1.2, new stdClass()]);

    expect($c)->toBeInstanceOf(TypedCollection::class);
})->group('Unit');

test('Instantiate a TypedCollection[int, string].', function () {
    $c = new class (['foo', 'bar']) extends TypedCollection {
        protected string $valueType = 'string';
    };

    expect($c)->toBeInstanceOf(TypedCollection::class);
})->group('Unit');

test('Instantiate a TypedCollection[string, string].', function () {
    $c = new class (['one' => 'foo', 'two' => 'bar']) extends TypedCollection {
        protected string $keyType = 'string';
        protected string $valueType = 'string';
    };

    expect($c)->toBeInstanceOf(TypedCollection::class);
})->group('Unit');

test('Add a new item to TypedCollection[string, string]', function () {
    $c = new class (['one' => 'foo', 'two' => 'bar']) extends TypedCollection {
        protected string $keyType = 'string';
        protected string $valueType = 'string';
    };

    $c['three'] = 'foobar';

    expect($c)->toHaveCount(3);
})->group('Unit');

test('Push a new item into the TypedCollection[int, string]', function () {
    $c = new class (['foo', 'bar']) extends TypedCollection {
        protected string $valueType = 'string';
    };

    $c[] = 'foobar';
    $c->push('other', 'another');
    $c->add('last one');

    expect($c)->toHaveCount(6);
})->group('Unit');

test('Prepend a new item into the TypedCollection[string, string]', function () {
    $c = new class (['one' => 'foo', 'two' => 'bar']) extends TypedCollection {
        protected string $keyType = 'string';
        protected string $valueType = 'string';
    };

    $c->prepend('last', 'last');

    expect($c)
        ->toHaveCount(3)
        ->toMatchArray(['one' => 'foo', 'two' => 'bar', 'last' => 'last']);
})->group('Unit');

test('Pluck attribute from item in TypeCollection[array]', function () {
    $items = [
        ['name' => 'Vicent', 'surname' => 'Vega'],
        ['name' => 'Jules', 'surname' => 'Winnfield']
    ];

    $c = new class ($items) extends TypedCollection {
        protected string $valueType = 'array';
    };

    $names = $c->pluck('name')->all();
    expect($names)->toMatchArray(['Vicent', 'Jules']);

    $names = $c->pluck('name', 'surname')->all();
    expect($names)->toMatchArray(['Vega' => 'Vicent', 'Winnfield' => 'Jules']);
})->group('Unit');

test('Return the collection keys.', function () {
    $c = new class (['one' => 'foo', 'two' => 'bar']) extends TypedCollection {
        protected string $keyType = 'string';
        protected string $valueType = 'string';
    };

    $keys = $c->keys()->all();
    expect($keys)->toMatchArray(['one', 'two']);
})->group('Unit');

test('Fail with wrong primitive value item type.', function () {
    new class ([1, '2']) extends TypedCollection {
        protected string $valueType = 'integer';
    };
})
    ->throws(InvariantViolation::class)
    ->group('Unit');

test('Fail with wrong class value item types.', function () {
    new class ([new stdClass(), '2']) extends TypedCollection {
        protected string $valueType = stdClass::class;
    };
})
    ->throws(InvariantViolation::class)
    ->group('Unit');

test('Fail due to unsupported key type.', function () {
    new class (['foo', 'bar']) extends TypedCollection {
        protected string $keyType = 'boolean';
    };
})
    ->throws(InvariantViolation::class)
    ->group('Unit');

test('Fail due to wrong key type.', function () {
    new class (['foo', 'bar']) extends TypedCollection {
        protected string $keyType = 'string';
    };
})
    ->throws(InvariantViolation::class)
    ->group('Unit');

test('Fail adding item with wrong key type.', function () {
    $c = new class ([]) extends TypedCollection {
        protected string $keyType = 'string';
    };

    $c[] = 'wrong';
})
    ->throws(InvariantViolation::class)
    ->group('Unit');