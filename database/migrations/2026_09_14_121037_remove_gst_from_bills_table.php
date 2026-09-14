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
        Schema::table('bills', function (Blueprint $table) {
            $table->dropColumn(['gst_amount', 'gst_percentage']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bills', function (Blueprint $table) {
            $table->decimal('gst_amount', 10, 2)->after('subtotal');
            $table->decimal('gst_percentage', 5, 2)->default(18.00)->after('gst_amount');
        });
    }
};
