<?php
namespace App\Livewire;
use App\Models\CartItem;
use Livewire\Component;
class CartPage extends Component {
    public function changeQty(int $id, int $delta): void {
        $item = CartItem::whereHas('cart', fn($q)=>$q->where('user_id', auth()->id()))->with('product')->findOrFail($id);
        $qty = $item->qty + $delta;
        if ($qty < 1) { $item->delete(); }
        elseif ($item->product->stok !== null && $qty > $item->product->stok) { $this->addError('cart', 'Jumlah melebihi stok tersedia.'); }
        else { $item->update(['qty'=>$qty]); }
        $this->dispatch('cart-updated');
    }
    public function remove(int $id): void { CartItem::whereHas('cart', fn($q)=>$q->where('user_id', auth()->id()))->findOrFail($id)->delete(); $this->dispatch('cart-updated'); }
    public function render() {
        $items = auth()->user()->cart?->items()->with('product.vendor')->get() ?? collect();
        return view('livewire.cart-page', ['items'=>$items, 'subtotal'=>$items->sum(fn($i)=>(float)$i->product->harga*$i->qty)])->layout('components.layouts.app',['title'=>'Keranjang']);
    }
}
