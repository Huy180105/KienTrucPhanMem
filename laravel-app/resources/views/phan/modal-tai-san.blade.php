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
