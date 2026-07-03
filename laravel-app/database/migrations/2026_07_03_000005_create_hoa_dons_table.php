<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hoa_dons', function (Blueprint $table) {
            $table->id('maHD');
            $table->unsignedBigInteger('maHopDong');
            $table->integer('thang');
            $table->integer('nam');
            $table->date('ngayLap');
            $table->double('tongTien');
            $table->string('trangThai')->default('Chưa thanh toán'); // Chưa thanh toán, Đã thanh toán
            
            $table->foreign('maHopDong')
                  ->references('maHopDong')
                  ->on('hop_dongs')
                  ->onDelete('cascade');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hoa_dons');
    }
};
