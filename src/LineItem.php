<?php

namespace QuickCheckout;

use Illuminate\Support\Str;
use QuickCheckout\Contracts\ProductInterface;

/**
 * @mixin ProductInterface
 */
class LineItem
{
    protected ?ProductInterface $product = null;

    protected string $id;

    protected int $quantity = 1;

    /**
     * @param array|ProductInterface $product
     * @param int $quantity
     * @param string|null $id
     *
     * @throws \Exception
     */
    public function __construct(array|ProductInterface $product, int $quantity = 1, ?string $id = null)
    {
        $this->id = $id ?? (string) Str::uuid();

        $this->quantity = $quantity;
        if ($product instanceof ProductInterface) {
            $this->product = $product;
        } else {
            $this->product = CartProduct::fromArray($product);
        }
    }

    public function toArray(): array
    {
        return [
            'id'       => $this->id,
            'quantity' => $this->quantity,
            'product'  => $this->product()?->toArray(),
        ];
    }

    /**
     * Get product from session.
     *
     * @param array|null $data
     *
     * @return static|null
     * @throws \Exception
     */
    public static function fromArray(?array $data = null): ?static
    {
        if (is_array($data) &&
             !empty($data['product'])
        ) {
            return new static($data['product'], $data['quantity'] ?? 1, $data['id'] ?? null);
        }

        return null;
    }

    /**
     * Get Line item product.
     *
     * @return ProductInterface|null
     */
    public function product(): ?ProductInterface
    {
        return $this->product;
    }

    /**
     * Get quantity.
     *
     * @return int
     */
    public function quantity(): int
    {
        return $this->quantity;
    }

    /**
     * @return string
     */
    public function id(): string
    {
        return $this->id;
    }

    /**
     * @return int
     * @throws \Exception
     */
    public function total(): int
    {
        return $this->product()?->price() * $this->quantity();
    }

    /**
     * Propagate call to product
     *
     * @param string $method
     * @param array $arguments
     *
     * @return false|mixed
     * @throws \Exception
     */
    public function __call(string $method, array $arguments)
    {
        $product = $this->product();
        if ($product && method_exists($product, $method)) {
            return call_user_func_array([ ($this->product()), $method ], $arguments);
        }

        throw new \BadMethodCallException("Method [{$method}] not exists in class: " . __CLASS__);
    }
}
