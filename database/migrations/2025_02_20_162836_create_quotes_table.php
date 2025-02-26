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
        Schema::create('quotes', function (Blueprint $table) {
            $table->id()->comment('見積回答ID');
            $table->foreignId('request_id')->index()->comment('見積依頼ID（外部キー）');
            $table->foreignId('supplier_id')->index()->comment('サプライヤーID(外部キー)');
            $table->double('unit_price')->comment('単価（送料込み）');
            $table->integer('quantity')->comment('数量');
            $table->double('total_amount')->comment('総額');
            $table->date('delivery_date')->comment('回答日');
            $table->boolean('is_accepted')->default(false)->comment('採用フラグ');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotes');
    }
};
