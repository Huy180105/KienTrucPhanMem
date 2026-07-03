<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\PhongTro;
use App\Models\KhachThue;
use App\Models\TaiSan;
use App\Models\HopDong;
use App\Models\HoaDon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Truncate existing data to prevent collisions
        Schema::disableForeignKeyConstraints();
        HoaDon::truncate();
        HopDong::truncate();
        TaiSan::truncate();
        KhachThue::truncate();
        PhongTro::truncate();
        User::truncate();
        Schema::enableForeignKeyConstraints();

        // 1. Create Default Admin User
        User::factory()->create([
            'name' => 'Quản trị viên',
            'email' => 'admin@antigravity.com',
            'password' => bcrypt('password'),
        ]);

        // 2. Create Rooms
        $rooms = [
            ['maPhong' => 'P101', 'tenPhong' => 'Phòng 101', 'tang' => 1, 'giaPhong' => 2500000, 'trangThai' => 'Đã thuê'],
            ['maPhong' => 'P102', 'tenPhong' => 'Phòng 102', 'tang' => 1, 'giaPhong' => 2500000, 'trangThai' => 'Trống'],
            ['maPhong' => 'P201', 'tenPhong' => 'Phòng 201', 'tang' => 2, 'giaPhong' => 3000000, 'trangThai' => 'Đã thuê'],
            ['maPhong' => 'P202', 'tenPhong' => 'Phòng 202', 'tang' => 2, 'giaPhong' => 3000000, 'trangThai' => 'Trống'],
            ['maPhong' => 'P301', 'tenPhong' => 'Phòng 301', 'tang' => 3, 'giaPhong' => 3500000, 'trangThai' => 'Đang bảo trì'],
        ];
        foreach ($rooms as $room) {
            PhongTro::create($room);
        }

        // 3. Create Tenants
        $tenants = [
            ['maKhach' => 1, 'hoTen' => 'Nguyễn Văn A', 'cccd' => '012345678901', 'sdt' => '0987654321', 'email' => 'nguyenvana@gmail.com', 'gioiTinh' => 'Nam', 'ngaySinh' => '1995-05-15', 'queQuan' => 'Hà Nội'],
            ['maKhach' => 2, 'hoTen' => 'Trần Thị B', 'cccd' => '098765432109', 'sdt' => '0912345678', 'email' => 'tranthib@gmail.com', 'gioiTinh' => 'Nữ', 'ngaySinh' => '1998-09-20', 'queQuan' => 'Đà Nẵng'],
            ['maKhach' => 3, 'hoTen' => 'Lê Văn C', 'cccd' => '045612378945', 'sdt' => '0905556677', 'email' => 'levanc@gmail.com', 'gioiTinh' => 'Nam', 'ngaySinh' => '2000-01-10', 'queQuan' => 'TP. HCM'],
        ];
        foreach ($tenants as $tenant) {
            KhachThue::create($tenant);
        }

        // 4. Create Assets
        $assets = [
            ['maTaiSan' => 101, 'tenTaiSan' => 'Điều hòa Daikin 9000 BTU', 'soLuong' => 1, 'tinhTrang' => 'Tốt', 'maPhong' => 'P101'],
            ['maTaiSan' => 102, 'tenTaiSan' => 'Tủ lạnh mini Electrolux', 'soLuong' => 1, 'tinhTrang' => 'Tốt', 'maPhong' => 'P101'],
            ['maTaiSan' => 103, 'tenTaiSan' => 'Giường ngủ gỗ xoan đào 1.6m', 'soLuong' => 1, 'tinhTrang' => 'Tốt', 'maPhong' => 'P102'],
            ['maTaiSan' => 104, 'tenTaiSan' => 'Bình nóng lạnh Ariston 20L', 'soLuong' => 1, 'tinhTrang' => 'Tốt', 'maPhong' => 'P201'],
            ['maTaiSan' => 105, 'tenTaiSan' => 'Quạt treo tường Senko', 'soLuong' => 2, 'tinhTrang' => 'Cũ', 'maPhong' => 'P202'],
        ];
        foreach ($assets as $asset) {
            TaiSan::create($asset);
        }

        // 5. Create Contracts
        $contracts = [
            ['maHopDong' => 1001, 'maPhong' => 'P101', 'maKhach' => 1, 'ngayLap' => '2026-01-01', 'ngayBatDau' => '2026-01-01', 'ngayKetThuc' => '2026-12-31', 'tienCoc' => 2500000, 'giaThueThang' => 2500000, 'trangThai' => 'Đang hiệu lực'],
            ['maHopDong' => 1002, 'maPhong' => 'P201', 'maKhach' => 2, 'ngayLap' => '2026-02-01', 'ngayBatDau' => '2026-02-01', 'ngayKetThuc' => '2027-01-31', 'tienCoc' => 3000000, 'giaThueThang' => 3000000, 'trangThai' => 'Đang hiệu lực'],
        ];
        foreach ($contracts as $contract) {
            HopDong::create($contract);
        }

        // 6. Create Invoices
        $invoices = [
            ['maHD' => 5001, 'maHopDong' => 1001, 'thang' => 6, 'nam' => 2026, 'ngayLap' => '2026-06-30', 'tongTien' => 2850000, 'trangThai' => 'Đã thanh toán'],
            ['maHD' => 5002, 'maHopDong' => 1001, 'thang' => 7, 'nam' => 2026, 'ngayLap' => '2026-07-01', 'tongTien' => 2900000, 'trangThai' => 'Chưa thanh toán'],
            ['maHD' => 5003, 'maHopDong' => 1002, 'thang' => 7, 'nam' => 2026, 'ngayLap' => '2026-07-02', 'tongTien' => 3450000, 'trangThai' => 'Chưa thanh toán'],
        ];
        foreach ($invoices as $invoice) {
            HoaDon::create($invoice);
        }
    }
}
