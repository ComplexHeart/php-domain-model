<?php

declare(strict_types=1);

use ComplexHeart\Domain\Model\Exceptions\Contracts\Aggregatable;
use ComplexHeart\Domain\Model\Exceptions\InvariantViolation;
use ComplexHeart\Domain\Model\Test\Fixtures\OrderManagement\Domain\Errors\InvalidPriceError;
use ComplexHeart\Domain\Model\Test\Fixtures\OrderManagement\Domain\Price;
use ComplexHeart\Domain\Model\Test\Fixtures\TypeSafety\CustomEntity;
use ComplexHeart\Domain\Model\Test\Fixtures\TypeSafety\Email;
use ComplexHeart\Domain\Model\Test\Fixtures\TypeSafety\Money;
use ComplexHeart\Domain\Model\Traits\HasInvariants;
use ComplexHeart\Domain\Model\ValueObjects\UUIDValue;

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
    ->throws(InvariantViolation::class, 'Multiple errors (2)');

test('InvariantViolation should support multiple violations aggregation', function () {
    try {
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

            protected function invariantAlwaysFailThree(): bool
            {
                return false;
            }
        };
    } catch (InvariantViolation $e) {
        expect($e->hasMultipleErrors())->toBeTrue()
            ->and($e->getErrorCount())->toBe(3)
            ->and($e->getErrors())->toHaveCount(3)
            ->and($e->getMessage())->toContain('Multiple errors (3)')
            ->and($e->getErrors()[0]->getMessage())->toContain('always fail one')
            ->and($e->getErrors()[1]->getMessage())->toContain('always fail two')
            ->and($e->getErrors()[2]->getMessage())->toContain('always fail three');
    }
})
    ->group('Unit');

test('InvariantViolation::fromErrors should handle single error cleanly', function () {
    try {
        new class () {
            use HasInvariants;

            public function __construct()
            {
                $this->check();
            }

            protected function invariantSingleFailure(): bool
            {
                return false;
            }
        };
    } catch (InvariantViolation $e) {
        expect($e->hasMultipleErrors())->toBeFalse()
            ->and($e->getErrorCount())->toBe(1)
            ->and($e->getErrors()[0]->getMessage())->toBe('single failure')
            ->and($e->getMessage())->toBe('single failure')
            ->and($e->getMessage())->not->toContain('Multiple errors');
    }
})
    ->group('Unit');

test('InvariantViolation::fromErrors should format multiple errors', function () {
    $errors = [
        new InvariantViolation('First error'),
        new InvariantViolation('Second error'),
        new InvariantViolation('Third error')
    ];
    $exception = InvariantViolation::fromErrors($errors);

    expect($exception)->toBeInstanceOf(InvariantViolation::class)
        ->and($exception->hasMultipleErrors())->toBeTrue()
        ->and($exception->getErrorCount())->toBe(3)
        ->and($exception->getErrors())->toBe($errors)
        ->and($exception->getMessage())->toContain('Multiple errors (3)')
        ->and($exception->getMessage())->toContain('First error')
        ->and($exception->getMessage())->toContain('Second error')
        ->and($exception->getMessage())->toContain('Third error');
})
    ->group('Unit');

test('InvariantViolation::fromErrors with single error should not show count', function () {
    $exception = InvariantViolation::fromErrors([new InvariantViolation('Single error message')]);

    expect($exception)->toBeInstanceOf(InvariantViolation::class)
        ->and($exception->hasMultipleErrors())->toBeFalse()
        ->and($exception->getErrorCount())->toBe(1)
        ->and($exception->getErrors()[0]->getMessage())->toBe('Single error message')
        ->and($exception->getMessage())->toBe('Single error message');
})
    ->group('Unit');

test('Custom non-aggregatable exception should be thrown immediately', function () {
    new class () {
        use HasInvariants;

        public function __construct()
        {
            $this->check();
        }

        protected function invariantCustomError(): bool
        {
            throw new DomainException('Custom domain error');
        }
    };
})
    ->group('Unit')
    ->throws(DomainException::class, 'Custom domain error');

test('Custom non-aggregatable exception stops invariant checking', function () {
    new class () {
        use HasInvariants;

        public function __construct()
        {
            $this->check();
        }

        protected function invariantFirstCheck(): bool
        {
            // This should throw immediately
            throw new RuntimeException('First error');
        }

        protected function invariantSecondCheck(): bool
        {
            // This should NEVER be reached
            throw new DomainException('Second error - should not be reached');
        }
    };
})
    ->group('Unit')
    ->throws(RuntimeException::class, 'First error');

test('Custom aggregatable exception should be aggregated', function () {
    try {
        new class () {
            use HasInvariants;

            public function __construct()
            {
                $this->check();
            }

            protected function invariantFirst(): bool
            {
                // Create aggregatable exception inline
                $exception = new class ('Aggregatable error') extends \Exception implements Aggregatable {};
                throw $exception;
            }

            protected function invariantSecond(): bool
            {
                return false; // Regular InvariantViolation
            }
        };
    } catch (InvariantViolation $e) {
        expect($e->hasMultipleErrors())->toBeTrue()
            ->and($e->getErrorCount())->toBe(2)
            ->and($e->getErrors()[0]->getMessage())->toContain('Aggregatable error')
            ->and($e->getErrors()[1]->getMessage())->toContain('second');
    }
})
    ->group('Unit');

test('InvariantViolation implements Aggregatable', function () {
    $exception = InvariantViolation::fromErrors([new InvariantViolation('Test')]);

    expect($exception)->toBeInstanceOf(Aggregatable::class);
})
    ->group('Unit');

test('Mix of custom non-aggregatable throws immediately before aggregation', function () {
    new class () {
        use HasInvariants;

        public function __construct()
        {
            $this->check();
        }

        protected function invariantFirstAggregatable(): bool
        {
            return false; // InvariantViolation
        }

        protected function invariantCustomNonAggregatable(): bool
        {
            throw new RuntimeException('Non-aggregatable error');
        }

        protected function invariantThirdAggregatable(): bool
        {
            return false; // Should not be reached
        }
    };
})
    ->group('Unit')
    ->throws(RuntimeException::class, 'Non-aggregatable error');

// Auto-check feature tests
test('ValueObject should auto-check invariants and fail', function () {
    Email::make('invalid-email');
})
    ->group('Unit')
    ->throws(InvariantViolation::class);

test('ValueObject should auto-check invariants and succeed', function () {
    $email = Email::make('valid@example.com');

    expect($email)->toBeInstanceOf(Email::class);
})
    ->group('Unit');

test('ValueObject with multiple invariants should validate all', function () {
    Money::make(-10, 'USD');
})
    ->group('Unit')
    ->throws(InvariantViolation::class);

test('ValueObject should fail on invalid currency invariant', function () {
    Money::make(100, 'US');
})
    ->group('Unit')
    ->throws(InvariantViolation::class);

test('Entity with auto-check disabled should not check invariants', function () {
    // This should NOT throw even though name is empty
    $entity = CustomEntity::make(UUIDValue::random(), '');

    expect($entity)->toBeInstanceOf(CustomEntity::class);
})
    ->group('Unit');
