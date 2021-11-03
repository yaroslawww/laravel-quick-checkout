<?php

namespace QuickCheckout;

use Illuminate\Session\Store;
use Illuminate\Support\Collection;
use QuickCheckout\Contracts\ProductInterface;

class Cart
{

    /**
     * Session store
     */
    private Store $session;

    /**
     * Key name to save in session.
     *
     * @var string
     */
    private string $sessionKey;

    /**
     * Cart validator
     */
    private CartValidator $validator;

    /**
     * Cart line items.
     *
     * @var Collection
     */
    protected Collection $lineItems;

    /**
     * @param Store $session
     * @param string $sessionKey
     * @param CartValidator $validator
     */
    public function __construct(Store $session, string $sessionKey, CartValidator $validator)
    {
        $this->purge();
        $this->session    = $session;
        $this->sessionKey = $sessionKey;
        $this->validator  = $validator;
    }

    /**
     * @return Store
     */
    public function getSession(): Store
    {
        return $this->session;
    }

    /**
     * @return string
     */
    public function getSessionKey(): string
    {
        return $this->sessionKey;
    }

    /**
     * @return CartValidator
     */
    public function getValidator(): CartValidator
    {
        return $this->validator;
    }

    /**
     * @param CartValidator $validator
     *
     * @return static
     */
    public function setValidator(CartValidator $validator): static
    {
        $this->validator = $validator;

        return $this;
    }

    /**
     * Lofty add line item to cart.
     *
     * @param LineItem $lineItem
     * @param bool $override
     *
     * @return static
     * @throws \Exception
     */
    public function addLineItem(LineItem $lineItem, bool $strict = false): static
    {
        if (!$this->validator->lineItemCanBeAdded($this, $lineItem)) {
            if ($strict) {
                throw new \Exception('Line item can;t be added');
            } else {
                return $this;
            }
        }
        $this->lineItems->push($lineItem);

        return $this;
    }

    /**
     * Crete line item from product
     * @param array|ProductInterface $product
     * @param int $quantity
     * @param string|null $id
     *
     * @return $this
     * @throws \Exception
     */
    public function withLineItem(array|ProductInterface $product, int $quantity = 1, ?string $id = null): static
    {
        return $this->addLineItem(Checkout::makeLineItem($product, $quantity, $id));
    }

    /**
     * Convert class to array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'line_items' => $this->lineItems->map(fn (LineItem $i) => $i->toArray())->toArray(),
        ];
    }

    /**
     * Initialise object from array.
     */
    public function fromArray(array $cart): static
    {
        $this->purge();
        if (!empty($cart['line_items']) && is_array($cart['line_items'])) {
            foreach ($cart['line_items'] as $data) {
                if ($li = LineItem::fromArray($data)) {
                    $this->addLineItem($li, true);
                }
            }
        }

        return $this;
    }

    /**
     * Put product to session.
     */
    public function putToSession(): static
    {
        $this->session->put($this->sessionKey, $this->toArray());

        return $this;
    }

    /**
     * Get cart from session.
     *
     * @param bool $forget
     *
     * @return static|null
     * @throws \Exception
     */
    public function fromSession(bool $forget = false): ?static
    {
        $cart = $this->session->get($this->sessionKey);

        if ($forget) {
            $this->session->forget($this->sessionKey);
        }
        if (is_array($cart)) {
            return $this->fromArray($cart);
        }

        return null;
    }

    /**
     * @return bool
     */
    public function isEmpty(): bool
    {
        return $this->lineItems->isEmpty();
    }

    /**
     * @return Collection
     */
    public function lineItems(): Collection
    {
        return $this->lineItems;
    }

    /**
     * @return int
     * @throws \Exception
     */
    public function total(): int
    {
        return (int) $this->lineItems->reduce(fn ($carry, LineItem $item) => $carry + $item->total(), 0);
    }

    /**
     * Forget cart from session.
     *
     * @return static
     */
    public function forget(): static
    {
        $this->session->forget($this->sessionKey);

        return $this;
    }

    /**
     * Forget cart from session.
     *
     * @return static
     */
    public function purge(): static
    {
        $this->lineItems = collect();

        return $this;
    }
}
