<?php

namespace QuickCheckout\Tests;

use QuickCheckout\LineItem;
use QuickCheckout\Tests\Fixtures\Models\Course;

class LineItemTest extends TestCase
{

    /** @test */
    public function from_array_return_null_if_not_valid_data()
    {
        $this->assertNull(LineItem::fromArray());
        $this->assertNull(LineItem::fromArray([]));
    }

    /** @test */
    public function from_array()
    {
        $course = Course::fake([ 'price' => 25 ]);
        $course->save();

        $lineItem = LineItem::fromArray([
            'product'  => $course->toCheckoutProduct()->toArray(),
            'quantity' => 2,
            'id'       => 'foo-id',
        ]);

        $this->assertEquals('foo-id', $lineItem->id());
        $this->assertEquals(50, $lineItem->total());
        $this->assertEquals(2, $lineItem->quantity());
        $this->assertEquals($course->getKey(), $lineItem->meta('entity_id'));

        $this->expectException(\BadMethodCallException::class);
        $lineItem->foo_method();
    }
}
