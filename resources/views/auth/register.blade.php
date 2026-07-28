<x-layouts.app title="Daftar">
    <div class="mx-auto max-w-md px-4 py-12"><div class="panel p-7"><h1 class="text-2xl font-black">Buat akun</h1><p class="mt-1 text-sm text-slate-500">Daftar sebagai warga sekolah untuk mulai memesan.</p>
        <form method="POST" action="{{ route('register') }}" class="mt-6 space-y-4">@csrf
            <div><label class="label">Nama lengkap</label><input class="input" name="name" value="{{ old('name') }}" required>@error('name')<p class="error">{{ $message }}</p>@enderror</div>
            <div><label class="label">Email</label><input class="input" type="email" name="email" value="{{ old('email') }}" required>@error('email')<p class="error">{{ $message }}</p>@enderror</div>
            <div><label class="label">Nomor HP</label><input class="input" type="tel" name="phone" value="{{ old('phone') }}" inputmode="tel" autocomplete="tel" placeholder="Contoh: 081234567890" required>@error('phone')<p class="error">{{ $message }}</p>@enderror<p class="mt-1 text-xs text-slate-500">Digunakan admin untuk menghubungi Anda terkait pengantaran.</p></div>
            <div><label class="label">Kelas default (opsional)</label><select class="input" name="kelas_id"><option value="">Pilih kelas</option>@foreach($kelas as $item)<option value="{{ $item->id }}" @selected(old('kelas_id')==$item->id)>{{ $item->nama }}</option>@endforeach</select></div>
            <div><label class="label">Password</label><input class="input" type="password" name="password" required>@error('password')<p class="error">{{ $message }}</p>@enderror</div>
            <div><label class="label">Konfirmasi password</label><input class="input" type="password" name="password_confirmation" required></div>
            <button class="btn-primary w-full">Daftar</button>
        </form>
        <p class="mt-5 text-center text-sm text-slate-500">Sudah punya akun? <a class="font-bold text-red-600" href="{{ route('login') }}">Masuk</a></p>
    </div></div>
</x-layouts.app>
