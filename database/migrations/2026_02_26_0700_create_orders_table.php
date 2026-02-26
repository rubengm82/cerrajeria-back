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
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('installation_address');
            $table->string('shipping_address');
            $table->timestamp('shipped_at')->nullable();
            $table->enum('payment_method', ['paypal', 'card', 'bizum']);
            $table->timestamps();
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
