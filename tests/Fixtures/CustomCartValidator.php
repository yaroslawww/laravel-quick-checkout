<?php

namespace QuickCheckout\Tests\Fixtures;

use QuickCheckout\Cart;
use QuickCheckout\CartValidator;
use QuickCheckout\LineItem;

class CustomCartValidator extends CartValidator
{

    /**
     * Check is line item can be added to cart.
     *
     * @param Cart $cart
     * @param LineItem $lineItem
     *
     * @return bool
     * @throws \Exception
     */
    public function lineItemCanBeAdded(Cart $cart, LineItem $lineItem): bool
    {
        return $lineItem->total() > 30;
    }
}
