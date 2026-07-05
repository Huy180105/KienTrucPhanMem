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
                    <tr class="border-b border-slate-100 bg-slate-50/50 text-slate-455 text-[10px] font-bold uppercase">
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
