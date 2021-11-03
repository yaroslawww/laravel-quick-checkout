<?php

namespace QuickCheckout\Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use QuickCheckout\CartProduct;
use QuickCheckout\Product;
use QuickCheckout\Tests\Fixtures\Models\Course;
use QuickCheckout\Tests\Fixtures\Models\CourseWithRestoreMethod;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function product_to_array()
    {
        $product = new Product('My product', 123, [
            'my_key' => 'my value',
        ]);

        $data = $product->toArray();

        $this->assertIsArray($data);
        $this->assertEquals(get_class($product), $data['class']);
        $this->assertEquals('My product', $data['title']);
        $this->assertEquals(123, $data['price']);
        $this->assertEquals('my value', $data['meta']['my_key']);
    }

    /** @test */
    public function product_from_array()
    {
        $product = CartProduct::fromArray([]);
        $this->assertNull($product);

        $product = CartProduct::fromArray([
            'class' => Product::class,
            'title' => 'Prod title',
            'price' => 564,
            'meta'  => [
                'foo'  => 'var',
                'foo2' => [
                    'sec_level' => 'bar',
                ],
                'foo3' => [
                    'bar1',
                    'bar2',
                    'bar3',
                    'bar4',
                ],
            ],

        ]);

        $this->assertInstanceOf(Product::class, $product);
        $this->assertEquals('Prod title', $product->title());
        $this->assertEquals(564, $product->price());
        $this->assertEquals('var', $product->meta()['foo']);
        $this->assertEquals('var', $product->meta('foo'));
        $this->assertEquals('bar', $product->meta('foo2.sec_level'));
        $this->assertEquals('bar3', $product->meta('foo3.2'));

        $this->assertNull($product->checkoutEntity());
    }

    /** @test */
    public function product_with_entity()
    {
        $course = Course::fake();
        $course->save();

        $product = $course->toCheckoutProduct([
            'foo' => 'bar',
        ]);

        $restoredProduct = CartProduct::fromArray($product->toArray());

        $entity = $restoredProduct->checkoutEntity();

        $this->assertInstanceOf(Course::class, $entity);
        $this->assertEquals($course->getKey(), $entity->getKey());
        $this->assertEquals($course->getKey(), $product->meta('entity_id'));
        $this->assertEquals('bar', $product->meta('foo'));
    }

    /** @test */
    public function product_with_entity_custom_restore_method()
    {
        $course = CourseWithRestoreMethod::fake();
        $course->save();

        $product = $course->toCheckoutProduct([
            'foo' => 'bar2',
        ]);

        $restoredProduct = CartProduct::fromArray($product->toArray());

        $entity = $restoredProduct->checkoutEntity();

        $this->assertInstanceOf(CourseWithRestoreMethod::class, $entity);
        $this->assertEquals($course->getKey(), $entity->getKey());
        $this->assertEquals($course->getKey(), $product->meta('entity_id'));
        $this->assertEquals('bar2', $product->meta('foo'));
    }
}
