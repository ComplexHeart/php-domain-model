# Value Objects

> A small simple object, like money or a date range, whose equality isn't based on identity.\
> -- Martin Fowler

A Value Object, as defined by Martin Fowler, is a small, simple object like money or a date range, whose equality isn't
based on identity. In the Complex Heart Domain Model library, creating a Value Object is made easy through the use of
the `IsValueObject` trait. This trait incorporates `HasAttributes`, `HasInvariants`, and `HasEquality` traits,
streamlining the process of implementing robust Value Objects. Additionally, the `ValueObject` interface is available to
expose the `values` and `equals` methods.

## Getting Started

Creating a Value Object with the Complex Heart Domain Model library is straightforward. The library provides a trait
named `IsValueObject` that, when used, adds three essential traits to your class:

* `HasAttributes`: Allows the Value Object to have attributes.
* `HasInvariants`: Enables the definition and validation of invariants.
* `HasEquality`: Facilitates the implementation of equality comparison.

Additionally, you can implement the `ValueObject` interface to expose the values and equals methods, providing a
consistent interface for all Value Objects.

## Example

Let's illustrate the implementation of a `Color` Value Object using the provided traits and interface:

```php
use ComplexHeart\Contracts\Domain\Model\ValueObject;
use ComplexHeart\Domain\Model\IsValueObject;

class Color implements ValueObject 
{
    use IsValueObject;

    public function __construct(public string $value) 
    {
        $this->check();
    }
    
    protected function invariantValueMustBeHexadecimal(): bool 
    {
        return preg_match('/^#(?:[0-9a-fA-F]{3}){1,2}$/', $this->value) === 1;
    }
    
    public function __toString(): string 
    {
        return $this->value;
    }
}

// Instantiate a Color object
$red = new Color('#ff0000');

// Check equality with another Color object
$red->equals(new Color('#00ff00')); // returns false

// Retrieve the value of the Color object
$red->value; // returns #ff0000

// Exception handling for invariant violation
$magenta = new Color('ff00ff'); // throws InvariantViolation: Value must be hexadecimal.
```

## Key Concepts

### Attribute Initialization

#### Modern Approach: Type-Safe Factory Method (Recommended)

Use the `make()` static factory method for type-safe instantiation with automatic invariant checking:

```php
class Email implements ValueObject
{
    use IsValueObject;

    public function __construct(private readonly string $value) {}

    protected function invariantValidFormat(): bool
    {
        return filter_var($this->value, FILTER_VALIDATE_EMAIL) !== false;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}

// Type-safe instantiation with automatic validation
$email = Email::make('user@example.com'); // ✅ Valid
$email = Email::make(123); // ❌ TypeError: parameter "value" must be of type string, int given
$email = Email::make('invalid'); // ❌ InvariantViolation: Valid format
```

**Benefits of `make()`:**
- Runtime type validation with clear error messages
- Automatic invariant checking after construction
- Works seamlessly with readonly properties
- PHPStan level 8 compliant

**Important:** Auto-check ONLY works when using `make()`. Direct constructor calls do NOT trigger automatic invariant checking, so you must manually call `$this->check()` in the constructor.

#### Alternative: Constructor Property Promotion with Manual Check

If you prefer direct constructor calls, you **must** manually call `$this->check()`:

```php
class SomeValueObject implements ValueObject
{
    use IsValueObject;

    public function __construct(public string $value)
    {
        $this->check(); // Required for invariant validation
    }
}

// Direct constructor call requires manual check() in constructor
$vo = new SomeValueObject('value'); // check() is called inside constructor
```

Use [Constructor property promotion](https://www.php.net/manual/en/language.oop5.decon.php#language.oop5.decon.constructor.promotion) for cleaner syntax.

#### Legacy: Initialize Method (Deprecated)

The `initialize` method is deprecated and will be removed in v1.0.0. It's incompatible with readonly properties:

```php
class SomeValueObject implements ValueObject
{
    use IsValueObject;

    public string $value;

    public function __construct(string $value)
    {
        $this->initialize(['value' => $value]); // Deprecated
    }
}
```

### Immutability

Immutability is a fundamental characteristic of Value Objects. A Value Object is considered immutable when its state
cannot be altered after instantiation. Once created, a Value Object retains its initial state throughout its lifecycle,
ensuring stability and predictability in the domain model.

You can
use [readonly properties](https://www.php.net/manual/en/language.oop5.properties.php#language.oop5.properties.readonly-properties)
to ensure the state of the value object do not change.

```php
class SomeValueObject implements ValueObject 
{
    use IsValueObject;

    public function __construct(public readonly string $value) 
    {
        $this->check();
    }
}
```

Alternatively, you can use the `HasImmutability` trait to enforce immutability. Just set the properties as private, the
`HasImmutability` trait adds `__set` and `__get` magic methods to access the inner properties of the Value Object but
blocking any attempt of updating the value.

```php
class SomeValueObject implements ValueObject 
{
    use IsValueObject;

    public function __construct(private string $value) 
    {
        $this->check();
    }
    
    //...
}

$vo = new SomeValueObject('original');
$vo->value = 'updated'; //ImmutabilityError: Cannot modify property value from immutable SomeValueObject object.
```

### Invariants

Implement the invariant methods to define rules that must be upheld by the Value Object. Invariants contribute to the
integrity of the object's state. The invariant rules names must begin with the prefix `invariant`:

```php
protected function invariantValueMustBeHexadecimal(): bool 
{
    return preg_match('/^#(?:[0-9a-fA-F]{3}){1,2}$/', $this->value) === 1;
}
```

An invariant function must adhere to the following criteria:

* It should either return a boolean value or throw an exception.
* Returning true signifies that the invariant has been successfully satisfied.
* Returning false, throwing an exception, or triggering an error in the invariant indicates that the invariant has not
  been met.

If boolean is returned the error message will be the invariant method name
(PascalCase) in normal case. For example:

```
invariantValueMustBeHexadecimal => InvariantViolation: Value must be hexadecimal
```

If exception is thrown the error message will be the exception message. This allows you to customize the error messages.

### Equality Check

Utilize the equals method inherited from the HasEquality trait to compare the equality of two Value Objects.

```php
$red = new Color('#ff0000');
$red->equals(new Color('#00ff00')); // returns false
$red->equals(new stdClass()); // returns false
$red->equals('red'); // returns false

$anotherRed = new Color('#ff0000');
$red->equals($anotherRed); // return true
```
