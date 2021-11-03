<?php

namespace QuickCheckout;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Session\Store;
use QuickCheckout\Contracts\ProductInterface;

class CheckoutManager
{

    /**
     * The application instance.
     */
    protected Application $app;

    protected ?Cart $cart = null;

    /**
     * Create a new instance.
     *
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function cart(): Cart
    {
        if ($this->cart) {
            return $this->cart;
        }

        return $this->cart = $this->app->make($this->app['config']->get('quick-checkout.cart'), [
            'session'    => $this->app->make(Store::class),
            'sessionKey' => $this->app['config']->get('quick-checkout.session_key'),
            'validator'  => $this->app->make($this->app['config']->get('quick-checkout.validator')),
        ]);
    }

    public function makeLineItem(array|ProductInterface $product, int $quantity = 1, ?string $id = null): LineItem
    {
        return $this->app->make($this->app['config']->get('quick-checkout.line_item'), [
            'product'  => $product,
            'quantity' => $quantity,
            'id'       => $id,
        ]);
    }
}
