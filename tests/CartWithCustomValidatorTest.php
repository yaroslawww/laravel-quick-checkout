<?php

namespace QuickCheckout\Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use QuickCheckout\Checkout;
use QuickCheckout\Product;
use QuickCheckout\Tests\Fixtures\CustomCartValidator;

class CartWithCustomValidatorTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function with_validator()
    {
        $cart = Checkout::cart()->setValidator(new CustomCartValidator())->purge();

        $cart->addLineItem(Checkout::makeLineItem(new Product('Not Valid', 25)));

        $this->assertCount(0, $cart->lineItems());

        $cart->addLineItem(Checkout::makeLineItem(new Product('Valid', 25), 2));

        $this->assertCount(1, $cart->lineItems());

        $this->expectException(\Exception::class);
        $cart->addLineItem(Checkout::makeLineItem(new Product('Not Valid', 25)), true);
    }
}
