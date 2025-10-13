# Entities

> Objects that have a distinct identity that runs through time and different representations.\
> -- Eric Evans

An Entity is a fundamental concept representing a distinct, identifiable object with a continuous and evolving
lifecycle. Unlike Value Objects, which are defined by their attributes and are interchangeable based on their values,
Entities are distinguished by a unique identity that persists over time.

## Getting Started

Creating an Entity with the Complex Heart Domain Model is really simple. The library provides a trait
named `IsEntity` that, when used, adds four essential traits to your class:

* `HasAttributes`: Allows the Entities to have attributes.
* `HasInvariants`: Enables the definition and validation of invariants.
* `HasEquality`: Facilitates the implementation of equality comparison.
* `HasIdentity`: Provides identification by unique id.

Additionally, you can implement the `Entity` interface to expose the values and equals methods, providing a
consistent interface for all Entities.

## Example

Let's illustrate the implementation of a `Customer` Entity using the provided traits and interface:

#### Modern Approach: Type-Safe Factory Method (Recommended)

```php
final class Customer implements Entity
{
    use IsEntity;

    public function __construct(
        private readonly UUIDValue $id,
        public string $name,
    ) {}

    public function id(): Identifier
    {
        return $this->id;
    }

    public function __toString(): string
    {
        return "$this->name ($this->id)";
    }
}

// Type-safe instantiation with automatic invariant validation
$customer = Customer::make(UUIDValue::random(), 'Vincent Vega');

// Named parameters for improved readability (PHP 8.0+)
$customer = Customer::make(
    id: UUIDValue::random(),
    name: 'Vincent Vega'
);
```

**Benefits:**
- Automatic invariant checking when using `make()`
- Type validation at runtime with clear error messages
- Named parameter support for improved readability
- Union type support (e.g., `int|float`, `string|null`)
- Cleaner constructor code

**Important:** Auto-check ONLY works when using `make()`. If you call the constructor directly (`new Customer(...)`), you must manually call `$this->check()` inside the constructor.

#### Alternative: Direct Constructor with Manual Check

If you need to use the constructor directly, you **must** manually call `$this->check()`:

```php
final class Customer implements Entity
{
    use IsEntity;

    public function __construct(
        public readonly UUIDValue $id,
        public string $name,
    ) {
        $this->check(); // Required for invariant validation
    }

    public function id(): Identifier
    {
        return $this->id;
    }

    public function __toString(): string
    {
        return "$this->name ($this->id)";
    }
}
```

## Key Concepts

### Identity

Entities have a distinct identity that sets them apart from other objects. This identity often persists across
different states or versions of the object.

## Lifecycle

Entities have a lifecycle that spans various states or transitions. They exist beyond a single operation or
transaction, maintaining continuity and relevance over time.

### Mutability

Unlike Value Objects, Entities can undergo changes in their internal state while retaining the same identity. This
mutability is often a crucial aspect of their behavior.

### Equality Based on Identity

Equality for Entities is based on identity rather than the equality of attribute values. Two entities with the same
identity are considered equal, regardless of potential differences in other attributes. You can use the `equals` method
with another entity, in this case the id will be used.

```php
$c1 = new Customer(UUIDValue::random(), 'Vincent Vega');
$c2 = new Customer(UUIDValue::random(), 'Marcellus Wallace');
$c1->equals($c2); // returns false

$c2->name = 'Vincent Vega';
$c1->equals($c2); // returns false
$c1->equals($c1); // return true
```

### Consistency and Business Rules

Entities encapsulate and enforce business rules and logic related to their specific domain. They are responsible for
maintaining consistency within their boundaries. You can use invariants the same way that with the Value Objects.

```php
protected function invariantSomeInvariantForCustomer(): bool 
{
    // do some validations.
}
```
