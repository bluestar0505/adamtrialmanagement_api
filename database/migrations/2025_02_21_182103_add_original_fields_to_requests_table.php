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
            $table->string('data_2d_org')->after('data_2d')->comment('2Dデータのオリジナル名');
            $table->string('data_3d_org')->after('data_3d')->comment('3Dデータのオリジナル名');


        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('requests', function (Blueprint $table) {
            $table->dropColumn([
                'data_2d_org',
                'data_3d_org',
            ]);
        });
    }
};
