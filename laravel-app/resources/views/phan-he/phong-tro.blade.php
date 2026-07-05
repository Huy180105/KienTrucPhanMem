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
