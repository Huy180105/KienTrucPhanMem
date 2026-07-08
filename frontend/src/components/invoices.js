// Invoices Component Logic
import axios from 'axios';

export function invoicesComponent() {
    return {
        fetchInvoices() {
            axios.get('/api/invoices')
                .then(res => { this.invoices = res.data; })
                .catch(err => console.error('Lỗi tải danh sách hóa đơn:', err));
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

        getContractInvoices(maHopDong) {
            return this.invoices.filter(i => i.maHopDong == maHopDong);
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
            this.$nextTick(() => {
                if (window.lucide) window.lucide.createIcons();
            });
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

        calculateTotal() {
            const form = this.invoiceForm;
            if (!form.maHopDong) return;

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

            if (form.dienMoi < form.dienCu) {
                alert('Lỗi: Số điện mới phải lớn hơn hoặc bằng số điện cũ.');
                return;
            }
            if (form.nuocMoi < form.nuocCu) {
                alert('Lỗi: Số nước mới phải lớn hơn hoặc bằng số nước cũ.');
                return;
            }

            const today = new Date();
            const currentYear = today.getFullYear();
            const currentMonth = today.getMonth() + 1;

            if (form.nam > currentYear || (form.nam === currentYear && form.thang > currentMonth)) {
                alert('Lỗi: Kỳ hóa đơn (Tháng/Năm) không được lớn hơn tháng/năm hiện tại.');
                return;
            }

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
        }
    };
}
