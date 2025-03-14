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
        Schema::table('quotes', function (Blueprint $table) {
            $table->json('products')->nullable()->after('supplier_id')->comment('部品リスト');
            $table->boolean('is_sent')->default(0)->after('delivery_date')->comment('送信ステータス');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quotes', function (Blueprint $table) {
            $table->dropColumn([
                'is_sent',
                'products',
            ]);
        });
    }
};
