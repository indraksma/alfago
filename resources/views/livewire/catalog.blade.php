<div>
    <section class="mx-auto max-w-7xl px-4 pt-6">
        @if($banners->isNotEmpty())
            <div x-data="{active:0,total:{{ $banners->count() }}}" x-init="setInterval(()=>active=(active+1)%total,5000)" class="relative mx-2 min-w-0 overflow-hidden rounded-2xl bg-slate-200 shadow-xl aspect-[4/3] sm:mx-0 sm:min-h-44 sm:rounded-3xl sm:aspect-[16/6]">
                @foreach($banners as $i=>$banner)<a href="{{ $banner->link_url ?: '#' }}" :class="active==={{$i}} ? 'z-10 opacity-100 pointer-events-auto' : 'z-0 opacity-0 pointer-events-none'" class="absolute inset-0 block transition-opacity duration-700 ease-in-out">
                    <img class="h-full w-full max-w-full object-cover opacity-75" src="{{ str_starts_with($banner->gambar,'http')?$banner->gambar:Storage::url($banner->gambar) }}" alt="{{ $banner->judul }}">
                    @if($banner->judul)<div class="absolute inset-0 flex items-end bg-gradient-to-t from-black/70 to-transparent p-4 sm:p-6"><h2 class="max-w-xl text-xl font-black text-white sm:text-4xl">{{ $banner->judul }}</h2></div>@endif
                </a>@endforeach
                <div class="absolute bottom-3 right-3 z-20 flex gap-1 sm:right-4">@foreach($banners as $i=>$banner)<button type="button" @click="active={{$i}}" :class="active==={{$i}}?'w-6 bg-white':'w-2 bg-white/50'" class="h-2 rounded-full transition-all"></button>@endforeach</div>
            </div>
        @else
            <div class="rounded-3xl bg-gradient-to-br from-red-600 to-red-800 p-8 text-white"><p class="font-bold text-yellow-300">JASTIP SEKOLAH</p><h1 class="mt-2 text-3xl font-black sm:text-5xl">Pesan favoritmu,<br>antar sampai kelas.</h1></div>
        @endif
        <div class="mt-6"><input wire:model.live.debounce.350ms="search" class="input !rounded-2xl !py-3.5" placeholder="Cari makanan, minuman, atau jajanan..."></div>
        <div class="mt-5 flex gap-2 overflow-x-auto pb-2">
            <button wire:click="selectCategory(null)" class="category-pill {{ $category===null?'active':'' }}">Semua</button>
            @foreach($categories as $cat)<button wire:click="selectCategory({{$cat->id}})" class="category-pill {{ $category===$cat->id?'active':'' }}"><span>{{ $cat->icon ?: '•' }}</span>{{ $cat->nama }}</button>@endforeach
        </div>
    </section>
    <section class="mx-auto max-w-7xl px-4 py-8"><div class="mb-5 flex items-end justify-between"><div><p class="text-sm font-bold text-red-600">PILIHAN HARI INI</p><h2 class="text-2xl font-black">Produk tersedia</h2></div><span class="text-sm text-slate-500">{{ $products->total() }} produk</span></div>
        <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5">
            @forelse($products as $product)<article class="product-card group">
                <a href="{{ route('products.show',$product) }}" wire:navigate class="block aspect-square overflow-hidden bg-slate-100">
                    @if($product->gambar)<img class="h-full w-full object-cover transition duration-300 group-hover:scale-105" src="{{ str_starts_with($product->gambar,'http')?$product->gambar:Storage::url($product->gambar) }}" alt="{{ $product->nama }}">@else<div class="grid h-full place-items-center text-5xl">🍽️</div>@endif
                </a>
                <div class="p-3"><p class="truncate text-[11px] font-bold uppercase tracking-wide text-slate-400">{{ $product->vendor->nama }}</p><a href="{{ route('products.show',$product) }}" wire:navigate class="mt-1 line-clamp-2 min-h-10 font-bold leading-tight hover:text-red-600">{{ $product->nama }}</a><p class="mt-2 font-black text-red-600">Rp{{ number_format($product->harga,0,',','.') }}</p>
                    <button wire:click="addToCart({{$product->id}})" class="mt-3 w-full rounded-xl bg-red-50 py-2 text-sm font-bold text-red-600 hover:bg-red-600 hover:text-white" @disabled($product->stok===0)>{{ $product->stok===0?'Habis':'+ Keranjang' }}</button>
                </div>
            </article>@empty<div class="col-span-full rounded-2xl border border-dashed p-12 text-center text-slate-500">Produk tidak ditemukan.</div>@endforelse
        </div><div class="mt-8">{{ $products->links() }}</div>
    </section>
</div>
