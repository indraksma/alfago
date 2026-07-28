<?php
namespace App\Models;
use App\Enums\VendorType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
class Vendor extends Model {
    use HasFactory;
    protected $fillable = ['nama', 'tipe', 'whatsapp_group_link', 'is_active'];
    protected function casts(): array { return ['tipe' => VendorType::class, 'is_active' => 'boolean']; }
    public function products(): HasMany { return $this->hasMany(Product::class); }
    public function orderItems(): HasMany { return $this->hasMany(OrderItem::class); }
}
