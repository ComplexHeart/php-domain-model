# Aggregates

> Aggregate is a cluster of domain objects that can be treated as a single unit.\
> -- Martin Fowler

Creating a Value Object is quite easy you only need to use the Trait `IsAggregate` this will
add the `HasAttributes`, `HasInvariants`, `HasDomainEvents`, `HasIdentity` and `HasEquality` Traits.
In addition, you could use the `Aggregate` interface to expose the `publishDomainEvents` method.

The following example illustrates the implementation of these components.


 
