<?php

namespace QuickCheckout\Tests\Fixtures\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use QuickCheckout\Contracts\UsedAsCheckoutProduct;
use QuickCheckout\Eloquent\AsCheckoutProduct;

class Course extends Model implements UsedAsCheckoutProduct
{
    use AsCheckoutProduct;

    protected $table = 'courses';

    protected $guarded = [];

    public static function fake(array $atts = [])
    {
        return new static(array_merge([
            'title' => Str::random(),
            'price' => random_int(1, 999),
        ], $atts));
    }
}
