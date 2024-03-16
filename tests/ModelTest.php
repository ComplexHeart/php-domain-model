<?php

use ComplexHeart\Domain\Model\Test\OrderManagement\Domain\Price;

test('Model values should be mapped by custom function sucessfully', function() {
    $price = new Price(10, 'AUD');

    $values = $price->values(fn($attribute) => "-->$attribute");

    expect($values['amount'])->toStartWith('-->');
    expect($values['currency'])->toStartWith('-->');
})
    ->group('Unit');
