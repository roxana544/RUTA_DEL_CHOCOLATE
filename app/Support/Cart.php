<?php

namespace App\Support;

use App\Models\Product;
use Illuminate\Support\Collection;

/**
 * Session cart: stores [product_id => quantity].
 */
class Cart
{
    private const KEY = 'cart';

    public function items(): array
    {
        return session(self::KEY, []);
    }

    public function count(): int
    {
        return array_sum($this->items());
    }

    public function set(Product $product, int $quantity): void
    {
        $items = $this->items();

        if ($quantity <= 0) {
            unset($items[$product->id]);
        } else {
            $items[$product->id] = min($quantity, $product->stock);
        }

        session([self::KEY => $items]);
    }

    public function add(Product $product, int $quantity = 1): void
    {
        $this->set($product, ($this->items()[$product->id] ?? 0) + $quantity);
    }

    /**
     * @return Collection<int, object{product: Product, quantity: int, subtotal: float}>
     */
    public function lines(): Collection
    {
        $items = $this->items();

        return Product::with('factory')->active()->whereIn('id', array_keys($items))->get()
            ->map(fn (Product $p) => (object) [
                'product' => $p,
                'quantity' => $items[$p->id],
                'subtotal' => $items[$p->id] * (float) $p->price,
            ]);
    }

    public function total(): float
    {
        return $this->lines()->sum('subtotal');
    }

    public function clear(): void
    {
        session()->forget(self::KEY);
    }
}
