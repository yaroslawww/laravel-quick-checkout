<?php

return [

    'session_key' => 'quick-checkout.cart',

    'validator' => \QuickCheckout\CartValidator::class,
    'cart'      => \QuickCheckout\Cart::class,
    'line_item' => \QuickCheckout\LineItem::class,

];
