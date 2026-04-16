<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const STATUS_NEW = 'nova';

    private const STATUSES = [
        self::STATUS_NEW,
        'contactat',
        'pressupost_aprovat',
        'en_curs',
        'en_transit',
        'finalitzat',
        'rebutjat',
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('custom_solutions', function (Blueprint $table) {
            $table->id();
            $table->string('email');
            $table->string('phone');
            $table->text('description');
            $table->enum('status', self::STATUSES)->default(self::STATUS_NEW);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('custom_solutions');
    }
};
