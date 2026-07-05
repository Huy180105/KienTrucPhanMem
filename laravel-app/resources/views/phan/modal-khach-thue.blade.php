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

