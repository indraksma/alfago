<?php

namespace Database\Seeders;

use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\UserRole;
use App\Models\Banner;
use App\Models\Category;
use App\Models\Kelas;
use App\Models\Order;
use App\Models\Product;
use App\Models\ShippingSetting;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $classes = collect(['X RPL 1','X RPL 2','XI RPL 1','XI RPL 2','XII RPL 1','XII RPL 2'])
            ->map(fn ($name) => Kelas::create(['nama' => $name, 'is_active' => true]));

        $admin = User::create([
            'name' => env('ADMIN_NAME', 'Admin ALFAGO'),
            'email' => env('ADMIN_EMAIL', 'admin@alfago.test'),
            'password' => env('ADMIN_PASSWORD', 'password'),
            'role' => UserRole::Admin,
        ]);
        $user = User::create([
            'name' => 'Budi Santoso', 'email' => 'user@alfago.test',
            'password' => 'password', 'role' => UserRole::User, 'kelas_id' => $classes[2]->id,
        ]);

        $toko = Vendor::create(['nama'=>'Toko Alfa Skansa','tipe'=>'toko','is_active'=>true]);
        $bakso = Vendor::create(['nama'=>'PKL Bakso Pak Budi','tipe'=>'pkl','whatsapp_group_link'=>'https://chat.whatsapp.com/example','is_active'=>true]);
        $minuman = Vendor::create(['nama'=>'Es Teh Bu Sari','tipe'=>'pkl','is_active'=>true]);

        $makanan = Category::create(['nama'=>'Makanan','icon'=>'🍜','is_active'=>true]);
        $drink = Category::create(['nama'=>'Minuman','icon'=>'🥤','is_active'=>true]);
        $snack = Category::create(['nama'=>'Jajanan','icon'=>'🍪','is_active'=>true]);

        $products = collect([
            [$bakso,$makanan,'Bakso Urat Komplit','Bakso hangat dengan urat, mi, sayur, dan kuah gurih.',8000,null,'https://images.unsplash.com/photo-1625938145744-e380515399bf?auto=format&fit=crop&w=800&q=80'],
            [$bakso,$makanan,'Mie Ayam Bakso','Mie ayam gurih dengan tambahan bakso.',10000,40,'https://images.unsplash.com/photo-1617622141675-d3005b9067c5?auto=format&fit=crop&w=800&q=80'],
            [$minuman,$drink,'Es Teh Manis','Teh melati segar dengan es.',3000,null,'https://images.unsplash.com/photo-1556679343-c7306c1976bc?auto=format&fit=crop&w=800&q=80'],
            [$minuman,$drink,'Es Jeruk Segar','Jeruk peras asli, manis dan menyegarkan.',5000,50,'https://images.unsplash.com/photo-1621263764928-df1444c5e859?auto=format&fit=crop&w=800&q=80'],
            [$toko,$snack,'Roti Cokelat','Roti lembut isi cokelat.',4500,25,'https://images.unsplash.com/photo-1509440159596-0249088772ff?auto=format&fit=crop&w=800&q=80'],
            [$toko,$snack,'Keripik Pedas','Keripik renyah dengan bumbu pedas.',6000,30,'https://images.unsplash.com/photo-1566478989037-eec170784d0b?auto=format&fit=crop&w=800&q=80'],
            [$toko,$makanan,'Nasi Ayam Geprek','Nasi, ayam krispi, dan sambal bawang.',12000,20,'https://images.unsplash.com/photo-1601050690597-df0568f70950?auto=format&fit=crop&w=800&q=80'],
            [$toko,$drink,'Air Mineral','Air mineral dingin 600 ml.',3000,100,'https://images.unsplash.com/photo-1602143407151-7111542de6e8?auto=format&fit=crop&w=800&q=80'],
        ])->map(fn ($p) => Product::create(['vendor_id'=>$p[0]->id,'category_id'=>$p[1]->id,'nama'=>$p[2],'deskripsi'=>$p[3],'harga'=>$p[4],'stok'=>$p[5],'gambar'=>$p[6],'is_active'=>true]));

        Banner::create(['judul'=>'Jajan Favorit, Diantar ke Kelas','gambar'=>'https://images.unsplash.com/photo-1504674900247-0877df9cc836?auto=format&fit=crop&w=1600&q=80','urutan'=>1,'is_active'=>true]);
        Banner::create(['judul'=>'Segar dan Hemat Setiap Hari','gambar'=>'https://images.unsplash.com/photo-1544145945-f90425340c7e?auto=format&fit=crop&w=1600&q=80','urutan'=>2,'is_active'=>true]);
        ShippingSetting::create(['id'=>1,'nominal'=>2000,'keterangan'=>'Biaya antar per pesanan']);

        foreach ([OrderStatus::Pending, OrderStatus::Processing, OrderStatus::Completed] as $index => $status) {
            $first = $products[$index]; $second = $products[$index + 2];
            $subtotal = (float) $first->harga * 2 + (float) $second->harga;
            $order = Order::create([
                'user_id'=>$user->id,'kelas_id'=>$classes[2]->id,'kode_pesanan'=>'ALF-'.now()->subDays($index)->format('Ymd').'-'.str_pad((string)($index+1),4,'0',STR_PAD_LEFT),
                'subtotal'=>$subtotal,'ongkir'=>2000,'total'=>$subtotal+2000,'metode_pembayaran'=>$index%2?PaymentMethod::Qris:PaymentMethod::Cash,
                'status'=>$status,'catatan'=>$index===0?'Tolong tidak pedas':null,'confirmed_at'=>$status===OrderStatus::Pending?null:now()->subDays($index),
                'created_at'=>now()->subDays($index),'updated_at'=>now()->subDays($index),
            ]);
            foreach ([[$first,2],[$second,1]] as [$product,$qty]) {
                $order->items()->create(['product_id'=>$product->id,'vendor_id'=>$product->vendor_id,'nama_produk'=>$product->nama,'harga'=>$product->harga,'qty'=>$qty,'subtotal'=>(float)$product->harga*$qty]);
            }
        }
    }
}
