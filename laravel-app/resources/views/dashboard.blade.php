@extends('layouts.app')

@section('title', 'Quản Lý Phòng Trọ')

@section('content')
<style>
    /* Bulletproof hover styles bypassing Tailwind build step */
    .room-card-container {
        position: relative;
    }
    .room-actions {
        position: absolute;
        right: 12px;
        top: 12px;
        display: flex;
        gap: 4px;
        opacity: 0;
        transition: opacity 0.15s ease-in-out;
        z-index: 20;
    }
    .room-card-container:hover .room-actions {
        opacity: 1 !important;
    }
    .room-status-badge {
        transition: opacity 0.15s ease-in-out;
    }
    .room-card-container:hover .room-status-badge {
        opacity: 0 !important;
    }
</style>
<div class="flex h-full w-full bg-slate-50 overflow-hidden" 
     x-data="rentalApp()" 
     x-init="initApp()">

    <!-- Sidebar Navigation -->
    @include('phan-he.thanh-dieu-huong')

    <!-- Main Content Panel -->
    <main class="flex-1 flex flex-col overflow-hidden relative z-10">
        
        <!-- Top Toolbar Header -->
        @include('phan-he.thanh-tieu-de')

        <!-- Dynamic Content Body Container -->
        <div class="flex-1 overflow-y-auto p-8 relative">
            @include('phan-he.tong-quan')
            @include('phan-he.phong-tro')
            @include('phan-he.khach-thue')
            @include('phan-he.hop-dong')
            @include('phan-he.hoa-don')
            @include('phan-he.tai-san')
        </div>
    </main>

    <!-- Modals System -->
    @include('phan-he.modal-phong')
    @include('phan-he.modal-khach-thue')
    @include('phan-he.modal-hop-dong')
    @include('phan-he.modal-hoa-don')
    @include('phan-he.modal-tai-san')

</div>

<!-- Script Engine -->
@include('phan-he.ung-dung-js')
@endsection
