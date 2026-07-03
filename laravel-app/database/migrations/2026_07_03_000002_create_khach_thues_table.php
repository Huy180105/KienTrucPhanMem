<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('khach_thues', function (Blueprint $table) {
            $table->id('maKhach');
            $table->string('hoTen');
            $table->string('cccd')->unique();
            $table->string('sdt');
            $table->string('email')->nullable();
            $table->string('gioiTinh');
            $table->date('ngaySinh')->nullable();
            $table->string('queQuan');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('khach_thues');
    }
};
