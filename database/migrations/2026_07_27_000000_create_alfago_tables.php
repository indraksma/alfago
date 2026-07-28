<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kelas', function (Blueprint $table) {
            $table->id(); $table->string('nama')->unique(); $table->boolean('is_active')->default(true)->index(); $table->timestamps();
        });
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'user'])->default('user')->index()->after('password');
            $table->foreignId('kelas_id')->nullable()->after('role')->constrained('kelas')->nullOnDelete();
        });
        Schema::create('vendors', function (Blueprint $table) {
            $table->id(); $table->string('nama'); $table->enum('tipe', ['toko', 'pkl']); $table->string('whatsapp_group_link')->nullable(); $table->boolean('is_active')->default(true)->index(); $table->timestamps();
        });
        Schema::create('categories', function (Blueprint $table) {
            $table->id(); $table->string('nama')->unique(); $table->string('icon')->nullable(); $table->boolean('is_active')->default(true)->index(); $table->timestamps();
        });
        Schema::create('products', function (Blueprint $table) {
            $table->id(); $table->foreignId('vendor_id')->constrained()->restrictOnDelete(); $table->foreignId('category_id')->constrained()->restrictOnDelete();
            $table->string('nama'); $table->text('deskripsi')->nullable(); $table->decimal('harga', 10, 2); $table->string('gambar')->nullable(); $table->unsignedInteger('stok')->nullable(); $table->boolean('is_active')->default(true)->index(); $table->timestamps(); $table->index(['category_id', 'is_active']);
        });
        Schema::create('banners', function (Blueprint $table) {
            $table->id(); $table->string('gambar'); $table->string('judul')->nullable(); $table->string('link_url')->nullable(); $table->integer('urutan')->default(0); $table->boolean('is_active')->default(true)->index(); $table->date('tanggal_mulai')->nullable(); $table->date('tanggal_selesai')->nullable(); $table->timestamps();
        });
        Schema::create('shipping_settings', function (Blueprint $table) {
            $table->id(); $table->decimal('nominal', 10, 2)->default(0); $table->string('keterangan')->nullable(); $table->timestamps();
        });
        Schema::create('carts', function (Blueprint $table) {
            $table->id(); $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete(); $table->timestamps();
        });
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id(); $table->foreignId('cart_id')->constrained()->cascadeOnDelete(); $table->foreignId('product_id')->constrained()->cascadeOnDelete(); $table->unsignedInteger('qty'); $table->timestamps(); $table->unique(['cart_id', 'product_id']);
        });
        Schema::create('orders', function (Blueprint $table) {
            $table->id(); $table->foreignId('user_id')->constrained()->restrictOnDelete(); $table->foreignId('kelas_id')->constrained('kelas')->restrictOnDelete(); $table->string('kode_pesanan')->unique();
            $table->decimal('subtotal', 10, 2); $table->decimal('ongkir', 10, 2); $table->decimal('total', 10, 2); $table->enum('metode_pembayaran', ['cash', 'qris']); $table->enum('status', ['pending', 'diproses', 'selesai'])->default('pending')->index(); $table->text('catatan')->nullable(); $table->timestamp('confirmed_at')->nullable(); $table->timestamps(); $table->index(['created_at', 'status']);
        });
        Schema::create('order_items', function (Blueprint $table) {
            $table->id(); $table->foreignId('order_id')->constrained()->cascadeOnDelete(); $table->foreignId('product_id')->constrained()->restrictOnDelete(); $table->foreignId('vendor_id')->constrained()->restrictOnDelete(); $table->string('nama_produk'); $table->decimal('harga', 10, 2); $table->unsignedInteger('qty'); $table->decimal('subtotal', 10, 2); $table->timestamps(); $table->index(['vendor_id', 'order_id']);
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('order_items'); Schema::dropIfExists('orders'); Schema::dropIfExists('cart_items'); Schema::dropIfExists('carts'); Schema::dropIfExists('shipping_settings'); Schema::dropIfExists('banners'); Schema::dropIfExists('products'); Schema::dropIfExists('categories'); Schema::dropIfExists('vendors');
        Schema::table('users', function (Blueprint $table) { $table->dropConstrainedForeignId('kelas_id'); $table->dropColumn('role'); });
        Schema::dropIfExists('kelas');
    }
};
