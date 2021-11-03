<?php

namespace QuickCheckout;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Arr;
use QuickCheckout\Contracts\ProductInterface;

abstract class CartProduct implements ProductInterface
{
    protected string $title = '';

    protected int $price = 0;

    protected array $meta = [];

    /**
     * @param string $title
     * @param int $price
     * @param array $meta
     */
    public function __construct(string $title, int $price, array $meta = [])
    {
        $this->title = $title;
        $this->price = $price;
        $this->meta  = $meta;
    }

    /**
     * @inerhitDoc
     */
    public static function fromArray(array $data): ?ProductInterface
    {
        $class = (string) ($data['class'] ?? '');
        if ($class && is_a($class, ProductInterface::class, true)) {
            return new $class(
                (string) ($data['title'] ?? ''),
                (int) ($data['price'] ?? 0),
                (array) ($data['meta'] ?? [])
            );
        }

        return null;
    }

    public function toArray(): array
    {
        return [
            'class' => get_class($this),
            'title' => $this->title,
            'price' => $this->price,
            'meta'  => $this->meta,
        ];
    }

    /**
     * @inerhitDoc
     */
    public function title(): string
    {
        return $this->title;
    }

    /**
     * @inerhitDoc
     */
    public function price(): int
    {
        return $this->price;
    }

    /**
     * @inerhitDoc
     */
    public function meta(?string $key = null, mixed $default = null): mixed
    {
        if (!is_null($key)) {
            return Arr::get($this->meta, $key, $default);
        }

        return $this->meta;
    }

    /**
     * @inerhitDoc
     * @psalm-suppress InvalidMethodCall
     */
    public function checkoutEntity(): ?Model
    {
        if (!empty($class = $this->meta('entity_type')) &&
             !empty($key = $this->meta('entity_id'))
        ) {
            $class = Arr::get(Relation::morphMap() ?: [], $class, $class);
            if (is_a($class, Model::class, true)) {
                if (method_exists($class, 'checkoutProductFromId')) {
                    return $class::checkoutProductFromId($key);
                }

                return $class::find($key);
            }
        }

        return null;
    }

    public static function getModelDataAsEntity(Model $model): array
    {
        return [
            'entity_type' => $model->getMorphClass(),
            'entity_id'   => $model->getKey(),
        ];
    }
}
