<div class="grid gap-6 xl:grid-cols-[380px_1fr]">
    <section class="panel h-fit p-5">
        <div class="flex items-center justify-between"><h2 class="text-lg font-black">{{ $editingId?'Edit':'Tambah' }} {{ $title }}</h2>@if($editingId)<button wire:click="resetForm" class="text-sm font-bold text-slate-500">Batal</button>@endif</div>
        <form wire:submit="save" class="mt-5 space-y-4">
            @if($resource!=='banners')<div><label class="label">Nama</label><input wire:model="nama" class="input">@error('nama')<p class="error">{{$message}}</p>@enderror</div>@endif
            @if($resource==='vendors')
                <div><label class="label">Tipe</label><select wire:model="tipe" class="input"><option value="toko">Toko</option><option value="pkl">PKL</option></select></div>
                <div><label class="label">Link grup WhatsApp</label><input wire:model="whatsapp_group_link" class="input" placeholder="https://chat.whatsapp.com/...">@error('whatsapp_group_link')<p class="error">{{$message}}</p>@enderror</div>
            @endif
            @if($resource==='categories')<div><label class="label">Icon / emoji</label><input wire:model="icon" class="input" placeholder="🍜"></div>@endif
            @if($resource==='products')
                <div class="grid grid-cols-2 gap-3"><div><label class="label">Vendor</label><select wire:model="vendor_id" class="input"><option value="">Pilih</option>@foreach($vendors as $v)<option value="{{$v->id}}">{{$v->nama}}</option>@endforeach</select>@error('vendor_id')<p class="error">{{$message}}</p>@enderror</div><div><label class="label">Kategori</label><select wire:model="category_id" class="input"><option value="">Pilih</option>@foreach($categories as $c)<option value="{{$c->id}}">{{$c->nama}}</option>@endforeach</select>@error('category_id')<p class="error">{{$message}}</p>@enderror</div></div>
                <div><label class="label">Deskripsi</label><textarea wire:model="deskripsi" class="input" rows="3"></textarea></div>
                <div class="grid grid-cols-2 gap-3"><div><label class="label">Harga</label><input wire:model="harga" type="number" min="0" class="input">@error('harga')<p class="error">{{$message}}</p>@enderror</div><div><label class="label">Stok</label><input wire:model="stok" type="number" min="0" class="input" placeholder="Kosong = unlimited">@error('stok')<p class="error">{{$message}}</p>@enderror</div></div>
            @endif
            @if($resource==='banners')
                <div><label class="label">Judul</label><input wire:model="judul" class="input"></div><div><label class="label">Link tujuan</label><input wire:model="link_url" class="input" placeholder="https://...">@error('link_url')<p class="error">{{$message}}</p>@enderror</div>
                <div class="grid grid-cols-2 gap-3"><div><label class="label">Urutan</label><input wire:model="urutan" type="number" class="input"></div><div></div><div><label class="label">Mulai</label><input wire:model="tanggal_mulai" type="date" class="input"></div><div><label class="label">Selesai</label><input wire:model="tanggal_selesai" type="date" class="input">@error('tanggal_selesai')<p class="error">{{$message}}</p>@enderror</div></div>
            @endif
            @if(in_array($resource,['products','banners']))
                <div><label class="label">Gambar {{ $editingId?'baru (opsional)':'' }}</label><input wire:model="gambar" type="file" accept="image/*" class="input file:mr-3 file:rounded-lg file:border-0 file:bg-red-50 file:px-3 file:py-1 file:text-red-600">@error('gambar')<p class="error">{{$message}}</p>@enderror
                    @if($gambar)<img src="{{$gambar->temporaryUrl()}}" class="mt-2 h-24 rounded-xl object-cover">@elseif($existingImage)<img src="{{str_starts_with($existingImage,'http')?$existingImage:Storage::url($existingImage)}}" class="mt-2 h-24 rounded-xl object-cover">@endif
                </div>
            @endif
            <label class="flex items-center gap-2 text-sm font-semibold"><input wire:model="is_active" type="checkbox" class="rounded border-slate-300 text-red-600"> Aktif</label>
            <button class="btn-primary w-full" wire:loading.attr="disabled">Simpan {{ $title }}</button>
        </form>
    </section>
    <section class="min-w-0"><div class="mb-4 flex items-center justify-between gap-3"><input wire:model.live.debounce.300ms="search" class="input max-w-sm" placeholder="Cari {{strtolower($title)}}..."><span class="text-sm text-slate-500">{{$rows->total()}} data</span></div>
        @error('delete')<div class="mb-4 rounded-xl bg-red-50 p-3 text-sm text-red-700">{{$message}}</div>@enderror
        <div class="panel overflow-x-auto"><table class="data-table"><thead><tr>
            @if(in_array($resource,['products','banners']))<th>Gambar</th>@endif
            <th>{{ $resource==='banners'?'Judul':'Nama' }}</th>
            @if($resource==='vendors')<th>Tipe</th>@endif @if($resource==='products')<th>Vendor / Kategori</th><th>Harga</th><th>Stok</th>@endif @if($resource==='banners')<th>Periode</th><th>Urutan</th>@endif
            <th>Status</th><th class="text-right">Aksi</th></tr></thead><tbody>
            @forelse($rows as $row)<tr wire:key="row-{{$row->id}}">
                @if(in_array($resource,['products','banners']))<td>@if($row->gambar)<img class="h-12 w-16 rounded-lg object-cover" src="{{str_starts_with($row->gambar,'http')?$row->gambar:Storage::url($row->gambar)}}">@else<span class="text-slate-300">—</span>@endif</td>@endif
                <td class="font-bold">{{$resource==='banners'?($row->judul?:'Tanpa judul'):$row->nama}}</td>
                @if($resource==='vendors')<td class="uppercase">{{$row->tipe->value}}</td>@endif
                @if($resource==='products')<td><span class="block">{{$row->vendor->nama}}</span><small class="text-slate-500">{{$row->category->nama}}</small></td><td>Rp{{number_format($row->harga,0,',','.')}}</td><td>{{$row->stok===null?'∞':$row->stok}}</td>@endif
                @if($resource==='banners')<td class="text-xs">{{$row->tanggal_mulai?->format('d/m/Y')?:'∞'}} – {{$row->tanggal_selesai?->format('d/m/Y')?:'∞'}}</td><td>{{$row->urutan}}</td>@endif
                <td><span class="rounded-full px-2 py-1 text-xs font-bold {{$row->is_active?'bg-emerald-100 text-emerald-700':'bg-slate-100 text-slate-500'}}">{{$row->is_active?'Aktif':'Nonaktif'}}</span></td>
                <td><div class="flex justify-end gap-2"><button wire:click="edit({{$row->id}})" class="table-action">Edit</button><button wire:click="delete({{$row->id}})" wire:confirm="Hapus data ini?" class="table-action text-red-600">Hapus</button></div></td>
            </tr>@empty<tr><td colspan="8" class="py-10 text-center text-slate-500">Belum ada data.</td></tr>@endforelse
        </tbody></table></div><div class="mt-5">{{$rows->links()}}</div>
    </section>
</div>
