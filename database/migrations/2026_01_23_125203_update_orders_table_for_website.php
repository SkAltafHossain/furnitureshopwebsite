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
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('id');
            $table->string('customer_email')->after('customer_name');
            $table->string('customer_phone')->after('customer_email');
            $table->text('customer_address')->after('customer_phone');
            $table->renameColumn('payment_type', 'payment_method');
            $table->enum('payment_status', ['pending', 'paid', 'failed'])->default('pending')->after('payment_method');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn(['user_id', 'customer_email', 'customer_phone', 'customer_address', 'payment_status']);
            $table->renameColumn('payment_method', 'payment_type');
        });
    }
};
