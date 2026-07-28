<?php
namespace App\Enums;
enum OrderStatus: string
{
    case Pending = 'pending';
    case Processing = 'diproses';
    case Completed = 'selesai';
    public function label(): string { return match ($this) { self::Pending => 'Pending', self::Processing => 'Diproses', self::Completed => 'Selesai' }; }
}
