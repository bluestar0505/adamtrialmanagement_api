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
        Schema::create('request_suppliers', function (Blueprint $table) {
            $table->foreignId('request_id')->index()->comment('見積依頼ID（外部キー）');
            $table->foreignId('supplier_id')->index()->comment('サプライヤーID(外部キー)');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('request_suppliers');
    }
};
