<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\Product;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class CartService
{
    public function add(User $user, Product $product, int $qty = 1): void
    {
        $product->loadMissing('vendor', 'category');
        if (! $product->isAvailable()) {
            throw ValidationException::withMessages(['cart' => 'Produk sedang tidak tersedia.']);
        }

        $cart = Cart::firstOrCreate(['user_id' => $user->id]);
        $item = $cart->items()->firstOrNew(['product_id' => $product->id]);
        $newQty = ($item->exists ? $item->qty : 0) + max(1, $qty);

        if ($product->stok !== null && $newQty > $product->stok) {
            throw ValidationException::withMessages(['cart' => "Stok {$product->nama} hanya tersisa {$product->stok}."]);
        }

        $item->qty = $newQty;
        $item->save();
    }
}
