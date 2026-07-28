<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Enums\UserRole;
use App\Livewire\Admin\OrderManager;
use App\Models\Category;
use App\Models\Kelas;
use App\Models\Product;
use App\Models\ShippingSetting;
use App\Models\User;
use App\Models\Vendor;
use App\Services\CartService;
use App\Services\CheckoutService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AlfagoFeatureTest extends TestCase
{
    use RefreshDatabase;

    private function masterData(?int $stock = 10): array
    {
        $kelas = Kelas::create(['nama'=>'XI RPL 1','is_active'=>true]);
        $vendor = Vendor::create(['nama'=>'PKL Test','tipe'=>'pkl','is_active'=>true]);
        $category = Category::create(['nama'=>'Makanan','icon'=>'🍜','is_active'=>true]);
        $product = Product::create(['vendor_id'=>$vendor->id,'category_id'=>$category->id,'nama'=>'Bakso Test','harga'=>8000,'stok'=>$stock,'is_active'=>true]);
        ShippingSetting::create(['id'=>1,'nominal'=>2000]);
        return compact('kelas','vendor','category','product');
    }

    public function test_public_catalog_and_auth_pages_are_available(): void
    {
        $this->masterData();
        $this->get('/')->assertOk()->assertSee('Bakso Test');
        $this->get('/login')->assertOk();
        $this->get('/register')->assertOk();
        $this->get('/checkout')->assertRedirect('/login');
    }

    public function test_user_can_register_and_login(): void
    {
        $data = $this->masterData();
        $this->post('/register', [
            'name'=>'Siti','email'=>'siti@example.com','kelas_id'=>$data['kelas']->id,
            'password'=>'password','password_confirmation'=>'password',
        ])->assertRedirect('/');
        $this->assertAuthenticated();
        $this->post('/logout')->assertRedirect('/');
        $this->post('/login',['email'=>'siti@example.com','password'=>'password'])->assertRedirect('/');
        $this->assertAuthenticated();
    }

    public function test_admin_routes_are_protected_by_role(): void
    {
        $this->masterData();
        $user = User::factory()->create(['role'=>UserRole::User]);
        $admin = User::factory()->create(['role'=>UserRole::Admin]);
        $this->actingAs($user)->get('/admin/dashboard')->assertForbidden();
        $this->actingAs($admin)->get('/admin/dashboard')->assertOk();
        foreach (['kelas','vendors','categories','products','banners'] as $resource) {
            $this->get("/admin/master/{$resource}")->assertOk();
        }
        $this->get('/admin/shipping')->assertOk();
        $this->get('/admin/orders')->assertOk();
    }

    public function test_checkout_snapshots_values_decrements_stock_and_clears_cart(): void
    {
        $data = $this->masterData(5);
        $user = User::factory()->create(['kelas_id'=>$data['kelas']->id]);
        app(CartService::class)->add($user, $data['product'], 2);
        $order = app(CheckoutService::class)->create($user, $data['kelas']->id, 'qris', 'Tidak pedas');

        $this->assertStringStartsWith('ALF-', $order->kode_pesanan);
        $this->assertSame('16000.00', $order->subtotal);
        $this->assertSame('18000.00', $order->total);
        $this->assertSame('Bakso Test', $order->items->first()->nama_produk);
        $this->assertSame(3, $data['product']->fresh()->stok);
        $this->assertDatabaseCount('cart_items', 0);
    }

    public function test_admin_can_advance_status_and_whatsapp_text_is_grouped(): void
    {
        $data = $this->masterData();
        $user = User::factory()->create(['kelas_id'=>$data['kelas']->id]);
        $admin = User::factory()->create(['role'=>UserRole::Admin]);
        app(CartService::class)->add($user, $data['product']);
        $order = app(CheckoutService::class)->create($user, $data['kelas']->id, 'cash', null);

        Livewire::actingAs($admin)->test(OrderManager::class, ['order'=>$order])
            ->call('confirm')
            ->assertSet('order.status', OrderStatus::Processing)
            ->assertSee('PKL Test')
            ->assertSee('PESANAN BARU - ALFAGO');
        $this->assertNotNull($order->fresh()->confirmed_at);
    }
}
