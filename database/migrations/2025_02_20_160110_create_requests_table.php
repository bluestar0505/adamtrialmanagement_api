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
        Schema::create('requests', function (Blueprint $table) {
            $table->id()->comment('見積依頼ID');
            $table->foreignId('buyer_id')->index()->comment('バイヤーID(外部キー)');
            $table->dateTime('request_date')->comment('依頼日時');
            $table->string('management_no')->comment('管理No');
            $table->string('product_name')->comment('案件名');
            $table->string('material')->nullable()->comment('材質');
            $table->integer('quantity')->comment('数量');
            $table->string('data_2d')->comment('2Dデータ');
            $table->string('data_3d')->comment('3Dデータ');
            $table->date('desired_delivery_date')->comment('希望納期');
            $table->date('reply_due_date')->comment('回答期日');
            $table->text('comment')->comment('コメント');
            $table->text('memo')->comment('メモ');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('requests');
    }
};
