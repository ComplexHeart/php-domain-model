# Domain Model

[![Tests](https://github.com/ComplexHeart/php-domain-model/actions/workflows/test.yml/badge.svg)](https://github.com/ComplexHeart/php-domain-model/actions/workflows/test.yml)
[![Quality Gate Status](https://sonarcloud.io/api/project_badges/measure?project=ComplexHeart_php-domain-model&metric=alert_status)](https://sonarcloud.io/summary/new_code?id=ComplexHeart_php-domain-model)
[![Coverage](https://sonarcloud.io/api/project_badges/measure?project=ComplexHeart_php-domain-model&metric=coverage)](https://sonarcloud.io/summary/new_code?id=ComplexHeart_php-domain-model)

## Modeling Aggregates, Entities and Value Objects

Complex Heart allows you to model your domain Aggregates, Entities, and Value Objects using a set of traits. Great, but
why traits and not classes? Well, sometimes you have some kind of inheritance in your classes. Being forced to use a
certain base class is too invasive and personally, I don't like it. By using a set of traits and interfaces you have all
the functionality you need without compromising the essence of your own domain.

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

For more information please check the wiki.
