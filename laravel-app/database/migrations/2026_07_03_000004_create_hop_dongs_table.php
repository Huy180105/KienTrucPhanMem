<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hop_dongs', function (Blueprint $table) {
            $table->id('maHopDong');
            $table->string('maPhong');
            $table->unsignedBigInteger('maKhach');
            $table->date('ngayLap');
            $table->date('ngayBatDau');
            $table->date('ngayKetThuc');
            $table->double('tienCoc');
            $table->double('giaThueThang');
            $table->string('trangThai')->default('Đang hiệu lực'); // Đang hiệu lực, Đã thanh lý, Hết hạn
            
            $table->foreign('maPhong')
                  ->references('maPhong')
                  ->on('phong_tros')
                  ->onDelete('cascade');

            $table->foreign('maKhach')
                  ->references('maKhach')
                  ->on('khach_thues')
                  ->onDelete('cascade');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hop_dongs');
    }
};
