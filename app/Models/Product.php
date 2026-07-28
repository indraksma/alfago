<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
class Product extends Model {
    use HasFactory;
    protected $fillable = ['vendor_id', 'category_id', 'nama', 'deskripsi', 'harga', 'gambar', 'stok', 'is_active'];
    protected function casts(): array { return ['harga' => 'decimal:2', 'is_active' => 'boolean', 'stok' => 'integer']; }
    public function vendor(): BelongsTo { return $this->belongsTo(Vendor::class); }
    public function category(): BelongsTo { return $this->belongsTo(Category::class); }
    public function cartItems(): HasMany { return $this->hasMany(CartItem::class); }
    public function orderItems(): HasMany { return $this->hasMany(OrderItem::class); }
    public function isAvailable(): bool { return $this->is_active && $this->vendor?->is_active && $this->category?->is_active && ($this->stok === null || $this->stok > 0); }
}
