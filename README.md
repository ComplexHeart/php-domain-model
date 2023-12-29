# Domain Model

[![Tests](https://github.com/ComplexHeart/php-domain-model/actions/workflows/test.yml/badge.svg)](https://github.com/ComplexHeart/php-domain-model/actions/workflows/test.yml)
[![Quality Gate Status](https://sonarcloud.io/api/project_badges/measure?project=ComplexHeart_php-domain-model&metric=alert_status)](https://sonarcloud.io/summary/new_code?id=ComplexHeart_php-domain-model)
[![Coverage](https://sonarcloud.io/api/project_badges/measure?project=ComplexHeart_php-domain-model&metric=coverage)](https://sonarcloud.io/summary/new_code?id=ComplexHeart_php-domain-model)

## Modeling Aggregates, Entities and Value Objects

Complex Heart allows you to model your domain Aggregates, Entities, and Value Objects using a set of traits. Great, but
why traits and not classes? Well, sometimes you have some kind of inheritance in your classes. Being forced to use a
certain base class is too invasive and personally, I don't like it. By using a set of traits and interfaces you have all
the functionality you need without compromising the essence of your own domain.

Let's see a very basic example:

```php
use ComplexHeart\Contracts\Domain\Model\ValueObject;use ComplexHeart\Domain\Model\IsValueObject;

/**
 * Class Color
 * @method string value()
 */
final class Color implements ValueObject 
{
    use IsValueObject;
    
    private string $value;
 
    public function __construct(string $value) {
        $this->initialize(['value' => $value]);
    }
    
    protected function invariantValueMustBeHexadecimal(): bool {
        return preg_match('/^#(?:[0-9a-fA-F]{3}){1,2}$/', $this->value) === 1;
    }
    
    public function __toString(): string {
        return $this->value();
    }
}

$red = new Color('#ff0000');
$red->equals(new Color('#00ff00')); // false
$red->value(); // #ff0000
$magenta = new Color('ff00ff'); // Exception InvariantViolation: Value must be hexadecimal.
```

To define a Value Object you only need to use the `IsValueObject` trait. This trait will allow you to use some functions
like `equals()` that will automatically compare the value of the objects. The `initialize()` is also available, it will
allow you to run invariant validations against the object values. Optionally, and recommended, you can use
the `ValueObject` interface.

The available traits are:

- `HasAttributes` Provide some functionality to manage attributes.
- `HasEquality` Provide functionality to handle equality between objects.
- `HasInvariants` Allow invariant checking on instantiation (Guard Clause).
- `HasIdentity` Define the Entity/Aggregate identity.
- `HasDomainEvents` Provide domain event management.

On top of those base traits **Complex Heart** provide ready to use compositions:

- `IsModel` composed by `HasAttributes` and `HasInvariants`.
- `IsValueObject` composed by `IsModel` and `HasEquality`.
- `IsEntity` composed by `IsModel`, `HasIdentity`, `HasEquality`.
- `IsAggregate` composed by `IsEntity`, `HasDomainEvents`.
