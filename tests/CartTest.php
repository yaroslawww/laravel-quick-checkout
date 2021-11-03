<?php

namespace QuickCheckout\Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use QuickCheckout\CartValidator;
use QuickCheckout\Product;
use QuickCheckout\Tests\Fixtures\Models\Course;
use QuickCheckout\Tests\Fixtures\Models\CourseWithRestoreMethod;

class CartTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function cart_manipulation()
    {
        $course = Course::fake([ 'price' => 25 ]);
        $course->save();
        $course2 = CourseWithRestoreMethod::fake([ 'price' => 30 ]);
        $course2->save();

        $cart = \QuickCheckout\Checkout::cart()->purge()
                                       ->withLineItem($course->toCheckoutProduct(), 2)
                                       ->withLineItem($course2->toCheckoutProduct(), 3, 'my_test_id')
                                       ->withLineItem(new Product('My product', 123), 4);


        $this->assertCount(3, $cart->lineItems());
        $this->assertEquals(25 * 2 + 30 * 3 + 123 * 4, $cart->total());
        $this->assertFalse($cart->isEmpty());

        $cart->purge();

        $this->assertTrue($cart->isEmpty());
        $this->assertCount(0, $cart->lineItems());
        $this->assertEquals(0, $cart->total());
    }

    /** @test */
    public function cart_put_to_session()
    {
        $course = Course::fake([ 'price' => 25 ]);
        $course->save();
        $course2 = CourseWithRestoreMethod::fake([ 'price' => 30 ]);
        $course2->save();

        $cart = \QuickCheckout\Checkout::cart()->purge()
                                       ->withLineItem($course->toCheckoutProduct(), 2)
                                       ->withLineItem($course2->toCheckoutProduct(), 3, 'my_test_id')
                                       ->withLineItem(new Product('My product', 123), 4);


        $cart->putToSession();

        $cartRestored = \QuickCheckout\Checkout::cart()->fromSession();

        $sessionStore = $cartRestored->getSession();
        $sessionKey   = $cartRestored->getSessionKey();
        $this->assertInstanceOf(CartValidator::class, $cart->getValidator());
        $this->assertTrue($sessionStore->exists($sessionKey));
        \QuickCheckout\Checkout::cart()->forget();
        $this->assertFalse($sessionStore->exists($sessionKey));
    }

    /** @test */
    public function null_if_cart_not_in_session()
    {
        $this->assertNull(\QuickCheckout\Checkout::cart()->fromSession());
    }

    /** @test */
    public function restore_from_session()
    {
        $cart = \QuickCheckout\Checkout::cart()->purge()
                                       ->withLineItem(new Product('My product', 123), 4);

        $cart->putToSession();
        $cart->purge();
        $this->assertTrue($cart->isEmpty());

        $cart->fromSession(true);
        $this->assertFalse($cart->isEmpty());
    }
}
