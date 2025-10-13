# Aggregates

> Aggregate is a cluster of domain objects that can be treated as a single unit.\
> -- Martin Fowler

An Aggregate is a cluster of associated objects that are treated as a unit for data consistency. Aggregates define clear consistency boundaries and enforce business rules within those boundaries. Creating an Aggregate with Complex Heart is straightforward using the `IsAggregate` trait, which combines `HasAttributes`, `HasInvariants`, `HasDomainEvents`, `HasIdentity`, and `HasEquality` traits. Additionally, you can implement the `Aggregate` interface to expose the `publishDomainEvents` method.

## Getting Started

The `IsAggregate` trait provides everything needed to model aggregates:

* `HasAttributes`: Manage aggregate attributes
* `HasInvariants`: Define and validate business rules
* `HasDomainEvents`: Register and publish domain events
* `HasIdentity`: Provide unique identification
* `HasEquality`: Enable identity-based equality comparison

## Example

The following example illustrates a complete Aggregate implementation.

#### Modern Approach: Type-Safe Factory Method (Recommended)

```php
final class Order implements Aggregate
{
    use IsAggregate;

    private function __construct(
        public Reference $reference,
        public Customer $customer,
        public OrderLines $lines,
        public Tags $tags,
        public Timestamp $created
    ) {}

    public static function create(int $number, array $customer): Order
    {
        $created = Timestamp::now();
        $order = self::make(
            reference: Reference::fromTimestamp($created, $number),
            customer: new Customer(...$customer),
            lines: OrderLines::empty(),
            tags: new Tags(),
            created: $created
        );

        $order->registerDomainEvent(new OrderCreated($order));

        return $order;
    }

    public function id(): Identifier
    {
        return $this->reference;
    }

    /**
     * Adds a new OrderLine to the Order.
     *
     * @throws InvariantViolation
     */
    public function addOrderLine(OrderLine $line): self
    {
        $this->lines->add($line);

        return $this;
    }

    public function withName(string $name): self
    {
        $this->customer->name = $name;
        return $this;
    }

    public function customerName(): string
    {
        return $this->customer->name;
    }

    public function __toString(): string
    {
        return $this->reference->value();
    }
}
```

**Benefits of using `make()` in factory methods:**
- Automatic invariant checking when using `make()`
- Type validation at runtime
- Cleaner factory method code
- Consistent with Value Objects and Entities

**Important:** Auto-check ONLY works when using `make()`. In the alternative approach using direct constructor calls, you must manually call `$this->check()` inside the constructor.

#### Alternative: Direct Constructor with Manual Check

If using the constructor directly, you **must** manually call `$this->check()`:

```php
final class Order implements Aggregate
{
    use IsAggregate;

    public function __construct(
        public Reference $reference,
        public Customer $customer,
        public OrderLines $lines,
        public Tags $tags,
        public Timestamp $created
    ) {
        $this->check(); // Required for invariant validation
    }

    public static function create(int $number, array $customer): Order
    {
        $created = Timestamp::now();
        $order = new Order(
            reference: Reference::fromTimestamp($created, $number),
            customer: new Customer(...$customer),
            lines: OrderLines::empty(),
            tags: new Tags(),
            created: $created
        );

        $order->registerDomainEvent(new OrderCreated($order));

        return $order;
    }

    // ... rest of the methods
}
```

## Key Concepts

### Root Entity

An Aggregate is always rooted in an Entity known as the "root" of the Aggregate. The root is responsible for maintaining
the integrity and consistency of the entire Aggregate.

### Direct Access Only to Root

External entities should access an Aggregate only through its root entity. This restriction ensures that the integrity
and business rules of the Aggregate are maintained.

### Consistency Boundary

Aggregates define a consistency boundary within which all changes must be consistent. This means that changes to the
internal state of the Aggregate (its entities and value objects) are performed through the root entity, ensuring that
business rules are enforced consistently.

### Atomic Transactions

Operations on Aggregates are typically treated as atomic transactions. Changes to the state of the Aggregate are either
fully applied or fully rejected, ensuring that the Aggregate is always in a valid and consistent state.

### Global Identity

Each Aggregate has a global identity represented by the identity of its root entity. This identity is used to uniquely
identify and reference the entire Aggregate.

### Encapsulation

Aggregates encapsulate internal details, hiding the complexity of their internal structure from external entities. This
encapsulation allows for changes to the internal implementation without affecting external entities.

### Domain Events

Domain Events are events that capture meaningful state changes within the domain. When integrated with Aggregates, Domain Events enhance the capability to communicate and react to changes effectively. The `HasDomainEvents` trait provides methods to easily implement Domain Events within your aggregates.

#### Registering Domain Events

```php
public static function create(int $number, array $customer): Order
{
    $created = Timestamp::now();
    $order = self::make(
        reference: Reference::fromTimestamp($created, $number),
        customer: new Customer(...$customer),
        lines: OrderLines::empty(),
        tags: new Tags(),
        created: $created
    );

    // Register domain event
    $order->registerDomainEvent(new OrderCreated($order));

    return $order;
}

public function addOrderLine(OrderLine $line): self
{
    $this->lines->add($line);

    // Register domain event for line addition
    $this->registerDomainEvent(new OrderLineAdded($this, $line));

    return $this;
}
```

The `registerDomainEvent()` method allows you to register events that implement the `Event` interface into the aggregate.

#### Publishing Domain Events

```php
// Publish all registered events to an event bus
$order->publishDomainEvents($eventBus);
```

**Key Points:**
- Events are registered during state changes
- Events are published in a batch to maintain transactional consistency
- The aggregate maintains a list of unpublished events
- Events should be published after successful persistence