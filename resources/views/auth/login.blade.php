<x-layouts.app title="Masuk">
    <div class="mx-auto max-w-md px-4 py-16">
        <div class="panel p-7"><h1 class="text-2xl font-black">Masuk ke ALFAGO</h1><p class="mt-1 text-sm text-slate-500">Lanjutkan belanja dan pantau pesananmu.</p>
            <form method="POST" action="{{ route('login') }}" class="mt-6 space-y-4">@csrf
                <div><label class="label">Email</label><input class="input" type="email" name="email" value="{{ old('email') }}" required autofocus>@error('email')<p class="error">{{ $message }}</p>@enderror</div>
                <div><label class="label">Password</label><input class="input" type="password" name="password" required></div>
                <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="remember" class="rounded border-slate-300 text-red-600"> Ingat saya</label>
                <button class="btn-primary w-full">Masuk</button>
            </form>
            <p class="mt-5 text-center text-sm text-slate-500">Belum punya akun? <a class="font-bold text-red-600" href="{{ route('register') }}">Daftar</a></p>
        </div>
    </div>
</x-layouts.app>
