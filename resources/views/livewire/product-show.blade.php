<div class="mx-auto max-w-5xl px-4 py-8">
    <a href="{{ route('catalog') }}" wire:navigate class="text-sm font-bold text-slate-500 hover:text-red-600">← Kembali belanja</a>
    <div class="mt-4 grid overflow-hidden rounded-3xl bg-white shadow-sm ring-1 ring-slate-200 md:grid-cols-2">
        <div class="aspect-square bg-slate-100">@if($product->gambar)<img class="h-full w-full object-cover" src="{{ str_starts_with($product->gambar,'http')?$product->gambar:Storage::url($product->gambar) }}">@else<div class="grid h-full place-items-center text-8xl">🍽️</div>@endif</div>
        <div class="flex flex-col p-6 md:p-10"><span class="w-fit rounded-full bg-yellow-100 px-3 py-1 text-xs font-bold text-yellow-800">{{ $product->category->nama }}</span><h1 class="mt-4 text-3xl font-black">{{ $product->nama }}</h1><p class="mt-2 text-sm font-semibold text-slate-500">{{ $product->vendor->nama }} · {{ strtoupper($product->vendor->tipe->value) }}</p><p class="mt-6 text-3xl font-black text-red-600">Rp{{ number_format($product->harga,0,',','.') }}</p><p class="mt-5 leading-relaxed text-slate-600">{{ $product->deskripsi ?: 'Produk pilihan untuk menemani aktivitas sekolahmu.' }}</p>
            <div class="mt-auto pt-8"><p class="mb-2 text-sm text-slate-500">Stok: {{ $product->stok===null?'Tersedia':$product->stok }}</p><div class="flex gap-3"><input wire:model="qty" type="number" min="1" class="input w-24"><button wire:click="add" class="btn-primary flex-1" @disabled($product->stok===0)>Tambah ke Keranjang</button></div>@error('cart')<p class="error">{{ $message }}</p>@enderror</div>
        </div>
    </div>
</div>
