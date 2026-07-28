<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'ALFAGO' }} — ALFAGO</title>
    @vite(['resources/css/app.css','resources/js/app.js'])
    @livewireStyles
</head>
<body class="min-h-screen bg-slate-50 text-slate-900">
    <header class="sticky top-0 z-40 border-b border-slate-200 bg-white/95 backdrop-blur">
        <div class="mx-auto flex max-w-7xl items-center gap-3 px-4 py-3">
            <a href="{{ route('catalog') }}" wire:navigate class="flex shrink-0 items-center gap-2">
                <span class="grid h-10 w-10 place-items-center rounded-xl bg-red-600 text-xl font-black text-white shadow-lg shadow-red-200">A</span>
                <span class="hidden text-xl font-black tracking-tight text-red-600 sm:block">ALFAGO</span>
            </a>
            <div class="ml-auto flex items-center gap-2">
                <a href="{{ route('cart') }}" wire:navigate class="relative rounded-xl p-2.5 text-slate-600 hover:bg-red-50 hover:text-red-600" aria-label="Keranjang">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13 5.4 5M7 13l-2 2h13m-9 4a1 1 0 1 1-2 0 1 1 0 0 1 2 0Zm10 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0Z"/></svg>
                    @auth
                        @php($cartCount = auth()->user()->cart?->items()->sum('qty') ?? 0)
                        @if($cartCount)<span class="absolute right-0 top-0 grid h-5 min-w-5 place-items-center rounded-full bg-red-600 px-1 text-[10px] font-bold text-white">{{ $cartCount }}</span>@endif
                    @endauth
                </a>
                @auth
                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" wire:navigate class="btn-secondary hidden sm:inline-flex">Admin</a>
                    @else
                        <a href="{{ route('orders.index') }}" wire:navigate class="btn-secondary hidden sm:inline-flex">Pesanan</a>
                    @endif
                    <form method="POST" action="{{ route('logout') }}">@csrf<button class="rounded-xl p-2.5 text-slate-600 hover:bg-slate-100" title="Keluar"><svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" d="M15 12H3m8-4 4 4-4 4m4-13h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/></svg></button></form>
                @else
                    <a href="{{ route('login') }}" class="btn-secondary">Masuk</a>
                    <a href="{{ route('register') }}" class="btn-primary hidden sm:inline-flex">Daftar</a>
                @endauth
            </div>
        </div>
    </header>
    @if(session('success'))<div class="mx-auto mt-4 max-w-7xl px-4"><div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800">{{ session('success') }}</div></div>@endif
    <main>{{ $slot }}</main>
    <footer class="mt-16 border-t border-slate-200 bg-white"><div class="mx-auto max-w-7xl px-4 py-8 text-center text-sm text-slate-500">ALFAGO · Marketplace jastip internal sekolah</div></footer>
    @livewireScripts
</body>
</html>
