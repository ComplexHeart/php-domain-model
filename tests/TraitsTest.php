<?php

declare(strict_types=1);

use ComplexHeart\Domain\Model\Contracts\Aggregatable;
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
    expect($price->amount)->toBeFloat()
        ->and($price->currency)->toBeString();
})
    ->group('Unit');

test('Object with HasImmutability should return new instance with override values.', function () {
    $price = new Price(100.0, 'EUR');
    $newPrice = $price->applyDiscount(10.0);

    expect($newPrice)->toBeInstanceOf(Price::class)
        ->and($newPrice->amount)->toBe(90.0);
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
    ->throws(InvariantViolation::class, 'Multiple invariant violations (2)');

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
        expect($e->hasMultipleViolations())->toBeTrue()
            ->and($e->getViolationCount())->toBe(3)
            ->and($e->getViolations())->toHaveCount(3)
            ->and($e->getViolations())->toContain('always fail one')
            ->and($e->getViolations())->toContain('always fail two')
            ->and($e->getViolations())->toContain('always fail three')
            ->and($e->getMessage())->toContain('Multiple invariant violations (3)');
    }
})
    ->group('Unit');

test('InvariantViolation::fromViolations should handle single violation cleanly', function () {
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
        expect($e->hasMultipleViolations())->toBeFalse()
            ->and($e->getViolationCount())->toBe(1)
            ->and($e->getViolations())->toBe(['single failure'])
            ->and($e->getMessage())->toBe('single failure')
            ->and($e->getMessage())->not->toContain('Multiple invariant violations');
    }
})
    ->group('Unit');

test('InvariantViolation::fromViolations should format multiple violations', function () {
    $violations = ['First error', 'Second error', 'Third error'];
    $exception = InvariantViolation::fromViolations($violations);

    expect($exception)->toBeInstanceOf(InvariantViolation::class)
        ->and($exception->hasMultipleViolations())->toBeTrue()
        ->and($exception->getViolationCount())->toBe(3)
        ->and($exception->getViolations())->toBe($violations)
        ->and($exception->getMessage())->toContain('Multiple invariant violations (3)')
        ->and($exception->getMessage())->toContain('First error')
        ->and($exception->getMessage())->toContain('Second error')
        ->and($exception->getMessage())->toContain('Third error');
})
    ->group('Unit');

test('InvariantViolation::fromViolations with single violation should not show count', function () {
    $exception = InvariantViolation::fromViolations(['Single error message']);

    expect($exception)->toBeInstanceOf(InvariantViolation::class)
        ->and($exception->hasMultipleViolations())->toBeFalse()
        ->and($exception->getViolationCount())->toBe(1)
        ->and($exception->getViolations())->toBe(['Single error message'])
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
        expect($e->hasMultipleViolations())->toBeTrue()
            ->and($e->getViolationCount())->toBe(2)
            ->and($e->getViolations())->toContain('Aggregatable error')
            ->and($e->getViolations())->toContain('second');
    }
})
    ->group('Unit');

test('InvariantViolation implements Aggregatable', function () {
    $exception = InvariantViolation::fromViolations(['Test']);

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
