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
            $table->double('unit_price')->nullable()->comment('単価（送料込み）')->change();
            $table->integer('quantity')->nullable()->comment('数量')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quotes', function (Blueprint $table) {
            $table->double('unit_price')->comment('単価（送料込み）')->change();
            $table->integer('quantity')->comment('数量')->change();
        });
    }
};
