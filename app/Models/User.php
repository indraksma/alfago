<?php

namespace App\Models;

use App\Enums\UserRole;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = ['name', 'email', 'phone', 'password', 'role', 'kelas_id'];
    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
        ];
    }
    public function kelas(): BelongsTo { return $this->belongsTo(Kelas::class); }
    public function cart(): HasOne { return $this->hasOne(Cart::class); }
    public function orders(): HasMany { return $this->hasMany(Order::class); }
    public function isAdmin(): bool { return $this->role === UserRole::Admin; }

    public function whatsappUrl(): ?string
    {
        if (! $this->phone) return null;

        $number = preg_replace('/\D+/', '', $this->phone);
        if (str_starts_with($number, '0')) $number = '62'.substr($number, 1);
        elseif (str_starts_with($number, '8')) $number = '62'.$number;

        return 'https://wa.me/'.$number;
    }
}
