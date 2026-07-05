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
