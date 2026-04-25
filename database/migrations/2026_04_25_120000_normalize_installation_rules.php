<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('installation_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('commerce_setting_id')->constrained('commerce_settings')->cascadeOnDelete();
            $table->decimal('min_subtotal', 10, 2);
            $table->decimal('max_subtotal', 10, 2)->nullable();
            $table->decimal('price', 10, 2);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('installation_rules');
    }
};
