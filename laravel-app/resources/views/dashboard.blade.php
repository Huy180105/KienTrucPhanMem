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

    <!-- Sidebar Navigation (Glassmorphic dark design) -->
    <aside class="w-64 bg-slate-900 text-slate-300 flex flex-col justify-between shadow-2xl relative z-20">
        <div>
            <!-- Header Brand -->
            <div class="h-16 flex items-center px-6 bg-slate-950/40 border-b border-slate-800 gap-3">
                <img src="/logo.png" alt="Logo" class="w-8 h-8 rounded-lg object-cover">
                <div>
                    <h1 class="text-white font-extrabold text-[11px] tracking-wide uppercase leading-tight">QUẢN LÝ PHÒNG TRỌ</h1>
                    <span class="text-[9px] text-indigo-400 font-semibold uppercase tracking-wider block leading-none">Chuyên Nghiệp • Tin Cậy</span>
                </div>
            </div>

            <!-- Profile Info -->
            <div class="px-6 py-6 border-b border-slate-800 bg-slate-950/20">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-gradient-to-tr from-purple-500 to-indigo-500 flex items-center justify-center text-white font-bold text-base shadow-lg">
                        QL
                    </div>
                    <div>
                        <div class="text-sm font-semibold text-white">Quản trị viên</div>
                        <div class="text-xs text-slate-500 flex items-center gap-1">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            Trực tuyến
                        </div>
                    </div>
                </div>
            </div>

            <!-- Navigation Links -->
            <nav class="px-4 py-4 space-y-1">
                <button @click="activeTab = 'dashboard'; selectedRoom = null; searchQuery = ''"
                        :class="activeTab === 'dashboard' ? 'bg-indigo-600/90 text-white shadow-md shadow-indigo-600/20' : 'hover:bg-slate-800/60 hover:text-white'"
                        class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition duration-200 group">
                    <i data-lucide="layout-dashboard" class="w-5 h-5 transition duration-200 group-hover:scale-105"></i>
                    Tổng quan
                </button>

                <button @click="activeTab = 'rooms'; selectedRoom = null; searchQuery = ''"
                        :class="activeTab === 'rooms' ? 'bg-indigo-600/90 text-white shadow-md shadow-indigo-600/20' : 'hover:bg-slate-800/60 hover:text-white'"
                        class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition duration-200 group">
                    <i data-lucide="key-round" class="w-5 h-5 transition duration-200 group-hover:scale-105"></i>
                    Phòng trọ
                </button>

                <button @click="activeTab = 'tenants'; selectedRoom = null; searchQuery = ''"
                        :class="activeTab === 'tenants' ? 'bg-indigo-600/90 text-white shadow-md shadow-indigo-600/20' : 'hover:bg-slate-800/60 hover:text-white'"
                        class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition duration-200 group">
                    <i data-lucide="users" class="w-5 h-5 transition duration-200 group-hover:scale-105"></i>
                    Khách thuê
                </button>

                <button @click="activeTab = 'contracts'; selectedRoom = null; searchQuery = ''"
                        :class="activeTab === 'contracts' ? 'bg-indigo-600/90 text-white shadow-md shadow-indigo-600/20' : 'hover:bg-slate-800/60 hover:text-white'"
                        class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition duration-200 group">
                    <i data-lucide="file-text" class="w-5 h-5 transition duration-200 group-hover:scale-105"></i>
                    Hợp đồng
                </button>

                <button @click="activeTab = 'invoices'; selectedRoom = null; searchQuery = ''"
                        :class="activeTab === 'invoices' ? 'bg-indigo-600/90 text-white shadow-md shadow-indigo-600/20' : 'hover:bg-slate-800/60 hover:text-white'"
                        class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition duration-200 group">
                    <i data-lucide="receipt" class="w-5 h-5 transition duration-200 group-hover:scale-105"></i>
                    Hóa đơn
                </button>

                <button @click="activeTab = 'assets'; selectedRoom = null; searchQuery = ''"
                        :class="activeTab === 'assets' ? 'bg-indigo-600/90 text-white shadow-md shadow-indigo-600/20' : 'hover:bg-slate-800/60 hover:text-white'"
                        class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition duration-200 group">
                    <i data-lucide="package" class="w-5 h-5 transition duration-200 group-hover:scale-105"></i>
                    Tài sản
                </button>
            </nav>
        </div>

        <!-- Footer / Logout Mock -->
        <div class="p-4 border-t border-slate-800">
            <button @click="alert('Đăng xuất thành công! (Mock)')" 
                    class="w-full flex items-center justify-center gap-2 bg-slate-800 hover:bg-rose-950/20 hover:text-rose-400 text-slate-400 py-2.5 px-4 rounded-xl text-sm font-medium transition duration-200 border border-slate-800 hover:border-rose-900/30">
                <i data-lucide="log-out" class="w-4 h-4"></i>
                Đăng xuất
            </button>
        </div>
    </aside>

    <!-- Main Content Panel -->
    <main class="flex-1 flex flex-col overflow-hidden relative z-10">
        
        <!-- Top Toolbar Header -->
        <header class="h-16 bg-white border-b border-slate-200 px-8 flex items-center justify-between shadow-sm flex-shrink-0">
            <div class="flex items-center gap-2">
                <h2 class="text-xl font-bold text-slate-800" 
                    x-text="tabTitles[activeTab]"></h2>
            </div>
            
            <div class="flex items-center gap-4">
                <!-- Search bar if not in dashboard -->
                <div x-show="activeTab !== 'dashboard'" class="relative w-64">
                    <input type="text" 
                           x-model="searchQuery" 
                           placeholder="Tìm kiếm nhanh..." 
                           class="w-full pl-9 pr-4 py-1.5 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition duration-150">
                    <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3 top-2.5"></i>
                </div>

                <!-- Profile summary (Interactive role toggler - NFR-03) -->
                <div class="flex items-center gap-3 pl-4 border-l border-slate-200">
                    <button @click="userRole = (userRole === 'admin' ? 'viewer' : 'admin'); changeRole()" 
                            class="flex items-center gap-2 focus:outline-none hover:opacity-80 transition group"
                            title="Click để chuyển đổi vai trò (NFR-03)">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-sm shadow-sm transition"
                             :class="userRole === 'admin' ? 'bg-indigo-50 text-indigo-600' : 'bg-amber-50 text-amber-600'"
                             x-text="userRole === 'admin' ? 'AD' : 'KS'">
                            AD
                        </div>
                        <div class="text-left">
                            <span class="text-xs font-bold block text-slate-800">{{ Auth::user()->name ?? 'Admin' }}</span>
                            <span class="text-[9px] text-slate-400 font-medium block" x-text="userRole === 'admin' ? 'Quyền: Quản trị viên' : 'Quyền: Chỉ xem'">Quyền: Quản trị viên</span>
                        </div>
                    </button>

                    <!-- Logout button -->
                    <form method="POST" action="/logout" style="display:inline;">
                        @csrf
                        <button type="submit" 
                                title="Đăng xuất"
                                style="display:flex;align-items:center;gap:6px;padding:6px 12px;background:#fff1f2;border:1px solid #fecdd3;border-radius:10px;color:#e11d48;font-size:12px;font-weight:700;cursor:pointer;transition:all 0.2s;font-family:inherit;"
                                onmouseover="this.style.background='#ffe4e6'"
                                onmouseout="this.style.background='#fff1f2'">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" x2="9" y1="12" y2="12"/></svg>
                            Đăng xuất
                        </button>
                    </form>
                </div>
            </div>
        </header>

        <!-- Dynamic Content Body Container -->
        <div class="flex-1 overflow-y-auto p-8 relative">

            <!-- 1. TAB: TỔNG QUAN -->
            <div x-show="activeTab === 'dashboard'" x-transition class="space-y-8">
                <!-- Stats Grid -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <!-- Revenue Card (Purple Gradient) -->
                    <div class="bg-gradient-to-br from-indigo-600 to-purple-700 text-white rounded-2xl p-6 shadow-lg shadow-indigo-600/10 flex flex-col justify-between relative overflow-hidden group">
                        <div class="absolute -right-4 -bottom-4 opacity-10 text-white group-hover:scale-110 transition duration-300">
                            <i data-lucide="banknote" class="w-32 h-32"></i>
                        </div>
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-indigo-200 text-xs font-semibold uppercase tracking-wider">Tổng Doanh Thu</p>
                                <h3 class="text-2xl font-extrabold mt-1" x-text="formatCurrency(totalRevenue())"></h3>
                            </div>
                            <span class="p-2.5 bg-white/10 rounded-xl text-white">
                                <i data-lucide="trending-up" class="w-5 h-5"></i>
                            </span>
                        </div>
                        <div class="mt-4 pt-4 border-t border-white/10 flex items-center justify-between text-xs text-indigo-200">
                            <span>Thanh toán tích lũy</span>
                            <span class="font-semibold text-emerald-300 flex items-center gap-0.5">
                                <i data-lucide="arrow-up-right" class="w-3 h-3"></i> 100% Thực thu
                            </span>
                        </div>
                    </div>

                    <!-- Rooms Status Card -->
                    <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm flex flex-col justify-between relative overflow-hidden group">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-slate-400 text-xs font-semibold uppercase tracking-wider">Trạng Thái Phòng</p>
                                <h3 class="text-2xl font-bold mt-1 text-slate-800">
                                    <span x-text="rentedRoomsCount()"></span> / <span x-text="rooms.length"></span>
                                    <span class="text-xs text-slate-400 font-normal">đã thuê</span>
                                </h3>
                            </div>
                            <span class="p-2.5 bg-indigo-50 rounded-xl text-indigo-600">
                                <i data-lucide="key-round" class="w-5 h-5"></i>
                            </span>
                        </div>
                        <div class="mt-4">
                            <!-- Progress Bar -->
                            <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                                <div class="bg-indigo-600 h-full transition-all duration-500" 
                                     :style="`width: ${(rentedRoomsCount() / (rooms.length || 1)) * 100}%`"></div>
                            </div>
                            <div class="mt-3 flex justify-between text-xs text-slate-500">
                                <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-emerald-500"></span> Trống: <strong class="text-slate-700" x-text="vacantRoomsCount()"></strong></span>
                                <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-amber-500"></span> Bảo trì: <strong class="text-slate-700" x-text="maintenanceRoomsCount()"></strong></span>
                            </div>
                        </div>
                    </div>

                    <!-- Active Contracts Card -->
                    <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm flex flex-col justify-between relative overflow-hidden group">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-slate-400 text-xs font-semibold uppercase tracking-wider">Hợp Đồng Hiệu Lực</p>
                                <h3 class="text-2xl font-bold mt-1 text-slate-800" x-text="activeContractsCount() + ' Hợp đồng'"></h3>
                            </div>
                            <span class="p-2.5 bg-emerald-50 rounded-xl text-emerald-600">
                                <i data-lucide="file-check-2" class="w-5 h-5"></i>
                            </span>
                        </div>
                        <div class="mt-4 pt-4 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                            <span>Khách thuê lưu trú:</span>
                            <span class="font-bold text-slate-700" x-text="tenants.length + ' người'"></span>
                        </div>
                    </div>

                    <!-- Unpaid Invoices Card -->
                    <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm flex flex-col justify-between relative overflow-hidden group">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-slate-400 text-xs font-semibold uppercase tracking-wider">Hóa Đơn Chưa Thu</p>
                                <h3 class="text-2xl font-bold mt-1 text-slate-800" x-text="unpaidInvoicesCount() + ' Hóa đơn'"></h3>
                            </div>
                            <span class="p-2.5 bg-rose-50 rounded-xl text-rose-600" :class="unpaidInvoicesCount() > 0 ? 'animate-pulse' : ''">
                                <i data-lucide="alert-circle" class="w-5 h-5"></i>
                            </span>
                        </div>
                        <div class="mt-4 pt-4 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                            <span>Số tiền chưa thu:</span>
                            <span class="font-bold text-rose-600" x-text="formatCurrency(unpaidInvoicesSum())"></span>
                        </div>
                    </div>
                </div>

                <!-- Visual grid for details -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Quick action buttons panel -->
                    <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm lg:col-span-1 space-y-6">
                        <h4 class="text-sm font-bold text-slate-800 uppercase tracking-wider">Lối Tắt Nghiệp Vụ</h4>
                        
                        <div class="grid grid-cols-1 gap-3">
                            <button @click="openAddRoom()" 
                                    class="w-full flex items-center gap-3 p-3 bg-slate-50 hover:bg-indigo-50 border border-slate-200 hover:border-indigo-200 rounded-xl text-left transition duration-150 group">
                                <span class="p-2 bg-indigo-100 group-hover:bg-indigo-600 text-indigo-600 group-hover:text-white rounded-lg transition duration-150">
                                    <i data-lucide="plus-circle" class="w-4 h-4"></i>
                                </span>
                                <div>
                                    <div class="text-xs font-bold text-slate-700 group-hover:text-indigo-900">Thêm Phòng Mới</div>
                                    <div class="text-[10px] text-slate-400">Thiết lập thêm phòng vào hệ thống</div>
                                </div>
                            </button>

                            <button @click="openAddTenant()" 
                                    class="w-full flex items-center gap-3 p-3 bg-slate-50 hover:bg-indigo-50 border border-slate-200 hover:border-indigo-200 rounded-xl text-left transition duration-150 group">
                                <span class="p-2 bg-blue-100 group-hover:bg-blue-600 text-blue-600 group-hover:text-white rounded-lg transition duration-150">
                                    <i data-lucide="user-plus" class="w-4 h-4"></i>
                                </span>
                                <div>
                                    <div class="text-xs font-bold text-slate-700 group-hover:text-indigo-900">Thêm Khách Thuê</div>
                                    <div class="text-[10px] text-slate-400">Đăng ký thông tin khách mới</div>
                                </div>
                            </button>

                            <button @click="openAddContract()" 
                                    class="w-full flex items-center gap-3 p-3 bg-slate-50 hover:bg-indigo-50 border border-slate-200 hover:border-indigo-200 rounded-xl text-left transition duration-150 group">
                                <span class="p-2 bg-emerald-100 group-hover:bg-emerald-600 text-emerald-600 group-hover:text-white rounded-lg transition duration-150">
                                    <i data-lucide="file-plus-2" class="w-4 h-4"></i>
                                </span>
                                <div>
                                    <div class="text-xs font-bold text-slate-700 group-hover:text-indigo-900">Lập Hợp Đồng Mới</div>
                                    <div class="text-[10px] text-slate-400">Cho thuê phòng, ghi nhận cọc</div>
                                </div>
                            </button>

                            <button @click="openAddInvoice()" 
                                    class="w-full flex items-center gap-3 p-3 bg-slate-50 hover:bg-indigo-50 border border-slate-200 hover:border-indigo-200 rounded-xl text-left transition duration-150 group">
                                <span class="p-2 bg-amber-100 group-hover:bg-amber-600 text-amber-600 group-hover:text-white rounded-lg transition duration-150">
                                    <i data-lucide="calculator" class="w-4 h-4"></i>
                                </span>
                                <div>
                                    <div class="text-xs font-bold text-slate-700 group-hover:text-indigo-900">Tính Hóa Đơn Tháng</div>
                                    <div class="text-[10px] text-slate-400">Tính tiền điện nước theo Strategy</div>
                                </div>
                            </button>
                        </div>
                    </div>

                    <!-- Recent Activity Table / Overview of active rooms -->
                    <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm lg:col-span-2">
                        <div class="flex justify-between items-center mb-6">
                            <h4 class="text-sm font-bold text-slate-800 uppercase tracking-wider">Danh Sách Phòng Trọ Hoạt Động</h4>
                            <button @click="activeTab = 'rooms'" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 transition">Xem tất cả &rarr;</button>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="border-b border-slate-100 text-slate-400 text-xs font-bold uppercase pb-3">
                                        <th class="py-3 px-2">Mã Phòng</th>
                                        <th class="py-3 px-2">Tên Phòng</th>
                                        <th class="py-3 px-2">Tầng</th>
                                        <th class="py-3 px-2">Giá Thuê</th>
                                        <th class="py-3 px-2">Trạng Thái</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 text-sm">
                                    <template x-for="room in rooms.slice(0, 4)" :key="room.maPhong">
                                        <tr class="hover:bg-slate-50/50 transition duration-150">
                                            <td class="py-3 px-2 font-bold text-slate-800" x-text="room.maPhong"></td>
                                            <td class="py-3 px-2 text-slate-600" x-text="room.tenPhong"></td>
                                            <td class="py-3 px-2 text-slate-600" x-text="'Tầng ' + room.tang"></td>
                                            <td class="py-3 px-2 font-semibold text-slate-800" x-text="formatCurrency(room.giaPhong)"></td>
                                            <td class="py-3 px-2">
                                                <span :class="getRoomStatusClass(room.trangThai)" 
                                                      class="inline-block px-2.5 py-0.5 rounded-full text-xs font-semibold" 
                                                      x-text="room.trangThai"></span>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. TAB: DANH SÁCH PHÒNG TRỌ -->
            <div x-show="activeTab === 'rooms'" x-transition class="space-y-6">
                <!-- Toolbar -->
                <div class="flex justify-between items-center bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
                    <div class="flex items-center gap-4">
                        <!-- Status Filter -->
                        <span class="text-xs font-bold text-slate-450 uppercase">Bộ lọc:</span>
                        <div class="flex gap-2">
                            <button @click="roomFilter = 'all'" :class="roomFilter === 'all' ? 'bg-slate-800 text-white' : 'bg-slate-100 hover:bg-slate-200 text-slate-600'" class="px-3 py-1 rounded-lg text-xs font-bold transition">Tất cả</button>
                            <button @click="roomFilter = 'available'" :class="roomFilter === 'available' ? 'bg-slate-800 text-white' : 'bg-slate-100 hover:bg-slate-200 text-slate-600'" class="px-3 py-1 rounded-lg text-xs font-bold transition">Trống</button>
                            <button @click="roomFilter = 'rented'" :class="roomFilter === 'rented' ? 'bg-slate-800 text-white' : 'bg-slate-100 hover:bg-slate-200 text-slate-600'" class="px-3 py-1 rounded-lg text-xs font-bold transition">Đã thuê</button>
                        </div>
                    </div>
                    <button @click="openAddRoom()" class="flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-xl text-xs font-bold shadow-md shadow-indigo-600/10 transition">
                        <i data-lucide="plus" class="w-4 h-4"></i> Thêm phòng
                    </button>
                </div>

                <!-- Grid/Cards for Rooms -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    <template x-for="room in filteredRooms()" :key="room.maPhong">
                        <div @click="selectRoom(room)" 
                             class="bg-white border border-slate-200 hover:border-indigo-400 rounded-2xl p-5 shadow-sm hover:shadow-md cursor-pointer transition-all duration-200 group flex flex-col justify-between relative overflow-hidden room-card-container"
                             :class="selectedRoom && selectedRoom.maPhong === room.maPhong ? 'ring-2 ring-indigo-500 border-transparent bg-indigo-50/20' : ''">
                             
                             <!-- Direct Edit/Delete Buttons (Hover visible) -->
                             <div class="absolute right-3 top-3 gap-1 z-20 room-actions">
                                 <button @click.stop="openEditRoom(room)" class="p-1.5 bg-white hover:bg-slate-50 border border-slate-200 rounded-lg text-slate-500 hover:text-indigo-600 transition shadow-sm" title="Chỉnh sửa">
                                     <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg>
                                 </button>
                                 <button @click.stop="deleteRoom(room.maPhong)" class="p-1.5 bg-white hover:bg-rose-50 border border-slate-200 rounded-lg text-slate-500 hover:text-rose-600 transition shadow-sm" title="Xóa phòng">
                                     <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/></svg>
                                 </button>
                             </div>

                             <div>
                                <div class="flex justify-between items-start mb-4">
                                    <div class="p-3 bg-slate-50 group-hover:bg-indigo-50 text-slate-600 group-hover:text-indigo-600 rounded-xl transition duration-150">
                                        <i data-lucide="key" class="w-5 h-5"></i>
                                    </div>
                                    <span :class="getRoomStatusClass(room.trangThai)" 
                                          class="px-2.5 py-0.5 rounded-full text-xs font-bold transition duration-155 room-status-badge" 
                                          x-text="room.trangThai"></span>
                                </div>
                                <h4 class="text-base font-extrabold text-slate-800 mb-1" x-text="room.tenPhong"></h4>
                                <p class="text-xs text-slate-400 mb-3" x-text="'Tầng ' + room.tang"></p>
                            </div>

                            <div class="pt-4 border-t border-slate-100 flex justify-between items-center">
                                <span class="text-xs text-slate-400">Đơn giá:</span>
                                <span class="text-sm font-bold text-slate-800" x-text="formatCurrency(room.giaPhong) + '/tháng'"></span>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Room Detail Slide-Out Drawer (Only shows when room is selected) -->
                <div x-show="selectedRoom" 
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-x-12"
                     x-transition:enter-end="opacity-100 translate-x-0"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-x-0"
                     x-transition:leave-end="opacity-0 translate-x-12"
                     class="bg-white border border-slate-200 rounded-2xl p-6 shadow-lg space-y-6">
                    <div class="flex justify-between items-center border-b border-slate-100 pb-4">
                        <div class="flex items-center gap-3">
                            <span class="p-2.5 bg-indigo-50 text-indigo-600 rounded-xl"><i data-lucide="info" class="w-5 h-5"></i></span>
                            <div>
                                <h3 class="text-base font-bold text-slate-800" x-text="'Chi tiết ' + selectedRoom?.tenPhong + ' (' + selectedRoom?.maPhong + ')'"></h3>
                                <p class="text-xs text-slate-400">Cấu trúc dữ liệu theo Class Diagram</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <button @click="openEditRoom(selectedRoom)" class="p-1.5 hover:bg-slate-100 text-slate-500 hover:text-slate-800 rounded-lg transition" title="Chỉnh sửa"><i data-lucide="edit-3" class="w-4 h-4"></i></button>
                            <button @click="deleteRoom(selectedRoom.maPhong)" class="p-1.5 hover:bg-rose-50 text-slate-500 hover:text-rose-600 rounded-lg transition" title="Xóa phòng"><i data-lucide="trash-2" class="w-4 h-4"></i></button>
                            <button @click="selectedRoom = null" class="p-1.5 hover:bg-slate-100 text-slate-500 hover:text-slate-800 rounded-lg transition" title="Đóng"><i data-lucide="x" class="w-4 h-4"></i></button>
                        </div>
                    </div>

                    <!-- Room Meta attributes -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 bg-slate-50 p-4 rounded-xl border border-slate-100 text-sm">
                        <div>
                            <span class="text-xs text-slate-400 block">Mã phòng:</span>
                            <strong class="text-slate-800" x-text="selectedRoom?.maPhong"></strong>
                        </div>
                        <div>
                            <span class="text-xs text-slate-400 block">Tầng lầu:</span>
                            <strong class="text-slate-800" x-text="selectedRoom?.tang"></strong>
                        </div>
                        <div>
                            <span class="text-xs text-slate-400 block">Đơn giá hợp đồng:</span>
                            <strong class="text-slate-800" x-text="formatCurrency(selectedRoom?.giaPhong)"></strong>
                        </div>
                        <div>
                            <span class="text-xs text-slate-400 block">Trạng thái:</span>
                            <span :class="getRoomStatusClass(selectedRoom?.trangThai)" class="inline-block px-2.5 py-0.5 mt-0.5 rounded-full text-xs font-bold" x-text="selectedRoom?.trangThai"></span>
                        </div>
                    </div>

                    <!-- Inner Accordions / Linked Entities (Relations 1 -> 0..*) -->
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <!-- Linked Assets -->
                        <div class="border border-slate-200 rounded-xl p-4 space-y-4 bg-white shadow-sm">
                            <div class="flex justify-between items-center pb-2 border-b border-slate-100">
                                <h5 class="text-xs font-bold text-slate-800 uppercase flex items-center gap-1.5">
                                    <i data-lucide="package" class="w-4 h-4 text-indigo-500"></i> Danh Sách Tài Sản (<span x-text="getRoomAssets(selectedRoom?.maPhong).length"></span>)
                                </h5>
                                <button @click="openAddAssetForRoom(selectedRoom?.maPhong)" class="text-xs font-bold text-indigo-600 hover:underline">+ Phân bổ</button>
                            </div>
                            <div class="space-y-2 max-h-48 overflow-y-auto">
                                <template x-if="getRoomAssets(selectedRoom?.maPhong).length === 0">
                                    <p class="text-xs text-slate-400 py-4 text-center">Chưa phân bổ tài sản nào cho phòng này.</p>
                                </template>
                                <template x-for="asset in getRoomAssets(selectedRoom?.maPhong)" :key="asset.maTaiSan">
                                    <div class="flex justify-between items-center text-xs p-2 bg-slate-50 rounded-lg border border-slate-100">
                                        <div>
                                            <span class="font-bold text-slate-800" x-text="asset.tenTaiSan"></span>
                                            <span class="text-slate-400" x-text="' - SL: ' + asset.soLuong"></span>
                                        </div>
                                        <span class="px-1.5 py-0.5 rounded bg-blue-50 text-blue-600 font-medium" x-text="asset.tinhTrang"></span>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- Linked Contract -->
                        <div class="border border-slate-200 rounded-xl p-4 space-y-4 bg-white shadow-sm">
                            <div class="flex justify-between items-center pb-2 border-b border-slate-100">
                                <h5 class="text-xs font-bold text-slate-800 uppercase flex items-center gap-1.5">
                                    <i data-lucide="file-text" class="w-4 h-4 text-emerald-500"></i> Hợp Đồng Hiện Tại
                                </h5>
                            </div>
                            <div>
                                <template x-if="!getRoomContract(selectedRoom?.maPhong)">
                                    <div class="text-center py-4">
                                        <p class="text-xs text-slate-400 mb-2">Phòng trống, chưa có hợp đồng hiệu lực.</p>
                                        <button @click="openAddContractForRoom(selectedRoom?.maPhong, selectedRoom?.giaPhong)" class="px-3 py-1 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-bold transition">Tạo hợp đồng</button>
                                    </div>
                                </template>
                                <template x-if="getRoomContract(selectedRoom?.maPhong)">
                                    <div class="text-xs space-y-2">
                                        <div class="flex justify-between">
                                            <span class="text-slate-450">Khách thuê:</span>
                                            <strong class="text-slate-800" x-text="getTenantName(getRoomContract(selectedRoom?.maPhong).maKhach)"></strong>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-slate-450">Ngày bắt đầu:</span>
                                            <span class="text-slate-800" x-text="formatDate(getRoomContract(selectedRoom?.maPhong).ngayBatDau)"></span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-slate-450">Tiền cọc:</span>
                                            <strong class="text-slate-800" x-text="formatCurrency(getRoomContract(selectedRoom?.maPhong).tienCoc)"></strong>
                                        </div>
                                        <div class="flex justify-between items-center pt-2 border-t border-slate-100 gap-2">
                                            <span class="px-2 py-0.5 rounded bg-emerald-50 text-emerald-600 font-bold" x-text="getRoomContract(selectedRoom?.maPhong).trangThai"></span>
                                            <div class="flex gap-1">
                                                <button @click="sendEmailReminder(getRoomContract(selectedRoom?.maPhong), $event)" class="px-2.5 py-1 bg-indigo-50 hover:bg-indigo-100 text-indigo-600 rounded-lg font-bold transition">Gửi Mail</button>
                                                <button @click="terminateContract(getRoomContract(selectedRoom?.maPhong).maHopDong)" class="px-2.5 py-1 bg-rose-50 hover:bg-rose-100 text-rose-600 rounded-lg font-bold transition">Thanh lý</button>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- Linked Invoices -->
                        <div class="border border-slate-200 rounded-xl p-4 space-y-4 bg-white shadow-sm">
                            <div class="flex justify-between items-center pb-2 border-b border-slate-100">
                                <h5 class="text-xs font-bold text-slate-800 uppercase flex items-center gap-1.5">
                                    <i data-lucide="receipt" class="w-4 h-4 text-purple-500"></i> Hóa Đơn Gần Đây
                                </h5>
                                <template x-if="getRoomContract(selectedRoom?.maPhong)">
                                    <button @click="openAddInvoiceForContract(getRoomContract(selectedRoom?.maPhong).maHopDong)" class="text-xs font-bold text-indigo-600 hover:underline">+ Tạo mới</button>
                                </template>
                            </div>
                            <div class="space-y-2 max-h-48 overflow-y-auto">
                                <template x-if="!getRoomContract(selectedRoom?.maPhong)">
                                    <p class="text-xs text-slate-400 py-4 text-center">Vui lòng lập hợp đồng trước khi tính hóa đơn.</p>
                                </template>
                                <template x-if="getRoomContract(selectedRoom?.maPhong) && getContractInvoices(getRoomContract(selectedRoom?.maPhong).maHopDong).length === 0">
                                    <p class="text-xs text-slate-400 py-4 text-center">Chưa có hóa đơn nào được xuất.</p>
                                </template>
                                <template x-for="invoice in getContractInvoices(getRoomContract(selectedRoom?.maPhong)?.maHopDong)" :key="invoice.maHD">
                                    <div class="flex justify-between items-center text-xs p-2 bg-slate-50 rounded-lg border border-slate-100">
                                        <div>
                                            <span class="font-bold text-slate-800" x-text="'T' + invoice.thang + '/' + invoice.nam"></span>
                                            <span class="text-slate-500 ml-2" x-text="formatCurrency(invoice.tongTien)"></span>
                                        </div>
                                        <span :class="invoice.trangThai === 'Đã thanh toán' ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600'" 
                                              class="px-1.5 py-0.5 rounded font-bold" 
                                              x-text="invoice.trangThai"></span>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3. TAB: QUẢN LÝ KHÁCH THUÊ -->
            <div x-show="activeTab === 'tenants'" x-transition class="space-y-6">
                <!-- Toolbar -->
                <div class="flex justify-between items-center bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
                    <div>
                        <p class="text-xs text-slate-400 font-medium">Danh sách toàn bộ thông tin khách thuê phòng</p>
                    </div>
                    <button @click="openAddTenant()" class="flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-xl text-xs font-bold shadow-md shadow-indigo-600/10 transition">
                        <i data-lucide="user-plus" class="w-4 h-4"></i> Đăng ký khách
                    </button>
                </div>

                <!-- Table -->
                <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="border-b border-slate-100 bg-slate-50 text-slate-400 text-xs font-bold uppercase">
                                    <th class="py-3 px-6">Mã Khách</th>
                                    <th class="py-3 px-6">Họ Tên</th>
                                    <th class="py-3 px-6">Số CCCD</th>
                                    <th class="py-3 px-6">Số Điện Thoại</th>
                                    <th class="py-3 px-6">Giới Tính</th>
                                    <th class="py-3 px-6">Ngày Sinh</th>
                                    <th class="py-3 px-6">Quê Quán</th>
                                    <th class="py-3 px-6 text-center">Thao Tác</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-sm text-slate-600">
                                <template x-for="tenant in filteredTenants()" :key="tenant.maKhach">
                                    <tr class="hover:bg-slate-50/50 transition">
                                        <td class="py-4 px-6 font-bold text-slate-800" x-text="'K' + tenant.maKhach"></td>
                                        <td class="py-4 px-6 font-semibold text-slate-800" x-text="tenant.hoTen"></td>
                                        <td class="py-4 px-6" x-text="tenant.cccd"></td>
                                        <td class="py-4 px-6 font-mono" x-text="tenant.sdt"></td>
                                        <td class="py-4 px-6">
                                            <span :class="tenant.gioiTinh === 'Nam' ? 'bg-blue-50 text-blue-600' : 'bg-pink-50 text-pink-600'" 
                                                  class="px-2 py-0.5 rounded text-xs font-bold" 
                                                  x-text="tenant.gioiTinh"></span>
                                        </td>
                                        <td class="py-4 px-6" x-text="formatDate(tenant.ngaySinh)"></td>
                                        <td class="py-4 px-6" x-text="tenant.queQuan"></td>
                                        <td class="py-4 px-6">
                                            <div class="flex justify-center items-center gap-2">
                                                <button @click="openEditTenant(tenant)" class="p-1 hover:bg-slate-100 text-indigo-600 rounded transition"><i data-lucide="edit" class="w-4 h-4"></i></button>
                                                <button @click="deleteTenant(tenant.maKhach)" class="p-1 hover:bg-rose-50 text-rose-600 rounded transition"><i data-lucide="trash-2" class="w-4 h-4"></i></button>
                                            </div>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- 4. TAB: QUẢN LÝ HỢP ĐỒNG -->
            <div x-show="activeTab === 'contracts'" x-transition class="space-y-6">
                <!-- Toolbar -->
                <div class="flex justify-between items-center bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
                    <div class="flex items-center gap-4">
                        <span class="text-xs font-bold text-slate-450 uppercase">Hiệu lực:</span>
                        <div class="flex gap-2">
                            <button @click="contractFilter = 'all'" :class="contractFilter === 'all' ? 'bg-slate-800 text-white' : 'bg-slate-100 hover:bg-slate-200 text-slate-600'" class="px-3 py-1 rounded-lg text-xs font-bold transition">Tất cả</button>
                            <button @click="contractFilter = 'active'" :class="contractFilter === 'active' ? 'bg-slate-800 text-white' : 'bg-slate-100 hover:bg-slate-200 text-slate-600'" class="px-3 py-1 rounded-lg text-xs font-bold transition">Đang hiệu lực</button>
                            <button @click="contractFilter = 'terminated'" :class="contractFilter === 'terminated' ? 'bg-slate-800 text-white' : 'bg-slate-100 hover:bg-slate-200 text-slate-600'" class="px-3 py-1 rounded-lg text-xs font-bold transition">Đã thanh lý</button>
                        </div>
                    </div>
                    <button @click="openAddContract()" class="flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-xl text-xs font-bold shadow-md shadow-indigo-600/10 transition">
                        <i data-lucide="file-plus" class="w-4 h-4"></i> Lập hợp đồng
                    </button>
                </div>

                <!-- Table -->
                <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="border-b border-slate-100 bg-slate-50 text-slate-400 text-xs font-bold uppercase">
                                    <th class="py-3 px-6">Mã HĐ</th>
                                    <th class="py-3 px-6">Phòng</th>
                                    <th class="py-3 px-6">Khách Thuê</th>
                                    <th class="py-3 px-6">Ngày Lập</th>
                                    <th class="py-3 px-6">Ngày Bắt Đầu</th>
                                    <th class="py-3 px-6">Ngày Kết Thúc</th>
                                    <th class="py-3 px-6">Tiền Cọc</th>
                                    <th class="py-3 px-6">Đơn Giá</th>
                                    <th class="py-3 px-6">Trạng Thái</th>
                                    <th class="py-3 px-6 text-center">Hành Động</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-sm text-slate-600">
                                <template x-for="contract in filteredContracts()" :key="contract.maHopDong">
                                    <tr class="hover:bg-slate-50/50 transition">
                                        <td class="py-4 px-6 font-bold text-slate-800" x-text="contract.maHopDong"></td>
                                        <td class="py-4 px-6 font-bold text-indigo-600" x-text="contract.maPhong"></td>
                                        <td class="py-4 px-6 font-semibold text-slate-800" x-text="getTenantName(contract.maKhach)"></td>
                                        <td class="py-4 px-6" x-text="formatDate(contract.ngayLap)"></td>
                                        <td class="py-4 px-6" x-text="formatDate(contract.ngayBatDau)"></td>
                                        <td class="py-4 px-6" x-text="formatDate(contract.ngayKetThuc)"></td>
                                        <td class="py-4 px-6 font-semibold text-emerald-600" x-text="formatCurrency(contract.tienCoc)"></td>
                                        <td class="py-4 px-6 font-bold text-slate-800" x-text="formatCurrency(contract.giaThueThang)"></td>
                                        <td class="py-4 px-6">
                                            <span :class="contract.trangThai === 'Đang hiệu lực' ? 'bg-emerald-50 text-emerald-600' : 'bg-slate-100 text-slate-500'" 
                                                  class="px-2.5 py-0.5 rounded-full text-xs font-bold" 
                                                  x-text="contract.trangThai"></span>
                                        </td>
                                        <td class="py-4 px-6">
                                            <div class="flex justify-center gap-2">
                                                <template x-if="contract.trangThai === 'Đang hiệu lực'">
                                                    <div class="flex gap-1">
                                                        <button @click="sendEmailReminder(contract, $event)" class="px-2.5 py-1 bg-indigo-50 hover:bg-indigo-100 text-indigo-650 rounded-lg text-xs font-bold transition">Gửi Mail</button>
                                                        <button @click="terminateContract(contract.maHopDong)" class="px-2.5 py-1 bg-rose-50 hover:bg-rose-100 text-rose-600 rounded-lg text-xs font-bold transition">Thanh lý</button>
                                                    </div>
                                                </template>
                                                <button @click="deleteContract(contract.maHopDong)" class="p-1 hover:bg-rose-50 text-rose-600 rounded transition"><i data-lucide="trash-2" class="w-4 h-4"></i></button>
                                            </div>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- 5. TAB: QUẢN LÝ HÓA ĐƠN -->
            <div x-show="activeTab === 'invoices'" x-transition class="space-y-6">
                <!-- Toolbar -->
                <div class="flex justify-between items-center bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
                    <div class="flex items-center gap-4">
                        <span class="text-xs font-bold text-slate-450 uppercase">Thanh toán:</span>
                        <div class="flex gap-2">
                            <button @click="invoiceFilter = 'all'" :class="invoiceFilter === 'all' ? 'bg-slate-800 text-white' : 'bg-slate-100 hover:bg-slate-200 text-slate-600'" class="px-3 py-1 rounded-lg text-xs font-bold transition">Tất cả</button>
                            <button @click="invoiceFilter = 'unpaid'" :class="invoiceFilter === 'unpaid' ? 'bg-slate-800 text-white' : 'bg-slate-100 hover:bg-slate-200 text-slate-600'" class="px-3 py-1 rounded-lg text-xs font-bold transition">Chưa thanh toán</button>
                            <button @click="invoiceFilter = 'paid'" :class="invoiceFilter === 'paid' ? 'bg-slate-800 text-white' : 'bg-slate-100 hover:bg-slate-200 text-slate-600'" class="px-3 py-1 rounded-lg text-xs font-bold transition">Đã thanh toán</button>
                        </div>
                    </div>
                    <button @click="openAddInvoice()" class="flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-xl text-xs font-bold shadow-md shadow-indigo-600/10 transition">
                        <i data-lucide="calculator" class="w-4 h-4"></i> Tính hóa đơn
                    </button>
                </div>

                <!-- Table -->
                <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="border-b border-slate-100 bg-slate-50 text-slate-400 text-xs font-bold uppercase">
                                    <th class="py-3 px-6">Mã Hóa Đơn</th>
                                    <th class="py-3 px-6">Phòng</th>
                                    <th class="py-3 px-6">Kỳ Hóa Đơn</th>
                                    <th class="py-3 px-6">Ngày Lập</th>
                                    <th class="py-3 px-6">Tổng Số Tiền</th>
                                    <th class="py-3 px-6">Trạng Thái</th>
                                    <th class="py-3 px-6 text-center">Thao Tác</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-sm text-slate-600">
                                <template x-for="invoice in filteredInvoices()" :key="invoice.maHD">
                                    <tr class="hover:bg-slate-50/50 transition">
                                        <td class="py-4 px-6 font-bold text-slate-800" x-text="'HD' + invoice.maHD"></td>
                                        <td class="py-4 px-6 font-bold text-indigo-600" x-text="getRoomIdFromContract(invoice.maHopDong)"></td>
                                        <td class="py-4 px-6 font-semibold text-slate-800" x-text="'Tháng ' + invoice.thang + '/' + invoice.nam"></td>
                                        <td class="py-4 px-6" x-text="formatDate(invoice.ngayLap)"></td>
                                        <td class="py-4 px-6 font-bold text-slate-800" x-text="formatCurrency(invoice.tongTien)"></td>
                                        <td class="py-4 px-6">
                                            <span :class="invoice.trangThai === 'Đã thanh toán' ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600'" 
                                                  class="px-2.5 py-0.5 rounded-full text-xs font-bold" 
                                                  x-text="invoice.trangThai"></span>
                                        </td>
                                        <td class="py-4 px-6">
                                            <div class="flex justify-center gap-2">
                                                <template x-if="invoice.trangThai === 'Chưa thanh toán'">
                                                    <button @click="payInvoice(invoice.maHD)" class="px-2.5 py-1 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-bold transition">Thanh toán</button>
                                                </template>
                                                <button @click="sendInvoiceEmail(invoice.maHD, $event)" 
                                                        class="p-1.5 hover:bg-indigo-50 text-indigo-600 rounded-lg transition" 
                                                        title="Gửi Email Hóa Đơn">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
                                                </button>
                                                <button @click="deleteInvoice(invoice.maHD)" 
                                                        :class="invoice.trangThai === 'Chưa thanh toán' ? 'opacity-35 cursor-not-allowed text-slate-400' : 'hover:bg-rose-50 text-rose-600'" 
                                                        class="p-1.5 rounded-lg transition" 
                                                        :title="invoice.trangThai === 'Chưa thanh toán' ? 'Không thể xóa hóa đơn chưa thanh toán' : 'Xóa hóa đơn'">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/><line x1="10" x2="10" y1="11" y2="17"/><line x1="14" x2="14" y1="11" y2="17"/></svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- 6. TAB: QUẢN LÝ TÀI SẢN -->
            <div x-show="activeTab === 'assets'" x-transition class="space-y-6">
                <!-- Toolbar -->
                <div class="flex justify-between items-center bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
                    <div class="flex items-center gap-4">
                        <span class="text-xs font-bold text-slate-450 uppercase">Lọc theo phòng:</span>
                        <select x-model="assetRoomFilter" class="bg-slate-100 border-none rounded-lg text-xs font-bold py-1 px-3 text-slate-700 focus:ring-2 focus:ring-indigo-500">
                            <option value="all">Tất cả phòng</option>
                            <template x-for="room in rooms" :key="room.maPhong">
                                <option :value="room.maPhong" x-text="room.tenPhong"></option>
                            </template>
                        </select>
                    </div>
                    <button @click="openAddAsset()" class="flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-xl text-xs font-bold shadow-md shadow-indigo-600/10 transition">
                        <i data-lucide="plus" class="w-4 h-4"></i> Đăng ký tài sản
                    </button>
                </div>

                <!-- Table -->
                <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="border-b border-slate-100 bg-slate-50 text-slate-400 text-xs font-bold uppercase">
                                    <th class="py-3 px-6">Mã Tài Sản</th>
                                    <th class="py-3 px-6">Tên Tài Sản</th>
                                    <th class="py-3 px-6">Số Lượng</th>
                                    <th class="py-3 px-6">Tình Trạng</th>
                                    <th class="py-3 px-6">Vị Trí (Phòng)</th>
                                    <th class="py-3 px-6 text-center">Thao Tác</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-sm text-slate-600">
                                <template x-for="asset in filteredAssets()" :key="asset.maTaiSan">
                                    <tr class="hover:bg-slate-50/50 transition">
                                        <td class="py-4 px-6 font-bold text-slate-800" x-text="'TS' + asset.maTaiSan"></td>
                                        <td class="py-4 px-6 font-semibold text-slate-800" x-text="asset.tenTaiSan"></td>
                                        <td class="py-4 px-6 font-medium text-slate-700" x-text="asset.soLuong"></td>
                                        <td class="py-4 px-6">
                                            <span :class="asset.tinhTrang === 'Tốt' ? 'bg-emerald-50 text-emerald-600' : 'bg-amber-50 text-amber-600'" 
                                                  class="px-2.5 py-0.5 rounded text-xs font-bold" 
                                                  x-text="asset.tinhTrang"></span>
                                        </td>
                                        <td class="py-4 px-6">
                                            <span x-show="asset.maPhong" class="px-2 py-0.5 rounded bg-indigo-50 text-indigo-600 font-bold" x-text="asset.maPhong"></span>
                                            <span x-show="!asset.maPhong" class="text-slate-400 italic">Chưa phân bổ</span>
                                        </td>
                                        <td class="py-4 px-6">
                                            <div class="flex justify-center items-center gap-2">
                                                <button @click="openEditAsset(asset)" class="p-1.5 hover:bg-slate-100 text-indigo-600 rounded-lg transition" title="Sửa tài sản">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                                </button>
                                                <button @click="deleteAsset(asset.maTaiSan)" class="p-1.5 hover:bg-rose-50 text-rose-600 rounded-lg transition" title="Xóa tài sản">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/><line x1="10" x2="10" y1="11" y2="17"/><line x1="14" x2="14" y1="11" y2="17"/></svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- NFR-02: Bảng lịch sử đối soát tài sản -->
                <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm mt-6">
                    <div class="bg-slate-50 border-b border-slate-100 p-4 flex items-center justify-between">
                        <div>
                            <h4 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                                <i data-lucide="history" class="w-4 h-4 text-indigo-600"></i>
                                Nhật Ký Đối Soát Dữ Liệu Tài Sản (NFR-02 - Tính toàn vẹn)
                            </h4>
                            <p class="text-[11px] text-slate-400">Tự động ghi nhận mọi thay đổi, không xóa vĩnh viễn dữ liệu gốc (Xóa mềm)</p>
                        </div>
                        <button @click="fetchAssetLogs()" class="p-1.5 hover:bg-slate-200 text-slate-600 rounded transition" title="Làm mới">
                            <i data-lucide="refresh-cw" class="w-4 h-4"></i>
                        </button>
                    </div>
                    <div class="overflow-x-auto max-h-[300px] overflow-y-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="border-b border-slate-100 bg-slate-50/50 text-slate-450 text-[10px] font-bold uppercase">
                                    <th class="py-2.5 px-6 w-32">Thời Gian</th>
                                    <th class="py-2.5 px-6 w-24">Mã Tài Sản</th>
                                    <th class="py-2.5 px-6 w-40">Tên Tài Sản</th>
                                    <th class="py-2.5 px-6 w-32">Hành Động</th>
                                    <th class="py-2.5 px-6">Chi Tiết Thay Đổi</th>
                                    <th class="py-2.5 px-6 w-36">Người Thực Hiện</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-xs text-slate-600">
                                <template x-for="log in assetLogs" :key="log.id">
                                    <tr class="hover:bg-slate-50/30 transition">
                                        <td class="py-3 px-6 text-slate-400 font-medium" x-text="formatDate(log.created_at) + ' ' + new Date(log.created_at).toLocaleTimeString('vi-VN', {hour: '2-digit', minute:'2-digit'})"></td>
                                        <td class="py-3 px-6 font-bold text-slate-700" x-text="'TS' + log.maTaiSan"></td>
                                        <td class="py-3 px-6 font-semibold text-slate-800" x-text="log.tenTaiSan"></td>
                                        <td class="py-3 px-6">
                                            <span :class="{
                                                'bg-emerald-50 text-emerald-600 border border-emerald-100': log.hanhDong === 'THÊM MỚI',
                                                'bg-amber-50 text-amber-600 border border-amber-100': log.hanhDong === 'CẬP NHẬT',
                                                'bg-rose-50 text-rose-600 border border-rose-100': log.hanhDong === 'THANH LÝ'
                                            }" class="px-2 py-0.5 rounded text-[10px] font-extrabold uppercase" x-text="log.hanhDong"></span>
                                        </td>
                                        <td class="py-3 px-6 leading-relaxed">
                                            <div x-show="log.trangThaiCu" class="text-slate-400 font-medium">
                                                <span class="text-slate-400 font-bold">Từ:</span> <span x-text="log.trangThaiCu"></span>
                                            </div>
                                            <div x-show="log.trangThaiMoi" class="text-indigo-600 font-bold">
                                                <span class="text-slate-500 font-bold">Đến:</span> <span x-text="log.trangThaiMoi"></span>
                                            </div>
                                        </td>
                                        <td class="py-3 px-6 font-medium text-slate-700 flex items-center gap-1.5">
                                            <span class="w-1.5 h-1.5 rounded-full animate-pulse" :class="log.nguoiThucHien === 'Quản trị viên' ? 'bg-indigo-500' : 'bg-amber-500'"></span>
                                            <span x-text="log.nguoiThucHien"></span>
                                        </td>
                                    </tr>
                                </template>
                                <template x-if="assetLogs.length === 0">
                                    <tr>
                                        <td colspan="6" class="py-6 text-center text-slate-400 italic">Chưa có hoạt động nào được ghi nhận.</td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </main>

    <!-- ==================== MODALS SYSTEM ==================== -->
    
    <!-- 1. MODAL: PHÒNG (ROOM) -->
    <div x-show="showRoomModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4" x-transition>
        <div @click.away="showRoomModal = false" 
             :class="isEditingRoom ? 'max-w-lg' : 'max-w-md'"
             class="bg-white rounded-2xl w-full overflow-hidden shadow-2xl border border-slate-100 flex flex-col transition-all duration-300">
            <div class="h-14 bg-slate-900 px-6 flex justify-between items-center text-white">
                <h4 class="font-bold text-sm uppercase tracking-wide" x-text="isEditingRoom ? 'Cập nhật phòng trọ' : 'Thêm phòng trọ mới'"></h4 >
                <button @click="showRoomModal = false" class="text-slate-400 hover:text-white transition"><i data-lucide="x" class="w-5 h-5"></i></button>
            </div>
            <form @submit.prevent="saveRoom()" class="p-6 space-y-4">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Mã Phòng</label>
                    <input type="text" x-model="roomForm.maPhong" :disabled="isEditingRoom" placeholder="Ví dụ: P105" required
                           class="w-full px-3 py-2 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 disabled:bg-slate-100">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Tên Phòng</label>
                    <input type="text" x-model="roomForm.tenPhong" placeholder="Ví dụ: Phòng 105" required
                           class="w-full px-3 py-2 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Tầng</label>
                        <input type="number" x-model.number="roomForm.tang" placeholder="1" required min="1"
                               class="w-full px-3 py-2 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Đơn Giá Thuê</label>
                        <input type="number" x-model.number="roomForm.giaPhong" placeholder="3000000" required min="0"
                               class="w-full px-3 py-2 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Trạng Thái</label>
                    <select x-model="roomForm.trangThai" class="w-full px-3 py-2 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                        <option value="Trống">Trống</option>
                        <option value="Đã thuê">Đã thuê</option>
                        <option value="Đang bảo trì">Đang bảo trì</option>
                    </select>
                </div>

                <!-- Cascade / inline asset list when editing (NFR-02 & User request) -->
                <template x-if="isEditingRoom">
                    <div class="border-t border-slate-100 pt-4 space-y-2">
                        <div class="flex justify-between items-center">
                            <label class="block text-xs font-bold text-slate-700 uppercase">Danh Sách Tài Sản Trong Phòng</label>
                            <span class="text-[10px] bg-indigo-50 text-indigo-600 px-2 py-0.5 rounded-full font-bold" x-text="assets.filter(a => a.maPhong === roomForm.maPhong).length + ' tài sản'"></span>
                        </div>
                        <div class="bg-slate-50 border border-slate-100 rounded-xl overflow-hidden max-h-[180px] overflow-y-auto">
                            <table class="w-full text-left border-collapse text-xs">
                                <thead>
                                    <tr class="bg-slate-100/80 border-b border-slate-200 text-slate-500 font-bold uppercase text-[9px]">
                                        <th class="py-2 px-3">Tên Tài Sản</th>
                                        <th class="py-2 px-3 w-16 text-center">SL</th>
                                        <th class="py-2 px-3 w-24">Tình Trạng</th>
                                        <th class="py-2 px-3 text-center w-12">Xóa</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 text-slate-600">
                                    <template x-for="asset in assets.filter(a => a.maPhong === roomForm.maPhong)" :key="asset.maTaiSan">
                                        <tr class="hover:bg-slate-100/30 bg-white transition">
                                            <td class="py-2 px-3 font-semibold text-slate-800" x-text="asset.tenTaiSan"></td>
                                            <td class="py-2 px-3 text-center">
                                                <input type="number" x-model.number="asset.soLuong" @change="axios.put('/api/assets/' + asset.maTaiSan, asset).then(() => { fetchAssets(); fetchAssetLogs(); })" class="w-12 border border-slate-200 rounded px-1 py-0.5 text-center focus:outline-none focus:ring-1 focus:ring-indigo-500 font-bold">
                                            </td>
                                            <td class="py-2 px-3">
                                                <select x-model="asset.tinhTrang" @change="axios.put('/api/assets/' + asset.maTaiSan, asset).then(() => { fetchAssets(); fetchAssetLogs(); })" class="w-full bg-slate-50 border border-slate-200 rounded px-1 py-0.5 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                                                    <option value="Tốt">Tốt</option>
                                                    <option value="Cũ">Cũ</option>
                                                    <option value="Hỏng">Hỏng</option>
                                                </select>
                                            </td>
                                            <td class="py-2 px-3 text-center">
                                                <button type="button" @click="if(confirm('Bạn có chắc muốn xóa tài sản này?')) { axios.delete('/api/assets/' + asset.maTaiSan).then(() => { fetchAssets(); fetchAssetLogs(); }) }" class="text-rose-600 hover:text-rose-800 transition">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mx-auto"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/><line x1="10" x2="10" y1="11" y2="17"/><line x1="14" x2="14" y1="11" y2="17"/></svg>
                                                </button>
                                            </td>
                                        </tr>
                                    </template>
                                    <template x-if="assets.filter(a => a.maPhong === roomForm.maPhong).length === 0">
                                        <tr>
                                            <td colspan="4" class="py-4 text-center text-slate-400 italic">Không có tài sản nào trong phòng này.</td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </template>

                <div class="pt-4 border-t border-slate-100 flex justify-end gap-2">
                    <button type="button" @click="showRoomModal = false" class="px-4 py-2 border border-slate-200 rounded-xl text-xs font-bold text-slate-500 hover:bg-slate-50 transition">Hủy bỏ</button>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold transition">Lưu lại</button>
                </div>
            </form>
        </div>
    </div>

    <!-- 2. MODAL: KHÁCH THUÊ (TENANT) -->
    <div x-show="showTenantModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4" x-transition>
        <div @click.away="showTenantModal = false" class="bg-white rounded-2xl w-full max-w-md overflow-hidden shadow-2xl border border-slate-100 flex flex-col">
            <div class="h-14 bg-slate-900 px-6 flex justify-between items-center text-white">
                <h4 class="font-bold text-sm uppercase tracking-wide" x-text="isEditingTenant ? 'Sửa thông tin khách thuê' : 'Đăng ký khách thuê mới'"></h4>
                <button @click="showTenantModal = false" class="text-slate-400 hover:text-white transition"><i data-lucide="x" class="w-5 h-5"></i></button>
            </div>
            <form @submit.prevent="saveTenant()" class="p-6 space-y-4">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Họ và Tên</label>
                    <input type="text" x-model="tenantForm.hoTen" placeholder="Nguyễn Văn A" required
                           class="w-full px-3 py-2 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Số CCCD</label>
                        <input type="text" x-model="tenantForm.cccd" placeholder="012345678901" required
                               class="w-full px-3 py-2 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Số Điện Thoại</label>
                        <input type="text" x-model="tenantForm.sdt" placeholder="0987654321" required
                               class="w-full px-3 py-2 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Giới Tính</label>
                        <select x-model="tenantForm.gioiTinh" class="w-full px-3 py-2 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                            <option value="Nam">Nam</option>
                            <option value="Nữ">Nữ</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Ngày Sinh</label>
                        <input type="date" x-model="tenantForm.ngaySinh" required
                               class="w-full px-3 py-2 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Email</label>
                    <input type="email" x-model="tenantForm.email" placeholder="nguyenvana@gmail.com" required
                           class="w-full px-3 py-2 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Quê Quán</label>
                    <input type="text" x-model="tenantForm.queQuan" placeholder="Hà Nội" required
                           class="w-full px-3 py-2 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                </div>
                <div class="pt-4 border-t border-slate-100 flex justify-end gap-2">
                    <button type="button" @click="showTenantModal = false" class="px-4 py-2 border border-slate-200 rounded-xl text-xs font-bold text-slate-500 hover:bg-slate-50 transition">Hủy</button>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold transition">Lưu lại</button>
                </div>
            </form>
        </div>
    </div>

    <!-- 3. MODAL: HỢP ĐỒNG (CONTRACT) -->
    <div x-show="showContractModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4" x-transition>
        <div @click.away="showContractModal = false" class="bg-white rounded-2xl w-full max-w-md overflow-hidden shadow-2xl border border-slate-100 flex flex-col">
            <div class="h-14 bg-slate-900 px-6 flex justify-between items-center text-white">
                <h4 class="font-bold text-sm uppercase tracking-wide">Lập Hợp Đồng Mới</h4>
                <button @click="showContractModal = false" class="text-slate-400 hover:text-white transition"><i data-lucide="x" class="w-5 h-5"></i></button>
            </div>
            <form @submit.prevent="saveContract()" class="p-6 space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Chọn Phòng</label>
                        <select x-model="contractForm.maPhong" required class="w-full px-3 py-2 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                            <option value="">Chọn phòng trống</option>
                            <template x-for="room in rooms" :key="room.maPhong">
                                <option :value="room.maPhong" x-text="room.tenPhong + ' (Tầng ' + room.tang + ')'" :disabled="room.trangThai !== 'Trống'"></option>
                            </template>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Khách Thuê</label>
                        <select x-model.number="contractForm.maKhach" required class="w-full px-3 py-2 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                            <option value="">Chọn khách hàng</option>
                            <template x-for="tenant in tenants" :key="tenant.maKhach">
                                <option :value="tenant.maKhach" x-text="tenant.hoTen"></option>
                            </template>
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Tiền Đặt Cọc</label>
                        <input type="number" x-model.number="contractForm.tienCoc" placeholder="3000000" required min="0"
                               class="w-full px-3 py-2 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Đơn Giá Thuê</label>
                        <input type="number" x-model.number="contractForm.giaThueThang" placeholder="3000000" required min="0"
                               class="w-full px-3 py-2 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Ngày Bắt Đầu</label>
                        <input type="date" x-model="contractForm.ngayBatDau" required
                               class="w-full px-3 py-2 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Ngày Kết Thúc</label>
                        <input type="date" x-model="contractForm.ngayKetThuc" required
                               class="w-full px-3 py-2 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                    </div>
                </div>
                <div class="pt-4 border-t border-slate-100 flex justify-end gap-2">
                    <button type="button" @click="showContractModal = false" class="px-4 py-2 border border-slate-200 rounded-xl text-xs font-bold text-slate-500 hover:bg-slate-50 transition">Hủy</button>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold transition">Tạo hợp đồng</button>
                </div>
            </form>
        </div>
    </div>

    <!-- 4. MODAL: HÓA ĐƠN & TÍNH TIỀN (INVOICE & STRATEGY) -->
    <div x-show="showInvoiceModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4" x-transition>
        <div @click.away="showInvoiceModal = false" class="bg-white rounded-2xl w-full max-w-md overflow-hidden shadow-2xl border border-slate-100 flex flex-col">
            <div class="h-14 bg-slate-900 px-6 flex justify-between items-center text-white">
                <h4 class="font-bold text-sm uppercase tracking-wide">Tính Hóa Đơn & Áp Dụng Thuật Toán</h4>
                <button @click="showInvoiceModal = false" class="text-slate-400 hover:text-white transition"><i data-lucide="x" class="w-5 h-5"></i></button>
            </div>
            <form @submit.prevent="saveInvoice()" class="p-6 space-y-4">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Hợp Đồng Hiện Lực</label>
                    <select x-model.number="invoiceForm.maHopDong" required @change="updateInvoiceBaseRent()" class="w-full px-3 py-2 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                        <option value="">Chọn hợp đồng</option>
                        <template x-for="contract in contracts.filter(c => c.trangThai === 'Đang hiệu lực')" :key="contract.maHopDong">
                            <option :value="contract.maHopDong" x-text="'HĐ' + contract.maHopDong + ' - Phòng ' + contract.maPhong + ' (' + getTenantName(contract.maKhach) + ')'"></option>
                        </template>
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Tháng Kỳ HĐ</label>
                        <input type="number" x-model.number="invoiceForm.thang" required min="1" max="12"
                               class="w-full px-3 py-2 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Năm Kỳ HĐ</label>
                        <input type="number" x-model.number="invoiceForm.nam" required min="2020"
                               class="w-full px-3 py-2 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 border-t border-slate-100 pt-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Điện mới - cũ (kWh)</label>
                        <div class="flex gap-1 items-center">
                            <input type="number" x-model.number="invoiceForm.dienMoi" placeholder="Mới" required @input="calculateTotal()" class="w-1/2 px-2 py-1.5 border border-slate-200 rounded-lg text-xs focus:ring-2 focus:ring-indigo-500/20">
                            <input type="number" x-model.number="invoiceForm.dienCu" placeholder="Cũ" required @input="calculateTotal()" class="w-1/2 px-2 py-1.5 border border-slate-200 rounded-lg text-xs focus:ring-2 focus:ring-indigo-500/20">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Nước mới - cũ (m³)</label>
                        <div class="flex gap-1 items-center">
                            <input type="number" x-model.number="invoiceForm.nuocMoi" placeholder="Mới" required @input="calculateTotal()" class="w-1/2 px-2 py-1.5 border border-slate-200 rounded-lg text-xs focus:ring-2 focus:ring-indigo-500/20">
                            <input type="number" x-model.number="invoiceForm.nuocCu" placeholder="Cũ" required @input="calculateTotal()" class="w-1/2 px-2 py-1.5 border border-slate-200 rounded-lg text-xs focus:ring-2 focus:ring-indigo-500/20">
                        </div>
                    </div>
                </div>

                <!-- Pattern Strategy Section -->
                <div class="bg-indigo-50/50 p-4 rounded-xl border border-indigo-100/50 space-y-3">
                    <div class="flex justify-between items-center">
                        <label class="block text-xs font-extrabold text-indigo-900 uppercase">Chiến lược Tính Giá (Strategy)</label>
                        <span class="text-[10px] bg-indigo-600 text-white font-bold px-1.5 py-0.5 rounded uppercase">Design Pattern</span>
                    </div>
                    <select x-model="invoiceForm.strategy" @change="calculateTotal()" class="w-full px-3 py-2 bg-white border border-indigo-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                        <option value="default">Default Strategy (Giá gốc + Điện nước)</option>
                        <option value="lateFee">Late Fee Strategy (Phạt đóng trễ hạn +100K)</option>
                    </select>

                    <div class="flex justify-between items-center text-xs font-medium text-indigo-950 pt-2">
                        <span>Tổng tiền dự tính:</span>
                        <strong class="text-indigo-600 text-base" x-text="formatCurrency(invoiceForm.tongTien)"></strong>
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-100 flex justify-end gap-2">
                    <button type="button" @click="showInvoiceModal = false" class="px-4 py-2 border border-slate-200 rounded-xl text-xs font-bold text-slate-500 hover:bg-slate-50 transition">Hủy bỏ</button>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold transition">Tạo hóa đơn</button>
                </div>
            </form>
        </div>
    </div>

    <!-- 5. MODAL: TÀI SẢN (ASSET) -->
    <div x-show="showAssetModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4" x-transition>
        <div @click.away="showAssetModal = false" class="bg-white rounded-2xl w-full max-w-md overflow-hidden shadow-2xl border border-slate-100 flex flex-col">
            <div class="h-14 bg-slate-900 px-6 flex justify-between items-center text-white">
                <h4 class="font-bold text-sm uppercase tracking-wide" x-text="isEditingAsset ? 'Cập nhật tài sản' : 'Đăng ký tài sản mới'"></h4>
                <button @click="showAssetModal = false" class="text-slate-400 hover:text-white transition"><i data-lucide="x" class="w-5 h-5"></i></button>
            </div>
            <form @submit.prevent="saveAsset()" class="p-6 space-y-4">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Tên Tài Sản</label>
                    <input type="text" x-model="assetForm.tenTaiSan" placeholder="Ví dụ: Tủ lạnh Electrolux" required
                           class="w-full px-3 py-2 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Số Lượng</label>
                        <input type="number" x-model.number="assetForm.soLuong" placeholder="1" required min="1"
                               class="w-full px-3 py-2 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Tình Trạng</label>
                        <select x-model="assetForm.tinhTrang" class="w-full px-3 py-2 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                            <option value="Tốt">Tốt</option>
                            <option value="Cũ">Cũ</option>
                            <option value="Hỏng">Hỏng</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Vị Trí (Phòng)</label>
                    <select x-model="assetForm.maPhong" class="w-full px-3 py-2 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500">
                        <option value="">Chưa phân bổ (Để trống)</option>
                        <template x-for="room in rooms" :key="room.maPhong">
                            <option :value="room.maPhong" x-text="room.tenPhong"></option>
                        </template>
                    </select>
                </div>
                <div class="pt-4 border-t border-slate-100 flex justify-end gap-2">
                    <button type="button" @click="showAssetModal = false" class="px-4 py-2 border border-slate-200 rounded-xl text-xs font-bold text-slate-500 hover:bg-slate-50 transition">Hủy</button>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold transition">Lưu lại</button>
                </div>
            </form>
        </div>
    </div>

