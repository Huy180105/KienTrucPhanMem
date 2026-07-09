// Logic của Component Hóa Đơn (Invoices)
import axios from 'axios';

export function invoicesComponent() {
    return {
        /**
         * Lấy danh sách toàn bộ hóa đơn từ Backend API
         * GET /api/invoices
         */
        fetchInvoices() {
            axios.get('/api/invoices')
                .then(res => { this.invoices = res.data; })
                .catch(err => console.error('Lỗi tải danh sách hóa đơn:', err));
        },

        /**
         * Lọc danh sách hóa đơn hiển thị theo trạng thái (Chưa thanh toán / Đã thanh toán)
         * và theo từ khóa tìm kiếm searchQuery (mã phòng của hợp đồng tương ứng)
         */
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

        /**
         * Lấy danh sách hóa đơn liên kết với một hợp đồng cụ thể
         */
        getContractInvoices(maHopDong) {
            return this.invoices.filter(i => i.maHopDong == maHopDong);
        },

        // --- CÁC HÀM XỬ LÝ CRUD HÓA ĐƠN ---

        /**
         * Mở modal lập hóa đơn mới và thiết lập các giá trị mặc định cho kỳ hóa đơn hiện tại
         */
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
                strategy: 'default', // Sử dụng mẫu Strategy thiết kế mặc định ở Backend
                baseRent: 0,
                tongTien: 0
            };
            this.showInvoiceModal = true;
            this.$nextTick(() => {
                if (window.lucide) window.lucide.createIcons();
            });
        },

        /**
         * Mở modal lập hóa đơn cho một hợp đồng cụ thể
         */
        openAddInvoiceForContract(contractId) {
            this.openAddInvoice();
            this.invoiceForm.maHopDong = contractId;
            this.updateInvoiceBaseRent();
        },

        /**
         * Tự động lấy tiền thuê nhà gốc của hợp đồng để làm cơ sở tính toán tổng tiền
         */
        updateInvoiceBaseRent() {
            const contract = this.contracts.find(c => c.maHopDong === this.invoiceForm.maHopDong);
            if (contract) {
                this.invoiceForm.baseRent = contract.giaThueThang;
                this.calculateTotal(); // Tự động tính toán tổng tiền hóa đơn dự toán
            }
        },

        /**
         * Gọi API tính toán tổng tiền hóa đơn theo Strategy Pattern ở Backend
         * POST /api/invoices/calculate
         * 
         * Luồng chạy:
         * Client gửi chỉ số điện/nước cũ & mới và phương thức tính (mặc định/trễ hạn) lên Backend.
         * Backend sẽ chọn chiến lược phù hợp và trả về tổng tiền chính xác.
         */
        calculateTotal() {
            const form = this.invoiceForm;
            if (!form.maHopDong) return;

            // Nếu chỉ số nhập sai thì không tính tiền phụ phí điện nước
            if (form.dienMoi < form.dienCu || form.nuocMoi < form.nuocCu) {
                form.tongTien = form.baseRent;
                return;
            }

            // Gọi API tính tiền theo Strategy Pattern
            axios.post('/api/invoices/calculate', {
                maHopDong: form.maHopDong,
                dienCu: form.dienCu,
                dienMoi: form.dienMoi,
                nuocCu: form.nuocCu,
                nuocMoi: form.nuocMoi,
                strategy: form.strategy
            })
            .then(res => {
                form.tongTien = res.data.tongTien; // Nhận tổng tiền từ backend trả về
            })
            .catch(err => {
                console.error('Lỗi tính toán hóa đơn bằng Strategy:', err);
            });
        },

        /**
         * Lưu hóa đơn mới xuống cơ sở dữ liệu
         * Chứa rất nhiều ràng buộc kiểm tra nghiệp vụ quan trọng ở Client-side
         */
        saveInvoice() {
            if (!this.checkAdminPermission()) return; // Bảo mật NFR-03

            const form = this.invoiceForm;

            // Nghiệp vụ 1: Chỉ số mới phải lớn hơn hoặc bằng chỉ số cũ
            if (form.dienMoi < form.dienCu) {
                alert('Lỗi: Số điện mới phải lớn hơn hoặc bằng số điện cũ.');
                return;
            }
            if (form.nuocMoi < form.nuocCu) {
                alert('Lỗi: Số nước mới phải lớn hơn hoặc bằng số nước cũ.');
                return;
            }

            // Nghiệp vụ 2: Kỳ hóa đơn không được vượt quá thời gian hiện tại
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
                
                // Nghiệp vụ 3: Kỳ hóa đơn phải thuộc thời hạn hiệu lực của hợp đồng thuê phòng
                if (billingPeriod < startPeriod || billingPeriod > endPeriod) {
                    alert(`Lỗi: Kỳ hóa đơn (Tháng ${form.thang}/${form.nam}) phải nằm trong thời hạn hiệu lực của hợp đồng (từ ${this.formatDate(contract.ngayBatDau)} đến ${this.formatDate(contract.ngayKetThuc)}).`);
                    return;
                }

                // Nghiệp vụ 4: Mỗi phòng trọ chỉ được phép có duy nhất 1 hóa đơn cho mỗi kỳ (Tháng/Năm)
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

            // Tiến hành gửi yêu cầu lập hóa đơn
            axios.post('/api/invoices', this.invoiceForm)
                .then(res => {
                    this.fetchInvoices();
                    this.showInvoiceModal = false;
                })
                .catch(err => alert('Lỗi lập hóa đơn: ' + (err.response?.data?.message || err.message)));
        },

        /**
         * Thanh toán hóa đơn (Ghi nhận trạng thái Đã thanh toán)
         * PUT /api/invoices/{id}/pay
         */
        payInvoice(maHD) {
            if (!this.checkAdminPermission()) return; // Bảo mật NFR-03
            axios.put('/api/invoices/' + maHD + '/pay')
                .then(res => {
                    this.fetchInvoices();
                    alert('Thanh toán hóa đơn thành công! Doanh thu đã được ghi nhận.');
                })
                .catch(err => alert('Lỗi thanh toán hóa đơn: ' + (err.response?.data?.message || err.message)));
        },

        /**
         * Xóa hóa đơn khỏi cơ sở dữ liệu
         * Nghiệp vụ bảo mật: Không được phép xóa các hóa đơn ở trạng thái 'Chưa thanh toán' để tránh thất thoát dữ liệu.
         */
        deleteInvoice(maHD) {
            if (!this.checkAdminPermission()) return; // Bảo mật NFR-03
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

        /**
         * Gửi email thông báo chi tiết hóa đơn (tiền phòng, điện, nước) tới khách thuê tương ứng
         * POST /api/invoices/{id}/send-email
         */
        sendInvoiceEmail(maHD, event) {
            if (this.sendingEmails.includes(maHD)) return;
            this.sendingEmails.push(maHD);
            
            // Hiển thị vòng quay Spinner tải trong khi gửi mail
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
