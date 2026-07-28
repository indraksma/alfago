<?php

namespace App\Livewire;

use App\Models\Banner;
use App\Models\Category;
use App\Models\Product;
use App\Services\CartService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class Catalog extends Component
{
    use WithPagination;
    #[Url] public string $search = '';
    #[Url] public ?int $category = null;

    public function updatedSearch(): void { $this->resetPage(); }
    public function selectCategory(?int $id): void { $this->category = $id; $this->resetPage(); }

    public function addToCart(int $productId, CartService $cart): mixed
    {
        if (! Auth::check()) return $this->redirectRoute('login', navigate: true);
        $cart->add(Auth::user(), Product::findOrFail($productId));
        session()->flash('success', 'Produk ditambahkan ke keranjang.');
        $this->dispatch('cart-updated');
        return null;
    }

    public function render()
    {
        $products = Product::with('vendor', 'category')
            ->where('is_active', true)->whereHas('vendor', fn ($q) => $q->where('is_active', true))
            ->whereHas('category', fn ($q) => $q->where('is_active', true))
            ->when($this->search, fn ($q) => $q->where(fn ($query) => $query->where('nama', 'like', "%{$this->search}%")->orWhere('deskripsi', 'like', "%{$this->search}%")))
            ->when($this->category, fn ($q) => $q->where('category_id', $this->category))
            ->latest()->paginate(12);
        $today = today();
        $banners = Banner::where('is_active', true)->where(fn ($q) => $q->whereNull('tanggal_mulai')->orWhere('tanggal_mulai', '<=', $today))
            ->where(fn ($q) => $q->whereNull('tanggal_selesai')->orWhere('tanggal_selesai', '>=', $today))->orderBy('urutan')->get();
        return view('livewire.catalog', ['products' => $products, 'categories' => Category::where('is_active', true)->orderBy('nama')->get(), 'banners' => $banners])
            ->layout('components.layouts.app', ['title' => 'Belanja']);
    }
}
