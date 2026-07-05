@extends('layouts.app')

@section('title', 'Quản Lý Phòng Trọ')

@section('content')
{{-- ============================================================
     DASHBOARD - FILE KHUNG CHÍNH
     Tổng hợp tất cả các phần giao diện thông qua @include.
     Mỗi @include trỏ đến một file trong thư mục /phan/.
     ============================================================ --}}
<style>
    /* CSS hover cho thẻ phòng trọ (không qua Tailwind compiler) */
    .room-card-container { position: relative; }
    .room-actions {
        position: absolute; right: 12px; top: 12px;
        display: flex; gap: 4px;
        opacity: 0; transition: opacity 0.15s ease-in-out; z-index: 20;
    }
    .room-card-container:hover .room-actions { opacity: 1 !important; }
    .room-status-badge { transition: opacity 0.15s ease-in-out; }
    .room-card-container:hover .room-status-badge { opacity: 0 !important; }
</style>

<div class="flex h-full w-full bg-slate-50 overflow-hidden"
     x-data="rentalApp()"
     x-init="initApp()">

    {{-- Thanh điều hướng bên trái (Sidebar) --}}
    @include('phan.thanh-dieu-huong')

    {{-- Khu vực nội dung chính --}}
    <main class="flex-1 flex flex-col overflow-hidden relative z-10">

        {{-- Thanh công cụ phía trên (Header) --}}
        @include('phan.thanh-cong-cu-tren')

        {{-- Vùng hiển thị nội dung từng tab --}}
        <div class="flex-1 overflow-y-auto p-8 relative">

            {{-- Tab 1: Tổng quan --}}
            @include('phan.tab-tong-quan')

            {{-- Tab 2: Danh sách Phòng trọ --}}
            @include('phan.tab-phong-tro')

            {{-- Tab 3: Quản lý Khách thuê --}}
            @include('phan.tab-khach-thue')

            {{-- Tab 4: Quản lý Hợp đồng --}}
            @include('phan.tab-hop-dong')

            {{-- Tab 5: Quản lý Hóa đơn --}}
            @include('phan.tab-hoa-don')

            {{-- Tab 6: Quản lý Tài sản --}}
            @include('phan.tab-tai-san')

        </div>
    </main>

    {{-- ==================== HỆ THỐNG MODAL ==================== --}}

    {{-- Modal: Thêm / Sửa Phòng trọ --}}
    @include('phan.modal-phong-tro')

    {{-- Modal: Thêm / Sửa Khách thuê --}}
    @include('phan.modal-khach-thue')

    {{-- Modal: Thêm / Sửa Hợp đồng --}}
    @include('phan.modal-hop-dong')

    {{-- Modal: Tạo Hóa đơn & Tính tiền (Design Pattern: Strategy) --}}
    @include('phan.modal-hoa-don')

    {{-- Modal: Thêm / Sửa Tài sản --}}
    @include('phan.modal-tai-san')

</div>

{{-- JavaScript chính - Alpine.js rentalApp() controller --}}
@include('phan.javascript-chinh')

@endsection
