<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class ShippingSetting extends Model {
    protected $fillable = ['nominal', 'keterangan'];
    protected function casts(): array { return ['nominal' => 'decimal:2']; }
    public static function current(): self { return self::firstOrCreate(['id' => 1], ['nominal' => 0]); }
}
