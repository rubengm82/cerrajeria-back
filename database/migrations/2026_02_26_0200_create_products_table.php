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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->integer('stock');
            $table->string('code')->unique();
            $table->decimal('discount', 5, 2)->nullable();
            $table->foreignId('category')->constrained('categories')->onDelete('cascade');
            $table->boolean('is_installable')->default(false);
            $table->boolean('is_important_to_show')->default(false);
            $table->decimal('installation_price', 10, 2)->nullable();
            $table->integer('extra_keys')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
