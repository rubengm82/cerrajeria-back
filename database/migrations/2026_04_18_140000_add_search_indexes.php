<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Índices para búsqueda en products
        Schema::table('products', function (Blueprint $table) {
            // Índice FULLTEXT para búsqueda por texto (MySQL 5.6+ con InnoDB)
            // Para MySQL: FULLTEXT index en name, description, code
            if (DB::getDriverName() === 'mysql') {
                DB::statement('ALTER TABLE products ADD FULLTEXT INDEX products_search_idx (name, description, code)');
            }
            
            // Índices normales para filtros comunes
            $table->index('category_id');
            $table->index('is_active');
            $table->index('is_important_to_show');
            $table->index('price');
            $table->index('name'); // Para búsquedas LIKE simples
        });

        // Índices para búsqueda en packs
        Schema::table('packs', function (Blueprint $table) {
            if (DB::getDriverName() === 'mysql') {
                DB::statement('ALTER TABLE packs ADD FULLTEXT INDEX packs_search_idx (name, description)');
            }
            $table->index('total_price');
            $table->index('name');
        });

        // Índice en la tabla pivote pack_products para búsqueda de packs por producto
        Schema::table('pack_products', function (Blueprint $table) {
            $table->index('products_id');
            $table->index('pack_id');
            // Índice compuesto para buscar packs que contengan un producto específico
            $table->unique(['products_id', 'pack_id'], 'pack_product_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (DB::getDriverName() === 'mysql') {
                DB::statement('DROP INDEX products_search_idx ON products');
            }
            $table->dropIndex(['category_id']);
            $table->dropIndex(['is_active']);
            $table->dropIndex(['is_important_to_show']);
            $table->dropIndex(['price']);
            $table->dropIndex(['name']);
        });

        Schema::table('packs', function (Blueprint $table) {
            if (DB::getDriverName() === 'mysql') {
                DB::statement('DROP INDEX packs_search_idx ON packs');
            }
            $table->dropIndex(['total_price']);
            $table->dropIndex(['name']);
        });

        Schema::table('pack_products', function (Blueprint $table) {
            $table->dropUnique('pack_product_unique');
            $table->dropIndex(['products_id']);
            $table->dropIndex(['pack_id']);
        });
    }
};
