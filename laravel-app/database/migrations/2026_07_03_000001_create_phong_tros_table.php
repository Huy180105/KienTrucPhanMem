<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('phong_tros', function (Blueprint $table) {
            $table->string('maPhong')->primary();
            $table->string('tenPhong');
            $table->integer('tang');
            $table->double('giaPhong');
            $table->string('trangThai')->default('Trống'); // Trống, Đã thuê, Đang bảo trì
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('phong_tros');
    }
};
