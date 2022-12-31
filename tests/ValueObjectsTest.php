<?php

declare(strict_types=1);

use ComplexHeart\Domain\Model\Exceptions\ImmutableException;
use ComplexHeart\Domain\Model\Exceptions\InvariantViolation;
use ComplexHeart\Domain\Model\Test\Sample\Models\Reference;
use ComplexHeart\Domain\Model\Test\Sample\Models\SampleList;
use ComplexHeart\Domain\Model\ValueObjects\ArrayValue;
use ComplexHeart\Domain\Model\ValueObjects\DateTimeValue;
use ComplexHeart\Domain\Model\ValueObjects\EnumValue;
use ComplexHeart\Domain\Model\ValueObjects\FloatValue;
use ComplexHeart\Domain\Model\ValueObjects\IntegerValue;
use ComplexHeart\Domain\Model\ValueObjects\StringValue;
use ComplexHeart\Domain\Model\ValueObjects\BooleanValue;
use ComplexHeart\Domain\Model\ValueObjects\UUIDValue;

test('StringValue should create a valid StringValue Object.', function () {
    $vo = new Reference('F2022.12.01-00001');
    expect($vo)->toEqual('F2022.12.01-00001');
    expect((string) $vo)->toEqual('F2022.12.01-00001');
})
    ->group('Unit');

test('StringValue should return true on equal StringValue Objects.', function () {
    $vo = new Reference('F2022.12.01-00001');
    expect($vo->equals(new Reference('F2022.12.01-00001')))->toBeTrue();
})
    ->group('Unit');

test('StringValue should return false on not equal StringValue Objects.', function () {
    $vo = new Reference('F2022.12.01-00001');
    expect($vo->equals(new Reference('F2022.12.01-00002')))->toBeFalse();
})
    ->group('Unit');

test('StringValue should throw exception on min length invariant violation.', function () {
    new class('a') extends StringValue {
        protected int $_minLength = 5;
    };
})
    ->throws(InvariantViolation::class)
    ->group('Unit');

test('StringValue should throw exception on max length invariant violation.', function () {
    new class('this is long') extends StringValue {
        protected int $_maxLength = 5;
    };
})
    ->throws(InvariantViolation::class)
    ->group('Unit');

test('StringValue should throw exception on regex invariant violation.', function () {
    new class('INVALID') extends StringValue {
        protected string $_pattern = '[a-z]';
    };
})
    ->throws(InvariantViolation::class)
    ->group('Unit');

test('BooleanValue should create a valid BooleanValue Object.', function () {
    $vo = new class(true) extends BooleanValue {
        protected array $_strings = [
            'true' => 'Yes',
            'false' => 'No',
        ];
    };
    expect((string) $vo)->toEqual('Yes');
})
    ->group('Unit');

test('IntegerValue should create a valid IntegerValue Object.', function () {
    $vo = new class(1) extends IntegerValue {
        protected int $_maxValue = 100;

        protected int $_minValue = 1;
    };
    expect((string) $vo)->toEqual('1');
})
    ->group('Unit');

test('IntegerValue should throw exception on min value invariant violation.', function () {
    new class(0) extends IntegerValue {
        protected int $_maxValue = 100;

        protected int $_minValue = 1;
    };
})
    ->throws(InvariantViolation::class)
    ->group('Unit');

test('IntegerValue should throw exception on mix value invariant violation.', function () {
    new class(101) extends IntegerValue {
        protected int $_maxValue = 100;

        protected int $_minValue = 1;
    };
})
    ->throws(InvariantViolation::class)
    ->group('Unit');

test('FloatValue should create a valid FloatValue Object.', function () {
    $vo = new class(3.14) extends FloatValue {
    };
    expect((string) $vo)->toEqual('3.14');
})
    ->group('Unit');

test('ArrayValue should create a valid ArrayValue Object.', function () {
    $vo = new class([1]) extends ArrayValue {
        protected int $_minItems = 1;

        protected int $_maxItems = 10;

        protected string $valueType = 'integer';
    };
    expect($vo)->toHaveCount(1);
})
    ->group('Unit');

test('ArrayValue should throw exception on invalid item type.', function () {
    new SampleList([0]);
})
    ->throws(InvariantViolation::class)
    ->group('Unit');

test('ArrayValue should throw exception on invalid minimum number of items.', function () {
    new class([]) extends ArrayValue {
        protected int $_minItems = 2;

        protected int $_maxItems = 0;

        protected string $valueType = 'integer';
    };
})
    ->throws(InvariantViolation::class)
    ->group('Unit');

test('ArrayValue should throw exception on invalid maximum number of items.', function () {
    new class([1, 2]) extends ArrayValue {
        protected int $_minItems = 0;

        protected int $_maxItems = 1;

        protected string $valueType = 'integer';
    };
})
    ->throws(InvariantViolation::class)
    ->group('Unit');

test('ArrayValue should implement correctly ArrayAccess interface.', function () {
    $vo = new class(['one', 'two']) extends ArrayValue {
        protected int $_minItems = 1;

        protected int $_maxItems = 10;

        protected string $valueType = 'string';
    };

    expect($vo)->toHaveCount(2);
    expect($vo)->toBeIterable();
    expect($vo->getIterator())->toBeInstanceOf(ArrayIterator::class);
    expect($vo[0])->toEqual('one');
});

test('ArrayValue should throw exception on deleting a value.', function () {
    $vo = new class(['one', 'two']) extends ArrayValue {
        protected int $_minItems = 1;

        protected int $_maxItems = 10;

        protected string $valueType = 'string';
    };
    unset($vo[1]);
})
    ->group('Unit')
    ->throws(ImmutableException::class);

test('ArrayValue should throw exception on changing a value.', function () {
    $vo = new class(['one', 'two']) extends ArrayValue {
        protected int $_minItems = 1;

        protected int $_maxItems = 10;

        protected string $valueType = 'string';
    };
    $vo[1] = 'NewOne';
})
    ->group('Unit')
    ->throws(ImmutableException::class);

test('ArrayValue should be converted to string correctly.', function () {
    $vo = new class(['one', 'two']) extends ArrayValue {
        protected int $_minItems = 1;

        protected int $_maxItems = 10;

        protected string $valueType = 'string';
    };

    expect((string) $vo)
        ->toBeString()
        ->toEqual('["one","two"]');
});

test('ArrayValue should implement correctly Serializable interface.', function () {
    $vo = new SampleList(['one', 'two']);
    $vo->unserialize($vo->serialize());

    expect($vo->values())->toEqual(['value' => ['one', 'two']]);
});

test('ArrayValue should implement successfully serialize and unserialize methods.', function () {
    $vo = new SampleList(['one', 'two']);

    expect($vo)->toEqual(unserialize(serialize($vo)));
});

test('UUIDValue should create a valid UUIDValue Object.', function () {
    $vo = UUIDValue::random();

    expect($vo->is($vo))->toBeTrue();
    expect((string) $vo)->toEqual($vo->__toString());
});

test('DateTimeValue should create a valid DateTimeValue Object.', function () {
    $vo = new DateTimeValue('2023-01-01T22:25:00+01:00');

    expect($vo->values())->toBe(['value' => '2023-01-01T22:25:00+01:00']);
});

test('EnumValue should create a valid EnumValue Object.', function () {
    $vo = new class ('one') extends EnumValue {
        const ONE = 'one';
        const TWO = 'two';
    };

    expect($vo->value())->toBe('one');
    expect($vo->value())->toBe((string) $vo);

    expect($vo::getLabels()[0])->toBe('ONE');
    expect($vo::getLabels()[1])->toBe('TWO');
});

