<?php
namespace App\Livewire;
use App\Models\Product;
use App\Services\CartService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
class ProductShow extends Component {
    public Product $product;
    public int $qty = 1;
    public function mount(Product $product): void { $product->load('vendor', 'category'); abort_unless($product->is_active && $product->vendor->is_active && $product->category->is_active, 404); $this->product = $product; }
    public function add(CartService $cart): mixed { if (!Auth::check()) return $this->redirectRoute('login', navigate:true); $cart->add(Auth::user(), $this->product, $this->qty); session()->flash('success','Produk ditambahkan ke keranjang.'); $this->dispatch('cart-updated'); return null; }
    public function render() { return view('livewire.product-show')->layout('components.layouts.app', ['title'=>$this->product->nama]); }
}
