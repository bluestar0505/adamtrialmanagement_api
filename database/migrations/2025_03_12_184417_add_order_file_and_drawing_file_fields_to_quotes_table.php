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
            $table->string('drawing_file_org')->nullable()->after('is_accepted')->comment('正式図面（zip）のオリジナル名');
            $table->string('drawing_file')->nullable()->after('is_accepted')->comment('正式図面（zip）');
            $table->string('order_file_org')->nullable()->after('is_accepted')->comment('注文書のオリジナル名');
            $table->string('order_file')->nullable()->after('is_accepted')->comment('注文書');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quotes', function (Blueprint $table) {
            $table->dropColumn([
                'order_file',
                'order_file_org',
                'drawing_file',
                'drawing_file_org',
            ]);
        });
    }
};
