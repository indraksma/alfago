<?php

namespace App\Livewire\Admin;

use App\Models\Banner;
use App\Models\Category;
use App\Models\Kelas;
use App\Models\Product;
use App\Models\Vendor;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Throwable;

class CrudPage extends Component
{
    use WithFileUploads, WithPagination;

    public string $resource;
    public ?int $editingId = null;
    public string $search = '';
    public string $nama = '';
    public string $tipe = 'toko';
    public string $whatsapp_group_link = '';
    public string $icon = '';
    public ?int $vendor_id = null;
    public ?int $category_id = null;
    public string $deskripsi = '';
    public $harga = '';
    public $stok = null;
    public $gambar;
    public ?string $existingImage = null;
    public string $judul = '';
    public string $link_url = '';
    public int $urutan = 0;
    public string $tanggal_mulai = '';
    public string $tanggal_selesai = '';
    public bool $is_active = true;

    public function mount(string $resource): void
    {
        abort_unless(array_key_exists($resource, $this->configs()), 404);
        $this->resource = $resource;
    }

    private function configs(): array
    {
        return [
            'kelas' => ['model' => Kelas::class, 'title' => 'Kelas'],
            'vendors' => ['model' => Vendor::class, 'title' => 'Vendor'],
            'categories' => ['model' => Category::class, 'title' => 'Kategori'],
            'products' => ['model' => Product::class, 'title' => 'Produk'],
            'banners' => ['model' => Banner::class, 'title' => 'Banner'],
        ];
    }

    private function modelClass(): string { return $this->configs()[$this->resource]['model']; }
    private function model(): Model { $class = $this->modelClass(); return $this->editingId ? $class::findOrFail($this->editingId) : new $class; }

    private function rulesForForm(): array
    {
        return match ($this->resource) {
            'kelas' => ['nama' => ['required','string','max:255',Rule::unique('kelas')->ignore($this->editingId)], 'is_active' => ['boolean']],
            'vendors' => ['nama'=>['required','string','max:255'],'tipe'=>['required','in:toko,pkl'],'whatsapp_group_link'=>['nullable','url','max:255'],'is_active'=>['boolean']],
            'categories' => ['nama'=>['required','string','max:255',Rule::unique('categories')->ignore($this->editingId)],'icon'=>['nullable','string','max:255'],'is_active'=>['boolean']],
            'products' => ['nama'=>['required','string','max:255'],'vendor_id'=>['required','exists:vendors,id'],'category_id'=>['required','exists:categories,id'],'deskripsi'=>['nullable','string'],'harga'=>['required','numeric','min:0'],'stok'=>['nullable','integer','min:0'],'gambar'=>[$this->editingId?'nullable':'nullable','image','max:3072'],'is_active'=>['boolean']],
            'banners' => ['judul'=>['nullable','string','max:255'],'link_url'=>['nullable','url','max:255'],'urutan'=>['required','integer'],'tanggal_mulai'=>['nullable','date'],'tanggal_selesai'=>['nullable','date','after_or_equal:tanggal_mulai'],'gambar'=>[$this->editingId?'nullable':'required','image','max:5120'],'is_active'=>['boolean']],
        };
    }

    public function save(): void
    {
        $validated = $this->validate($this->rulesForForm());
        $model = $this->model();
        if (array_key_exists('gambar', $validated)) unset($validated['gambar']);
        if ($this->resource === 'products' || $this->resource === 'banners') {
            if ($this->gambar) {
                if ($model->exists && $model->gambar) Storage::disk('public')->delete($model->gambar);
                $validated['gambar'] = $this->gambar->store($this->resource, 'public');
            }
        }
        $model->fill($validated)->save();
        session()->flash('success', $this->configs()[$this->resource]['title'].' berhasil disimpan.');
        $this->resetForm();
    }

    public function edit(int $id): void
    {
        $model = ($this->modelClass())::findOrFail($id);
        $this->editingId = $model->id;
        foreach (['nama','tipe','whatsapp_group_link','icon','vendor_id','category_id','deskripsi','harga','stok','judul','link_url','urutan','tanggal_mulai','tanggal_selesai','is_active'] as $field) {
            if (array_key_exists($field, $model->getAttributes())) {
                $value = $model->{$field};
                $this->{$field} = $value instanceof \BackedEnum ? $value->value : ($value instanceof \DateTimeInterface ? $value->format('Y-m-d') : ($value ?? ''));
            }
        }
        $this->existingImage = $model->gambar ?? null;
    }

    public function delete(int $id): void
    {
        try {
            $model = ($this->modelClass())::findOrFail($id);
            $image = $model->gambar ?? null;
            $model->delete();
            if ($image) Storage::disk('public')->delete($image);
            session()->flash('success', 'Data berhasil dihapus.');
        } catch (Throwable) {
            $this->addError('delete', 'Data sudah digunakan dan tidak dapat dihapus. Nonaktifkan data sebagai gantinya.');
        }
    }

    public function resetForm(): void
    {
        $this->reset(['editingId','nama','whatsapp_group_link','icon','vendor_id','category_id','deskripsi','harga','stok','gambar','existingImage','judul','link_url','tanggal_mulai','tanggal_selesai']);
        $this->tipe='toko'; $this->urutan=0; $this->is_active=true; $this->resetValidation();
    }

    public function render()
    {
        $class = $this->modelClass();
        $query = $class::query()->when($this->search, function ($q) {
            $column = $this->resource === 'banners' ? 'judul' : 'nama';
            $q->where($column, 'like', "%{$this->search}%");
        });
        if ($this->resource === 'products') $query->with('vendor','category');
        return view('livewire.admin.crud-page', [
            'rows'=>$query->latest()->paginate(10),
            'title'=>$this->configs()[$this->resource]['title'],
            'vendors'=>Vendor::where('is_active',true)->orderBy('nama')->get(),
            'categories'=>Category::where('is_active',true)->orderBy('nama')->get(),
        ])->layout('components.layouts.admin', ['title'=>$this->configs()[$this->resource]['title']]);
    }
}
