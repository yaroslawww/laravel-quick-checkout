<?php

namespace QuickCheckout;

class CartValidator
{

    /**
     * Check is line item can be added to cart.
     *
     * @param Cart $cart
     * @param LineItem $lineItem
     *
     * @return bool
     */
    public function lineItemCanBeAdded(Cart $cart, LineItem $lineItem): bool
    {
        return true;
    }
}
