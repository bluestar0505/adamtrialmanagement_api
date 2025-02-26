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
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id()->comment('サプライヤーID');
            $table->string('supplier_no')->comment('管理No');
            $table->string('company_name')->comment('会社名');
            $table->string('postal_code')->nullable()->comment('郵便番号');
            $table->string('address')->nullable()->comment('住所');
            $table->string('phone_number')->nullable()->comment('電話番号');
            $table->string('fax_number')->nullable()->comment('FAX番号');
            $table->string('contact_name')->comment('担当者名');
            $table->string('contact_email')->comment('担当者メールアドレス');
            //$table->string('password')->comment('パスワードハッシュ');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};
