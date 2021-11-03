<?php

namespace QuickCheckout\Contracts;

use Illuminate\Database\Eloquent\Model;

interface ProductInterface
{
    public static function fromArray(array $data): ?ProductInterface;

    public function toArray(): array;

    public function title(): string;

    public function price(): int;

    public function meta(?string $key = null, mixed $default = null): mixed;

    public function checkoutEntity(): ?Model;
}
