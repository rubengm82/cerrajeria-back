<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->enum('status', ['in_cart', 'pending', 'shipped', 'installation_confirmed'])->default('in_cart');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('customer_name')->nullable();
            $table->string('customer_last_name_one')->nullable();
            $table->string('customer_last_name_second')->nullable();
            $table->string('customer_dni')->nullable();
            $table->string('customer_phone')->nullable();
            $table->string('customer_email')->nullable();
            $table->string('customer_address')->nullable();
            $table->string('customer_zip_code')->nullable();
            $table->string('customer_province')->nullable();
            $table->string('customer_country')->nullable()->default('España');
            $table->string('installation_address');
            $table->string('installation_zip_code')->nullable();
            $table->string('installation_province')->nullable();
            $table->string('installation_country')->nullable()->default('España');
            $table->string('shipping_address');
            $table->string('shipping_zip_code')->nullable();
            $table->string('shipping_province')->nullable();
            $table->string('shipping_country')->nullable()->default('España');
            $table->decimal('shipping_price', 10, 2)->default(0);
            $table->decimal('installation_price', 10, 2)->default(0);
            $table->timestamp('shipped_at')->nullable();
            $table->enum('payment_method', ['paypal', 'card', 'bizum']);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
