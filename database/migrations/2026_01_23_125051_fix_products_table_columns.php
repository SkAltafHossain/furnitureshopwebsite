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
        Schema::table('products', function (Blueprint $table) {
            $table->renameColumn('product_name', 'name');
            $table->renameColumn('stock_quantity', 'stock');
            $table->renameColumn('product_image', 'image');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->renameColumn('name', 'product_name');
            $table->renameColumn('stock', 'stock_quantity');
            $table->renameColumn('image', 'product_image');
        });
    }
};
