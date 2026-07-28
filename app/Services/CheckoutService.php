<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Models\Kelas;
use App\Models\Order;
use App\Models\Product;
use App\Models\ShippingSetting;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CheckoutService
{
    public function create(User $user, int $kelasId, string $payment, ?string $note): Order
    {
        return DB::transaction(function () use ($user, $kelasId, $payment, $note) {
            $kelas = Kelas::whereKey($kelasId)->where('is_active', true)->firstOrFail();
            $cart = $user->cart()->with('items.product.vendor', 'items.product.category')->lockForUpdate()->first();
            if (! $cart || $cart->items->isEmpty()) {
                throw ValidationException::withMessages(['cart' => 'Keranjang masih kosong.']);
            }

            $products = Product::with('vendor', 'category')
                ->whereIn('id', $cart->items->pluck('product_id'))
                ->lockForUpdate()->get()->keyBy('id');

            $subtotal = 0;
            foreach ($cart->items as $item) {
                $product = $products->get($item->product_id);
                if (! $product?->isAvailable() || ($product->stok !== null && $item->qty > $product->stok)) {
                    throw ValidationException::withMessages(['cart' => "Stok {$item->product->nama} berubah. Periksa keranjang kembali."]);
                }
                $subtotal += (float) $product->harga * $item->qty;
            }

            $shipping = ShippingSetting::current();
            $order = Order::create([
                'user_id' => $user->id,
                'kelas_id' => $kelas->id,
                'kode_pesanan' => 'TMP-'.Str::uuid(),
                'subtotal' => $subtotal,
                'ongkir' => $shipping->nominal,
                'total' => $subtotal + (float) $shipping->nominal,
                'metode_pembayaran' => PaymentMethod::from($payment),
                'status' => OrderStatus::Pending,
                'catatan' => filled($note) ? trim($note) : null,
            ]);
            $order->update(['kode_pesanan' => 'ALF-'.now()->format('Ymd').'-'.str_pad((string) $order->id, 4, '0', STR_PAD_LEFT)]);

            foreach ($cart->items as $item) {
                $product = $products[$item->product_id];
                $lineSubtotal = (float) $product->harga * $item->qty;
                $order->items()->create([
                    'product_id' => $product->id, 'vendor_id' => $product->vendor_id,
                    'nama_produk' => $product->nama, 'harga' => $product->harga,
                    'qty' => $item->qty, 'subtotal' => $lineSubtotal,
                ]);
                if ($product->stok !== null) {
                    $product->decrement('stok', $item->qty);
                }
            }

            $cart->items()->delete();
            $user->update(['kelas_id' => $kelas->id]);
            return $order->fresh(['items.vendor', 'kelas']);
        }, 3);
    }
}
