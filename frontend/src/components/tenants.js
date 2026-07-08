// Tenants Component Logic
import axios from 'axios';

export function tenantsComponent() {
    return {
        fetchTenants() {
            axios.get('/api/tenants')
                .then(res => { this.tenants = res.data; })
                .catch(err => console.error('Lỗi tải danh sách khách thuê:', err));
        },

        filteredTenants() {
            let list = this.tenants;
            if (this.searchQuery.trim() !== '') {
                const q = this.searchQuery.toLowerCase();
                list = list.filter(t => t.hoTen.toLowerCase().includes(q));
            }
            return list;
        },

        // --- TENANTS CRUD ---
        openAddTenant() {
            this.isEditingTenant = false;
            this.tenantForm = { maKhach: null, hoTen: '', cccd: '', sdt: '', email: '', gioiTinh: 'Nam', ngaySinh: '', queQuan: '' };
            this.showTenantModal = true;
            this.$nextTick(() => {
                if (window.lucide) window.lucide.createIcons();
            });
        },

        openEditTenant(tenant) {
            this.isEditingTenant = true;
            this.tenantForm = { ...tenant };
            this.showTenantModal = true;
            this.$nextTick(() => {
                if (window.lucide) window.lucide.createIcons();
            });
        },

        saveTenant() {
            if (!this.checkAdminPermission()) return;

            // Validate Phone Number (must be exactly 10 digits)
            const phoneRegex = /^[0-9]{10}$/;
            if (!phoneRegex.test(this.tenantForm.sdt)) {
                alert('Lỗi: Số điện thoại phải nhập đúng 10 chữ số (từ 0-9).');
                return;
            }

            // Validate Birthday (must be less than today)
            if (this.tenantForm.ngaySinh) {
                const dob = new Date(this.tenantForm.ngaySinh);
                const today = new Date();
                today.setHours(0, 0, 0, 0);
                if (dob >= today) {
                    alert('Lỗi: Ngày sinh phải nhỏ hơn ngày hiện tại.');
                    return;
                }
            }

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
                        const newTenant = res.data;
                        alert(`Thêm khách thuê "${newTenant.hoTen}" thành công! Bạn cần lập hợp đồng thuê phòng cho khách mới này.`);
                        this.openAddContractForTenant(newTenant.maKhach);
                    })
                    .catch(err => alert('Lỗi thêm khách thuê: ' + (err.response?.data?.message || err.message)));
            }
        },

        deleteTenant(maKhach) {
            if (!this.checkAdminPermission()) return;

            // 1. Kiểm tra tất cả hợp đồng của khách thuê này
            const tenantContracts = this.contracts.filter(c => c.maKhach == maKhach);
            
            // 2. Tìm xem có hóa đơn nào chưa thanh toán trong các hợp đồng đó hay không
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

            // 3. Nếu phát hiện có hóa đơn chưa thanh toán, chặn hành động xóa
            if (unpaidInvoice) {
                alert(`Không thể xóa khách thuê này vì họ còn hóa đơn chưa thanh toán (Phòng ${unpaidInvoice.maPhong}, kỳ tháng ${unpaidInvoice.thang}/${unpaidInvoice.nam}). Vui lòng thanh toán hóa đơn trước khi xóa.`);
                return;
            }

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
