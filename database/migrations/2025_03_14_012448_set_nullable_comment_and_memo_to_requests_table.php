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
            $table->text('comment')->nullable()->comment('コメント')->change();
            $table->text('memo')->nullable()->comment('メモ')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('requests', function (Blueprint $table) {
            $table->text('comment')->comment('コメント')->change();
            $table->text('memo')->comment('メモ')->change();
        });
    }
};
