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
        Schema::create('tai_san_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('maTaiSan');
            $table->string('tenTaiSan');
            $table->string('hanhDong'); // CREATE, UPDATE, DELETE (Thanh lý)
            $table->string('trangThaiCu')->nullable();
            $table->string('trangThaiMoi')->nullable();
            $table->string('nguoiThucHien')->default('Quản trị viên');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tai_san_logs');
    }
};