</div>

<!-- ==================== RENTAL APP ENGINE (ALPINE JS) ==================== -->
<script>
function rentalApp() {
    return {
        // Role system & logs (NFR-03 & NFR-02)
        userRole: 'admin',
        assetLogs: [],

        // Tab system
        activeTab: 'dashboard',
        tabTitles: {
            dashboard: 'Bảng Điều Khiển Tổng Quan',
            rooms: 'Quản Lý Phòng Trọ',
            tenants: 'Danh Sách Khách Thuê',
            contracts: 'Quản Lý Hợp Đồng',
            invoices: 'Danh Sách Hóa Đơn',
            assets: 'Quản Lý Tài Sản'
        },

        // Search and Filters
        searchQuery: '',
        roomFilter: 'all',
        contractFilter: 'all',
        invoiceFilter: 'all',
        assetRoomFilter: 'all',

        // Selected State
        selectedRoom: null,

        // Database Collections (Simulated State)
        rooms: [],
        tenants: [],
        contracts: [],
        invoices: [],
        sendingEmails: [],
        assets: [],

        // Modal Forms State
        showRoomModal: false,
        isEditingRoom: false,
        roomForm: { maPhong: '', tenPhong: '', tang: 1, giaPhong: 2500000, trangThai: 'Trống' },

        showTenantModal: false,
        isEditingTenant: false,
        tenantForm: { maKhach: null, hoTen: '', cccd: '', sdt: '', email: '', gioiTinh: 'Nam', ngaySinh: '', queQuan: '' },

        showContractModal: false,
        contractForm: { maHopDong: null, maPhong: '', maKhach: '', ngayLap: '', ngayBatDau: '', ngayKetThuc: '', tienCoc: 0, giaThueThang: 0 },

        showInvoiceModal: false,
        invoiceForm: { maHD: null, maHopDong: '', thang: '', nam: '', ngayLap: '', dienCu: 0, dienMoi: 0, nuocCu: 0, nuocMoi: 0, strategy: 'default', baseRent: 0, tongTien: 0 },

        showAssetModal: false,
        isEditingAsset: false,
        assetForm: { maTaiSan: null, tenTaiSan: '', soLuong: 1, tinhTrang: 'Tốt', maPhong: '' },

        // Initialization
        initApp() {
            // Configure Axios CSRF header automatically
            const tokenMeta = document.querySelector('meta[name="csrf-token"]');
            if (tokenMeta) {
                axios.defaults.headers.common['X-CSRF-TOKEN'] = tokenMeta.getAttribute('content');
            }

            // Configure Axios X-User-Role header automatically (NFR-03)
            axios.defaults.headers.common['X-User-Role'] = this.userRole;

            // Load all database entities from backend
            this.fetchRooms();
            this.fetchTenants();
            this.fetchContracts();
            this.fetchInvoices();
            this.fetchAssets();
            this.fetchAssetLogs();

            // Render Lucide icons when window finishes initializing
            this.$nextTick(() => {
                lucide.createIcons();
            });
        },

        // --- BACKEND API SYNCHRONIZERS ---
        fetchRooms() {
            axios.get('/api/rooms')
                .then(res => { 
                    this.rooms = res.data; 
                    // Refresh selected room if it was open
                    if (this.selectedRoom) {
                        const updated = this.rooms.find(r => r.maPhong === this.selectedRoom.maPhong);
                        if (updated) this.selectedRoom = updated;
                    }
                })
                .catch(err => console.error('Lỗi tải danh sách phòng:', err));
        },
        fetchTenants() {
            axios.get('/api/tenants')
                .then(res => { this.tenants = res.data; })
                .catch(err => console.error('Lỗi tải danh sách khách thuê:', err));
        },
        fetchContracts() {
            axios.get('/api/contracts')
                .then(res => { this.contracts = res.data; })
                .catch(err => console.error('Lỗi tải danh sách hợp đồng:', err));
        },
        fetchInvoices() {
            axios.get('/api/invoices')
                .then(res => { this.invoices = res.data; })
                .catch(err => console.error('Lỗi tải danh sách hóa đơn:', err));
        },
        fetchAssets() {
            axios.get('/api/assets')
                .then(res => { this.assets = res.data; })
                .catch(err => console.error('Lỗi tải danh sách tài sản:', err));
        },
        fetchAssetLogs() {
            axios.get('/api/assets/logs')
                .then(res => { this.assetLogs = res.data; })
                .catch(err => console.error('Lỗi tải nhật ký đối soát:', err));
        },
        changeRole() {
            axios.defaults.headers.common['X-User-Role'] = this.userRole;
        },
        checkAdminPermission() {
            if (this.userRole !== 'admin') {
                alert('Yêu cầu bảo mật (NFR-03): Chỉ Quản trị viên mới được phép thực hiện các chức năng Thêm, Sửa, Xóa.');
                return false;
            }
            return true;
        },

        // Helper: Formatting Currency
        formatCurrency(value) {
            return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(value);
        },

        // Helper: Formatting Date
        formatDate(dateStr) {
            if (!dateStr) return '';
            const d = new Date(dateStr);
            return `${String(d.getDate()).padStart(2, '0')}/${String(d.getMonth() + 1).padStart(2, '0')}/${d.getFullYear()}`;
        },

        // Helper: Dynamic badge classes
        getRoomStatusClass(status) {
            if (status === 'Trống') return 'bg-emerald-50 text-emerald-600';
            if (status === 'Đã thuê') return 'bg-indigo-50 text-indigo-600';
            return 'bg-amber-50 text-amber-600';
        },

        // Statistics computations
        rentedRoomsCount() { return this.rooms.filter(r => r.trangThai === 'Đã thuê').length; },
        vacantRoomsCount() { return this.rooms.filter(r => r.trangThai === 'Trống').length; },
        maintenanceRoomsCount() { return this.rooms.filter(r => r.trangThai === 'Đang bảo trì').length; },
        activeContractsCount() { return this.contracts.filter(c => c.trangThai === 'Đang hiệu lực').length; },
        unpaidInvoicesCount() { return this.invoices.filter(i => i.trangThai === 'Chưa thanh toán').length; },
        unpaidInvoicesSum() { return this.invoices.filter(i => i.trangThai === 'Chưa thanh toán').reduce((sum, item) => sum + item.tongTien, 0); },
        totalRevenue() { return this.invoices.filter(i => i.trangThai === 'Đã thanh toán').reduce((sum, item) => sum + item.tongTien, 0); },

        // Filter methods
        filteredRooms() {
            let list = this.rooms;
            if (this.roomFilter === 'available') list = list.filter(r => r.trangThai === 'Trống');
            if (this.roomFilter === 'rented') list = list.filter(r => r.trangThai === 'Đã thuê');
            if (this.roomFilter === 'maintenance') list = list.filter(r => r.trangThai === 'Đang bảo trì');

            if (this.searchQuery.trim() !== '') {
                const q = this.searchQuery.toLowerCase();
                list = list.filter(r => r.maPhong.toLowerCase().includes(q) || r.tenPhong.toLowerCase().includes(q));
            }
            return list;
        },

        filteredTenants() {
            let list = this.tenants;
            if (this.searchQuery.trim() !== '') {
                const q = this.searchQuery.toLowerCase();
                list = list.filter(t => t.hoTen.toLowerCase().includes(q) || t.cccd.includes(q) || t.sdt.includes(q));
            }
            return list;
        },

        filteredContracts() {
            let list = this.contracts;
            if (this.contractFilter === 'active') list = list.filter(c => c.trangThai === 'Đang hiệu lực');
            if (this.contractFilter === 'terminated') list = list.filter(c => c.trangThai === 'Đã thanh lý');

            if (this.searchQuery.trim() !== '') {
                const q = this.searchQuery.toLowerCase();
                list = list.filter(c => c.maPhong.toLowerCase().includes(q) || this.getTenantName(c.maKhach).toLowerCase().includes(q));
            }
            return list;
        },

        filteredInvoices() {
            let list = this.invoices;
            if (this.invoiceFilter === 'unpaid') list = list.filter(i => i.trangThai === 'Chưa thanh toán');
            if (this.invoiceFilter === 'paid') list = list.filter(i => i.trangThai === 'Đã thanh toán');

            if (this.searchQuery.trim() !== '') {
                const q = this.searchQuery.toLowerCase();
                list = list.filter(i => {
                    const contract = this.contracts.find(c => c.maHopDong === i.maHopDong);
                    return contract && contract.maPhong.toLowerCase().includes(q);
                });
            }
            return list;
        },

        filteredAssets() {
            let list = this.assets;
            if (this.assetRoomFilter !== 'all') {
                list = list.filter(a => a.maPhong === this.assetRoomFilter);
            }

            if (this.searchQuery.trim() !== '') {
                const q = this.searchQuery.toLowerCase();
                list = list.filter(a => a.tenTaiSan.toLowerCase().includes(q) || (a.maPhong && a.maPhong.toLowerCase().includes(q)));
            }
            return list;
        },

        // Helper relations resolver
        getRoomContract(maPhong) {
            return this.contracts.find(c => c.maPhong === maPhong && c.trangThai === 'Đang hiệu lực');
        },

        getTenantName(maKhach) {
            const tenant = this.tenants.find(t => t.maKhach == maKhach);
            return tenant ? tenant.hoTen : 'Không rõ';
        },

        getContractInvoices(maHopDong) {
            return this.invoices.filter(i => i.maHopDong == maHopDong);
        },

        getRoomIdFromContract(maHopDong) {
            const contract = this.contracts.find(c => c.maHopDong == maHopDong);
            return contract ? contract.maPhong : 'Không rõ';
        },

        // --- ROOMS CRUD ---
        openAddRoom() {
            this.isEditingRoom = false;
            this.roomForm = { maPhong: '', tenPhong: '', tang: 1, giaPhong: 2500000, trangThai: 'Trống' };
            this.showRoomModal = true;
            this.$nextTick(() => lucide.createIcons());
        },

        openEditRoom(room) {
            this.isEditingRoom = true;
            this.roomForm = { ...room };
            this.showRoomModal = true;
            this.$nextTick(() => lucide.createIcons());
        },

        saveRoom() {
            if (!this.checkAdminPermission()) return;
            if (this.isEditingRoom) {
                axios.put('/api/rooms/' + this.roomForm.maPhong, this.roomForm)
                    .then(res => {
                        this.fetchRooms();
                        this.showRoomModal = false;
                    })
                    .catch(err => alert('Lỗi cập nhật phòng: ' + (err.response?.data?.message || err.message)));
            } else {
                axios.post('/api/rooms', this.roomForm)
                    .then(res => {
                        this.fetchRooms();
                        this.showRoomModal = false;
                    })
                    .catch(err => alert('Lỗi tạo phòng: ' + (err.response?.data?.message || err.message)));
            }
        },

        deleteRoom(maPhong) {
            if (!this.checkAdminPermission()) return;
            if (confirm(`Bạn có chắc muốn xóa phòng ${maPhong}?`)) {
                axios.delete('/api/rooms/' + maPhong)
                    .then(() => {
                        this.fetchRooms();
                        this.selectedRoom = null;
                    })
                    .catch(err => alert('Lỗi xóa phòng: ' + (err.response?.data?.message || err.message)));
            }
        },

        // --- TENANTS CRUD ---
        openAddTenant() {
            this.isEditingTenant = false;
            this.tenantForm = { maKhach: null, hoTen: '', cccd: '', sdt: '', email: '', gioiTinh: 'Nam', ngaySinh: '', queQuan: '' };
            this.showTenantModal = true;
            this.$nextTick(() => lucide.createIcons());
        },

        openEditTenant(tenant) {
            this.isEditingTenant = true;
            this.tenantForm = { ...tenant };
            this.showTenantModal = true;
            this.$nextTick(() => lucide.createIcons());
        },

        saveTenant() {
            if (!this.checkAdminPermission()) return;
            if (this.isEditingTenant) {
                axios.put('/api/tenants/' + this.tenantForm.maKhach, this.tenantForm)
                    .then(res => {
                        this.fetchTenants();
                        this.showTenantModal = false;
                    })
                    .catch(err => alert('Lỗi cập nhật khách thuê: ' + (err.response?.data?.message || err.message)));
            } else {
                axios.post('/api/tenants', this.tenantForm)
                    .then(res => {
                        this.fetchTenants();
                        this.showTenantModal = false;
                    })
                    .catch(err => alert('Lỗi thêm khách thuê: ' + (err.response?.data?.message || err.message)));
            }
        },

        deleteTenant(maKhach) {
            if (!this.checkAdminPermission()) return;
            if (confirm('Bạn có chắc muốn xóa thông tin khách thuê này?')) {
                axios.delete('/api/tenants/' + maKhach)
                    .then(() => {
                        this.fetchTenants();
                    })
                    .catch(err => alert('Lỗi xóa khách thuê: ' + (err.response?.data?.message || err.message)));
            }
        },

        // --- CONTRACTS CRUD ---
        openAddContract() {
            this.contractForm = {
                maHopDong: null,
                maPhong: '',
                maKhach: '',
                ngayLap: new Date().toISOString().split('T')[0],
                ngayBatDau: new Date().toISOString().split('T')[0],
                ngayKetThuc: '',
                tienCoc: 0,
                giaThueThang: 0
            };
            this.showContractModal = true;
            this.$nextTick(() => lucide.createIcons());
        },

        openAddContractForRoom(roomId, roomPrice) {
            this.openAddContract();
            this.contractForm.maPhong = roomId;
            this.contractForm.giaThueThang = roomPrice;
            this.contractForm.tienCoc = roomPrice; // Mặc định đặt cọc bằng 1 tháng tiền phòng
        },

        saveContract() {
            if (!this.checkAdminPermission()) return;
            axios.post('/api/contracts', this.contractForm)
                .then(res => {
                    this.fetchContracts();
                    this.fetchRooms();
                    this.showContractModal = false;
                })
                .catch(err => alert('Lỗi lập hợp đồng: ' + (err.response?.data?.message || err.message)));
        },

        terminateContract(maHopDong) {
            if (!this.checkAdminPermission()) return;
            if (confirm('Bạn có chắc chắn muốn thanh lý hợp đồng này? Trạng thái phòng sẽ được cập nhật về trống.')) {
                axios.put('/api/contracts/' + maHopDong + '/terminate')
                    .then(res => {
                        this.fetchContracts();
                        this.fetchRooms();
                    })
                    .catch(err => alert('Lỗi thanh lý hợp đồng: ' + (err.response?.data?.message || err.message)));
            }
        },

        deleteContract(maHopDong) {
            if (!this.checkAdminPermission()) return;
            if (confirm('Xóa hợp đồng này khỏi cơ sở dữ liệu?')) {
                axios.delete('/api/contracts/' + maHopDong)
                    .then(() => {
                        this.fetchContracts();
                        this.fetchRooms();
                    })
                    .catch(err => alert('Lỗi xóa hợp đồng: ' + (err.response?.data?.message || err.message)));
            }
        },

        sendEmailReminder(contract, event) {
            const tenant = this.tenants.find(t => t.maKhach === contract.maKhach);
            if (!tenant) {
                alert('Không tìm thấy thông tin khách thuê!');
                return;
            }
            const targetEmail = tenant.email || 'nguyenvana@gmail.com';
            
            // Show loading status on clicked button
            const btn = event.currentTarget;
            const originalText = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<span class="animate-pulse">Đang gửi...</span>';

            axios.post('/api/contracts/' + contract.maHopDong + '/send-reminder', {
                contract: {
                    ...contract,
                    ngayKetThucFormatted: this.formatDate(contract.ngayKetThuc)
                },
                tenant: tenant,
                email: targetEmail
            })
            .then(response => {
                alert('Đã gửi email nhắc nhở hết hạn đến: ' + targetEmail + ' thành công! Hãy mở Mailpit tại http://localhost:8025 để xem email.');
            })
            .catch(error => {
                alert('Gửi mail thất bại: ' + (error.response?.data?.message || error.message));
            })
            .finally(() => {
                btn.disabled = false;
                btn.innerHTML = originalText;
            });
        },

        // --- INVOICES CRUD (STRATEGY PATTERN LOGIC) ---
        openAddInvoice() {
            this.invoiceForm = {
                maHD: null,
                maHopDong: '',
                thang: new Date().getMonth() + 1,
                nam: new Date().getFullYear(),
                ngayLap: new Date().toISOString().split('T')[0],
                dienCu: 0,
                dienMoi: 0,
                nuocCu: 0,
                nuocMoi: 0,
                strategy: 'default',
                baseRent: 0,
                tongTien: 0
            };
            this.showInvoiceModal = true;
            this.$nextTick(() => lucide.createIcons());
        },

        openAddInvoiceForContract(contractId) {
            this.openAddInvoice();
            this.invoiceForm.maHopDong = contractId;
            this.updateInvoiceBaseRent();
        },

        updateInvoiceBaseRent() {
            const contract = this.contracts.find(c => c.maHopDong === this.invoiceForm.maHopDong);
            if (contract) {
                this.invoiceForm.baseRent = contract.giaThueThang;
                this.calculateTotal();
            }
        },

        // Design Pattern: Strategy Implementation for calculation calling backend
        calculateTotal() {
            const form = this.invoiceForm;
            if (!form.maHopDong) return;

            // Simple validation check before sending API to avoid console error logs
            if (form.dienMoi < form.dienCu || form.nuocMoi < form.nuocCu) {
                form.tongTien = form.baseRent;
                return;
            }

            axios.post('/api/invoices/calculate', {
                maHopDong: form.maHopDong,
                dienCu: form.dienCu,
                dienMoi: form.dienMoi,
                nuocCu: form.nuocCu,
                nuocMoi: form.nuocMoi,
                strategy: form.strategy
            })
            .then(res => {
                form.tongTien = res.data.tongTien;
            })
            .catch(err => {
                console.error('Lỗi tính toán hóa đơn bằng Strategy:', err);
            });
        },

        saveInvoice() {
            if (!this.checkAdminPermission()) return;

            const form = this.invoiceForm;

            // 1. Check new readings vs old readings
            if (form.dienMoi < form.dienCu) {
                alert('Lỗi: Số điện mới phải lớn hơn hoặc bằng số điện cũ.');
                return;
            }
            if (form.nuocMoi < form.nuocCu) {
                alert('Lỗi: Số nước mới phải lớn hơn hoặc bằng số nước cũ.');
                return;
            }

            // 2. Check billing period must be <= today's month/year
            const today = new Date();
            const currentYear = today.getFullYear();
            const currentMonth = today.getMonth() + 1;

            if (form.nam > currentYear || (form.nam === currentYear && form.thang > currentMonth)) {
                alert('Lỗi: Kỳ hóa đơn (Tháng/Năm) không được lớn hơn tháng/năm hiện tại.');
                return;
            }

            // 3. Check billing period must be within contract duration
            const contract = this.contracts.find(c => c.maHopDong == form.maHopDong);
            if (contract) {
                const startDate = new Date(contract.ngayBatDau);
                const endDate = new Date(contract.ngayKetThuc);
                
                const startPeriod = startDate.getFullYear() * 12 + (startDate.getMonth() + 1);
                const endPeriod = endDate.getFullYear() * 12 + (endDate.getMonth() + 1);
                const billingPeriod = parseInt(form.nam) * 12 + parseInt(form.thang);
                
                if (billingPeriod < startPeriod || billingPeriod > endPeriod) {
                    alert(`Lỗi: Kỳ hóa đơn (Tháng ${form.thang}/${form.nam}) phải nằm trong thời hạn hiệu lực của hợp đồng (từ ${this.formatDate(contract.ngayBatDau)} đến ${this.formatDate(contract.ngayKetThuc)}).`);
                    return;
                }

                // 4. Check duplicate billing for the same room in the same month/year
                const roomId = contract.maPhong;
                const duplicate = this.invoices.find(i => {
                    const existingContract = this.contracts.find(c => c.maHopDong == i.maHopDong);
                    return existingContract && 
                           existingContract.maPhong === roomId && 
                           i.thang == form.thang && 
                           i.nam == form.nam;
                });
                if (duplicate) {
                    alert(`Lỗi: Phòng ${roomId} đã được lập hóa đơn cho kỳ tháng ${form.thang}/${form.nam} rồi (Mã hóa đơn: HD${duplicate.maHD}). Không thể tạo thêm.`);
                    return;
                }
            }

            axios.post('/api/invoices', this.invoiceForm)
                .then(res => {
                    this.fetchInvoices();
                    this.showInvoiceModal = false;
                })
                .catch(err => alert('Lỗi lập hóa đơn: ' + (err.response?.data?.message || err.message)));
        },

        payInvoice(maHD) {
            if (!this.checkAdminPermission()) return;
            axios.put('/api/invoices/' + maHD + '/pay')
                .then(res => {
                    this.fetchInvoices();
                    alert('Thanh toán hóa đơn thành công! Doanh thu đã được ghi nhận.');
                })
                .catch(err => alert('Lỗi thanh toán hóa đơn: ' + (err.response?.data?.message || err.message)));
        },

        deleteInvoice(maHD) {
            if (!this.checkAdminPermission()) return;
            const invoice = this.invoices.find(i => i.maHD === maHD);
            if (invoice && invoice.trangThai === 'Chưa thanh toán') {
                alert('Lỗi bảo mật: Không được phép xóa hóa đơn chưa thanh toán. Vui lòng thanh toán trước.');
                return;
            }
            if (confirm('Xóa hóa đơn này?')) {
                axios.delete('/api/invoices/' + maHD)
                    .then(() => {
                        this.fetchInvoices();
                    })
                    .catch(err => alert('Lỗi xóa hóa đơn: ' + (err.response?.data?.message || err.message)));
            }
        },

        sendInvoiceEmail(maHD, event) {
            if (this.sendingEmails.includes(maHD)) return;
            this.sendingEmails.push(maHD);
            
            const btn = event.currentTarget;
            const originalHtml = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = `<svg class="animate-spin h-4 w-4 text-indigo-600 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>`;
            
            axios.post('/api/invoices/' + maHD + '/send-email')
                .then(res => {
                    alert(res.data.message);
                })
                .catch(err => {
                    alert('Lỗi gửi email: ' + (err.response?.data?.message || err.message));
                })
                .finally(() => {
                    this.sendingEmails = this.sendingEmails.filter(id => id !== maHD);
                    btn.disabled = false;
                    btn.innerHTML = originalHtml;
                });
        },

        // --- ASSETS CRUD ---
        openAddAsset() {
            this.isEditingAsset = false;
            this.assetForm = { maTaiSan: null, tenTaiSan: '', soLuong: 1, tinhTrang: 'Tốt', maPhong: '' };
            this.showAssetModal = true;
            this.$nextTick(() => lucide.createIcons());
        },

        openAddAssetForRoom(roomId) {
            this.openAddAsset();
            this.assetForm.maPhong = roomId;
        },

        openEditAsset(asset) {
            this.isEditingAsset = true;
            this.assetForm = { ...asset };
            this.showAssetModal = true;
            this.$nextTick(() => lucide.createIcons());
        },

        saveAsset() {
            if (this.isEditingAsset) {
                axios.put('/api/assets/' + this.assetForm.maTaiSan, this.assetForm)
                    .then(res => {
                        this.fetchAssets();
                        this.fetchAssetLogs();
                        this.showAssetModal = false;
                    })
                    .catch(err => alert('Lỗi cập nhật tài sản: ' + (err.response?.data?.message || err.message)));
            } else {
                axios.post('/api/assets', this.assetForm)
                    .then(res => {
                        this.fetchAssets();
                        this.fetchAssetLogs();
                        this.showAssetModal = false;
                    })
                    .catch(err => alert('Lỗi thêm tài sản: ' + (err.response?.data?.message || err.message)));
            }
        },

        deleteAsset(maTaiSan) {
            if (confirm('Bạn có chắc chắn muốn xóa tài sản này?')) {
                axios.delete('/api/assets/' + maTaiSan)
                    .then(() => {
                        this.fetchAssets();
                        this.fetchAssetLogs();
                    })
                    .catch(err => alert('Lỗi xóa tài sản: ' + (err.response?.data?.message || err.message)));
            }
        }
    }
}
</script>
@endsection
