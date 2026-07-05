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

                <div class="flex justify-between items-center text-xs font-medium text-indigo-955 pt-2">
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
