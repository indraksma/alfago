<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class OrderItem extends Model {
    protected $fillable = ['order_id', 'product_id', 'vendor_id', 'nama_produk', 'harga', 'qty', 'subtotal'];
    protected function casts(): array { return ['harga'=>'decimal:2','subtotal'=>'decimal:2','qty'=>'integer']; }
    public function order(): BelongsTo { return $this->belongsTo(Order::class); }
    public function product(): BelongsTo { return $this->belongsTo(Product::class); }
    public function vendor(): BelongsTo { return $this->belongsTo(Vendor::class); }
}
