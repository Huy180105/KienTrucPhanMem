// Logic của Component Khách Thuê (Tenants)
import axios from 'axios';

export function tenantsComponent() {
    return {
        /**
         * Lấy danh sách toàn bộ khách thuê từ Backend API
         * GET /api/tenants
         */
        fetchTenants() {
            axios.get('/api/tenants')
                .then(res => { this.tenants = res.data; })
                .catch(err => console.error('Lỗi tải danh sách khách thuê:', err));
        },

        /**
         * Lọc danh sách khách thuê theo từ khóa tìm kiếm searchQuery (Họ tên)
         */
        filteredTenants() {
            let list = this.tenants;
            if (this.searchQuery.trim() !== '') {
                const q = this.searchQuery.toLowerCase();
                list = list.filter(t => t.hoTen.toLowerCase().includes(q));
            }
            return list;
        },

        // --- CÁC HÀM XỬ LÝ CRUD KHÁCH THUÊ ---

        /**
         * Mở modal thêm khách thuê mới và reset dữ liệu form về mặc định
         */
        openAddTenant() {
            this.isEditingTenant = false;
            this.tenantForm = { maKhach: null, hoTen: '', cccd: '', sdt: '', email: '', gioiTinh: 'Nam', ngaySinh: '', queQuan: '' };
            this.showTenantModal = true;
            this.$nextTick(() => {
                if (window.lucide) window.lucide.createIcons();
            });
        },

        /**
         * Mở modal chỉnh sửa thông tin khách thuê có sẵn
         */
        openEditTenant(tenant) {
            this.isEditingTenant = true;
            this.tenantForm = { ...tenant };
            this.showTenantModal = true;
            this.$nextTick(() => {
                if (window.lucide) window.lucide.createIcons();
            });
        },

        /**
         * Lưu thông tin khách thuê (Thêm mới hoặc Cập nhật)
         * Chứa các bước xác thực dữ liệu quan trọng ở Client-side
         */
        saveTenant() {
            if (!this.checkAdminPermission()) return; // Bảo mật NFR-03

            // Bước 1: Xác thực số điện thoại (phải nhập đúng 10 chữ số từ 0-9)
            const phoneRegex = /^[0-9]{10}$/;
            if (!phoneRegex.test(this.tenantForm.sdt)) {
                alert('Lỗi: Số điện thoại phải nhập đúng 10 chữ số (từ 0-9).');
                return;
            }

            // Bước 2: Xác thực ngày sinh (phải nhỏ hơn ngày hiện tại)
            if (this.tenantForm.ngaySinh) {
                const dob = new Date(this.tenantForm.ngaySinh);
                const today = new Date();
                today.setHours(0, 0, 0, 0);
                if (dob >= today) {
                    alert('Lỗi: Ngày sinh phải nhỏ hơn ngày hiện tại.');
                    return;
                }
            }

            // Bước 3: Gửi dữ liệu qua API Backend
            if (this.isEditingTenant) {
                // Cập nhật thông tin khách thuê
                axios.put('/api/tenants/' + this.tenantForm.maKhach, this.tenantForm)
                    .then(res => {
                        this.fetchTenants();
                        this.showTenantModal = false;
                    })
                    .catch(err => alert('Lỗi cập nhật khách thuê: ' + (err.response?.data?.message || err.message)));
            } else {
                // Thêm mới khách thuê
                axios.post('/api/tenants', this.tenantForm)
                    .then(res => {
                        this.fetchTenants();
                        this.showTenantModal = false;
                        const newTenant = res.data;
                        alert(`Thêm khách thuê "${newTenant.hoTen}" thành công! Bạn cần lập hợp đồng thuê phòng cho khách mới này.`);
                        // Tự động chuyển hướng lập hợp đồng cho khách thuê mới
                        this.openAddContractForTenant(newTenant.maKhach);
                    })
                    .catch(err => alert('Lỗi thêm khách thuê: ' + (err.response?.data?.message || err.message)));
            }
        },

        /**
         * Xóa thông tin khách thuê khỏi hệ thống
         * Chứa ràng buộc nghiệp vụ quan trọng: Không được xóa khách thuê nếu họ còn hóa đơn chưa thanh toán.
         */
        deleteTenant(maKhach) {
            if (!this.checkAdminPermission()) return; // Bảo mật NFR-03

            // 1. Lọc tất cả hợp đồng liên kết với khách thuê này
            const tenantContracts = this.contracts.filter(c => c.maKhach == maKhach);
            
            // 2. Kiểm tra xem có hóa đơn nào của khách này đang ở trạng thái 'Chưa thanh toán'
            let unpaidInvoice = null;
            for (const contract of tenantContracts) {
                const unpaid = this.invoices.find(i => i.maHopDong == contract.maHopDong && i.trangThai === 'Chưa thanh toán');
                if (unpaid) {
                    unpaidInvoice = {
                        thang: unpaid.thang,
                        nam: unpaid.nam,
                        maPhong: contract.maPhong
                    };
                    break;
                }
            }

            // 3. Nếu phát hiện có hóa đơn chưa thanh toán, chặn hành động xóa và hiển thị cảnh báo
            if (unpaidInvoice) {
                alert(`Không thể xóa khách thuê này vì họ còn hóa đơn chưa thanh toán (Phòng ${unpaidInvoice.maPhong}, kỳ tháng ${unpaidInvoice.thang}/${unpaidInvoice.nam}). Vui lòng thanh toán hóa đơn trước khi xóa.`);
                return;
            }

            // 4. Nếu hợp lệ, tiến hành gửi yêu cầu DELETE
            if (confirm('Bạn có chắc muốn xóa thông tin khách thuê này?')) {
                axios.delete('/api/tenants/' + maKhach)
                    .then(() => {
                        this.fetchTenants();
                        this.fetchContracts();
                        this.fetchRooms();
                    })
                    .catch(err => alert('Lỗi xóa khách thuê: ' + (err.response?.data?.message || err.message)));
            }
        }
    };
}
