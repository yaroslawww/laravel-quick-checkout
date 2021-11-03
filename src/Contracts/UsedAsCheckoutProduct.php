<?php

namespace QuickCheckout\Contracts;

use Illuminate\Database\Eloquent\Model;

/**
 * @mixin Model
 */
interface UsedAsCheckoutProduct
{
    public function toCheckoutProduct(array $meta = []): ProductInterface;

    public function checkoutProductTitle(): string;

    public function checkoutProductPrice(): int;

    public function checkoutProductMeta(): array;
}
