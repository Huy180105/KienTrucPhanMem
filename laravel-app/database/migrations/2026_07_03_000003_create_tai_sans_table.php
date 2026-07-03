<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tai_sans', function (Blueprint $table) {
            $table->id('maTaiSan');
            $table->string('tenTaiSan');
            $table->integer('soLuong');
            $table->string('tinhTrang');
            $table->string('maPhong')->nullable();
            
            $table->foreign('maPhong')
                  ->references('maPhong')
                  ->on('phong_tros')
                  ->onDelete('set null');
                  
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tai_sans');
    }
};
