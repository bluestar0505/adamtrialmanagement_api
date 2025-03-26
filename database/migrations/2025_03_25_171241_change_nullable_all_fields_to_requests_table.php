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
        Schema::table('requests', function (Blueprint $table) {
            $table->string('product_name')->nullable()->comment('案件名')->change();
            $table->string('data_2d')->nullable()->comment('2Dデータ')->change();
            $table->string('data_3d')->nullable()->comment('3Dデータ')->change();
            $table->string('data_2d_org')->nullable()->comment('2Dデータのオリジナル名')->change();
            $table->string('data_3d_org')->nullable()->comment('3Dデータのオリジナル名')->change();
            $table->date('desired_delivery_date')->nullable()->comment('希望納期')->change();
            $table->date('reply_due_date')->nullable()->comment('回答期日')->change();
            $table->tinyInteger('important')->nullable()->comment('優先フラグ')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('requets', function (Blueprint $table) {
            $table->string('product_name')->comment('案件名')->change();
            $table->string('data_2d')->comment('2Dデータ')->change();
            $table->string('data_3d')->comment('3Dデータ')->change();
            $table->string('data_2d_org')->comment('2Dデータのオリジナル名')->change();
            $table->string('data_3d_org')->comment('3Dデータのオリジナル名')->change();
            $table->date('desired_delivery_date')->comment('希望納期')->change();
            $table->date('reply_due_date')->comment('回答期日')->change();
            $table->tinyInteger('important')->comment('優先フラグ')->change();
        });
    }
};
