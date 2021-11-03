<?php

namespace QuickCheckout\Eloquent;

use QuickCheckout\CartProduct;
use QuickCheckout\Contracts\ProductInterface;
use QuickCheckout\Product;

trait AsCheckoutProduct
{
    public function checkoutProductTitle(): string
    {
        return (string) $this->title;
    }

    public function checkoutProductPrice(): int
    {
        return (int) $this->price;
    }

    public function checkoutProductMeta(): array
    {
        return array_merge(CartProduct::getModelDataAsEntity($this), $this->checkoutProductGeneralMeta());
    }

    public function checkoutProductGeneralMeta(): array
    {
        return [];
    }

    public function checkoutProductClass(): string
    {
        return Product::class;
    }

    public function toCheckoutProduct(array $meta = []): ProductInterface
    {
        $class = $this->checkoutProductClass();

        return new $class(
            $this->checkoutProductTitle(),
            $this->checkoutProductPrice(),
            array_merge($this->checkoutProductMeta(), $meta),
        );
    }
}
