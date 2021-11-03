<?php

namespace QuickCheckout\Tests\Fixtures\Models;

class CourseWithRestoreMethod extends Course
{
    public function checkoutProductGeneralMeta(): array
    {
        return [
            'foo' => 'bar',
        ];
    }

    public static function checkoutProductFromId($id)
    {
        return static::find($id);
    }
}
