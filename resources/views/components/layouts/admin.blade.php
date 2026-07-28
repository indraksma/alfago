<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}"><title>{{ $title ?? 'Admin' }} — ALFAGO</title>
    @vite(['resources/css/app.css','resources/js/app.js']) @livewireStyles
</head>
<body class="bg-slate-100 text-slate-900">
<div class="min-h-screen lg:flex">
    <aside class="border-b bg-slate-950 text-white lg:fixed lg:inset-y-0 lg:w-64 lg:border-0">
        <div class="flex items-center justify-between px-5 py-5"><a href="{{ route('admin.dashboard') }}" wire:navigate class="text-2xl font-black text-red-500">ALFAGO</a><span class="rounded-full bg-red-600/20 px-2 py-1 text-xs text-red-300">Admin</span></div>
        <nav class="flex gap-1 overflow-x-auto px-3 pb-4 text-sm lg:block lg:space-y-1">
            <a class="admin-link" href="{{ route('admin.dashboard') }}" wire:navigate>Dashboard</a>
            <a class="admin-link" href="{{ route('admin.orders.index') }}" wire:navigate>Pesanan</a>
            @foreach(['kelas'=>'Kelas','vendors'=>'Vendor','categories'=>'Kategori','products'=>'Produk','banners'=>'Banner'] as $key=>$label)
                <a class="admin-link" href="{{ route('admin.crud',$key) }}" wire:navigate>{{ $label }}</a>
            @endforeach
            <a class="admin-link" href="{{ route('admin.shipping') }}" wire:navigate>Ongkir</a>
            <a class="admin-link" href="{{ route('catalog') }}" wire:navigate>Lihat Toko</a>
        </nav>
    </aside>
    <div class="min-w-0 flex-1 lg:ml-64">
        <header class="flex items-center justify-between border-b bg-white px-5 py-4"><div><p class="text-xs font-semibold uppercase tracking-widest text-red-600">Panel Admin</p><h1 class="text-xl font-bold">{{ $title ?? '' }}</h1></div><form method="POST" action="{{ route('logout') }}">@csrf<button class="btn-secondary">Keluar</button></form></header>
        @if(session('success'))<div class="mx-5 mt-5 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('success') }}</div>@endif
        <main class="p-5 lg:p-8">{{ $slot }}</main>
    </div>
</div>
@livewireScripts
</body>
</html>
