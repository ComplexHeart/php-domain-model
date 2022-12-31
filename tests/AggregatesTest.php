<?php

declare(strict_types=1);

use ComplexHeart\Contracts\Domain\ServiceBus\Event;
use ComplexHeart\Contracts\Domain\ServiceBus\EventBus;
use ComplexHeart\Domain\Model\Test\Sample\Models\Order;

test('Aggregate should register domain event successfully.', function () {
    Order::create(1, 'Vincent Vega')->publishDomainEvents(mock(EventBus::class)->expect(
        publish: function (Event ...$events) {
            expect($events)->toHaveCount(1);
            foreach ($events as $event) {
                expect($event)->toBeInstanceOf(Event::class);
            }
        }
    ));
})
    ->group('Unit');

test('Aggregate should has identity based in identifier.', function () {
    $order1 = Order::create(1, 'Vincent Vega');
    $order2 = Order::create(1, 'Vincent Vega');
    $order3 = Order::create(2, 'Vincent Vega');

    expect($order1->equals($order2))->toBeTrue();
    expect($order1->equals($order3))->toBeFalse();
    expect($order1->equals(new stdClass()))->toBeFalse();
})
    ->group('Unit');

test('Aggregate should create new instance with new values.', function () {
    expect(Order::create(1, 'Vincent Vega')->withName('Jules')->name)
        ->toEqual('Jules');
})
    ->group('Unity');

test('Aggregate should return correct debug information.', function () {
    $order = Order::create(1, 'Vincent Vega');

    expect($order->__debugInfo())->toHaveKeys([
        'reference',
        'name',
        'lines',
        'created',
        'domainEvents'
    ]);
});
