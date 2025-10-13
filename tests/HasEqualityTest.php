<?php

declare(strict_types=1);

use ComplexHeart\Domain\Model\Traits\HasEquality;

test('HasEquality should compare objects by hash', function () {
    $class = get_class(new class ('dummy') {
        use HasEquality;

        public function __construct(private string $value)
        {
        }

        public function __toString(): string
        {
            return $this->value;
        }
    });

    $obj1 = new $class('test');
    $obj2 = new $class('test');

    expect($obj1->equals($obj2))->toBeTrue();
})
    ->group('Unit');

test('HasEquality should return false for different string representations', function () {
    $obj1 = new class ('test1') {
        use HasEquality;

        public function __construct(private string $value)
        {
        }

        public function __toString(): string
        {
            return $this->value;
        }
    };

    $obj2 = new class ('test2') {
        use HasEquality;

        public function __construct(private string $value)
        {
        }

        public function __toString(): string
        {
            return $this->value;
        }
    };

    expect($obj1->equals($obj2))->toBeFalse();
})
    ->group('Unit');

test('HasEquality should return false for different class types', function () {
    $obj1 = new class ('test') {
        use HasEquality;

        public function __construct(private string $value)
        {
        }

        public function __toString(): string
        {
            return $this->value;
        }
    };

    $obj2 = new stdClass();

    expect($obj1->equals($obj2))->toBeFalse();
})
    ->group('Unit');

test('HasEquality::hash should generate consistent SHA256 hash', function () {
    $obj = new class ('test-value') {
        use HasEquality;

        public function __construct(private string $value)
        {
        }

        public function __toString(): string
        {
            return $this->value;
        }

        public function getHash(): string
        {
            return $this->hash();
        }
    };

    $expectedHash = hash('sha256', 'test-value');

    expect($obj->getHash())->toBe($expectedHash);
})
    ->group('Unit');

test('HasEquality should handle complex objects', function () {
    $class = get_class(new class (['dummy' => 'data']) {
        use HasEquality;

        public function __construct(private array $data)
        {
        }

        public function __toString(): string
        {
            return json_encode($this->data);
        }
    });

    $obj1 = new $class(['name' => 'John', 'age' => 30]);
    $obj2 = new $class(['name' => 'John', 'age' => 30]);
    $obj3 = new $class(['name' => 'Jane', 'age' => 25]);

    expect($obj1->equals($obj2))->toBeTrue()
        ->and($obj1->equals($obj3))->toBeFalse();
})
    ->group('Unit');

test('HasEquality should handle empty strings', function () {
    $class = get_class(new class ('dummy') {
        use HasEquality;

        public function __construct(private string $value)
        {
        }

        public function __toString(): string
        {
            return $this->value;
        }
    });

    $obj1 = new $class('');
    $obj2 = new $class('');

    expect($obj1->equals($obj2))->toBeTrue();
})
    ->group('Unit');
