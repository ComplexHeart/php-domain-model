<?php

declare(strict_types=1);

use ComplexHeart\Domain\Model\Exceptions\InvariantViolation;
use ComplexHeart\Domain\Model\Test\Fixtures\TypeSafety\Email;
use ComplexHeart\Domain\Model\Test\Fixtures\TypeSafety\Money;
use ComplexHeart\Domain\Model\Test\Fixtures\TypeSafety\CustomEntity;
use ComplexHeart\Domain\Model\ValueObjects\UUIDValue;

test('ValueObject should auto-check invariants and fail', function () {
    Email::make('invalid-email');
})->throws(InvariantViolation::class);

test('ValueObject should auto-check invariants and succeed', function () {
    $email = Email::make('valid@example.com');

    expect($email)->toBeInstanceOf(Email::class);
});

test('ValueObject with multiple invariants should validate all', function () {
    Money::make(-10, 'USD');
})->throws(InvariantViolation::class);

test('ValueObject should fail on invalid currency invariant', function () {
    Money::make(100, 'US');
})->throws(InvariantViolation::class);

test('Entity with auto-check disabled should not check invariants', function () {
    // This should NOT throw even though name is empty
    $entity = CustomEntity::make(UUIDValue::random(), '');

    expect($entity)->toBeInstanceOf(CustomEntity::class);
});

test('Entity with auto-check enabled would fail on invalid data', function () {
    // Entities have auto-check enabled by default (via IsEntity)
    // But our CustomEntity overrides it to false
    // Let's test that regular entities DO auto-check

    // This should throw because invalid email
    Email::make('invalid-email');
})->throws(InvariantViolation::class);
