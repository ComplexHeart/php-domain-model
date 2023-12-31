# Aggregates

> Aggregate is a cluster of domain objects that can be treated as a single unit.\
> -- Martin Fowler

Creating a Value Object is quite easy you only need to use the Trait `IsAggregate` this will
add the `HasAttributes`, `HasInvariants`, `HasDomainEvents`, `HasIdentity` and `HasEquality` Traits.
In addition, you could use the `Aggregate` interface to expose the `publishDomainEvents` method.

## Example

The following example illustrates the implementation of these components.

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
        $this->check();
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

Domain Events are events that capture a meaningful state change within the domain. When integrated with Aggregates,
Domain Events enhance the capability to communicate and react to changes effectively. the `HasDomainEvents` trait
provides some methods to easy the implementation of Domain Events within your aggregates.

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
        $this->check();
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
```

The method `registerDomainEvent` allows you to register new events that implements the `Event` interface into the
aggregate.