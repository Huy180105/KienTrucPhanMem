import Alpine from 'alpinejs';
import axios from 'axios';

window.Alpine = Alpine;
window.axios = axios;

// Configure Axios defaults
axios.defaults.withCredentials = true;

// Setup Alpine JS App
window.rentalApp = function() {
    return {
        // Auth state
        currentUser: null,
        userRole: 'admin',
        assetLogs: [],

        // Tab system
        activeTab: 'dashboard',
        tabTitles: {
            dashboard: 'Bảng Điều Khiển Tổng Quan',
            rooms: 'Quản Lý Phòng Trọ',
            tenants: 'Danh Sách Khách Thuê',
            contracts: 'Quản Lý Hợp Đồng',
            invoices: 'Danh Sách Hóa Đơn',
            assets: 'Quản Lý Tài Sản'
        },

        // Search and Filters
        searchQuery: '',
        roomFilter: 'all',
        contractFilter: 'all',
        invoiceFilter: 'all',
        assetRoomFilter: 'all',

        // Selected State
        selectedRoom: null,

        // Database Collections (Simulated State)
        rooms: [],
        tenants: [],
        contracts: [],
        invoices: [],
        sendingEmails: [],
        assets: [],

        // Modal Forms State
        showRoomModal: false,
        isEditingRoom: false,
        roomForm: { maPhong: '', tenPhong: '', tang: 1, giaPhong: 2500000, trangThai: 'Trống' },

        showTenantModal: false,
        isEditingTenant: false,
        tenantForm: { maKhach: null, hoTen: '', cccd: '', sdt: '', email: '', gioiTinh: 'Nam', ngaySinh: '', queQuan: '' },

        showContractModal: false,
        contractForm: { maHopDong: null, maPhong: '', maKhach: '', ngayLap: '', ngayBatDau: '', ngayKetThuc: '', tienCoc: 0, giaThueThang: 0 },

        showInvoiceModal: false,
        invoiceForm: { maHD: null, maHopDong: '', thang: '', nam: '', ngayLap: '', dienCu: 0, dienMoi: 0, nuocCu: 0, nuocMoi: 0, strategy: 'default', baseRent: 0, tongTien: 0 },

        showAssetModal: false,
        isEditingAsset: false,
        assetForm: { maTaiSan: null, tenTaiSan: '', soLuong: 1, tinhTrang: 'Tốt', maPhong: '' },
        // Initialization
        initApp() {
            // Check authentication
            axios.get('/api/user')
                .then(res => {
                    if (res.data.success) {
                        this.currentUser = res.data.user;
                        this.userRole = res.data.user.role || 'admin';
                        axios.defaults.headers.common['X-User-Role'] = this.userRole;

                        // Load data
                        this.fetchRooms();
                        this.fetchTenants();
                        this.fetchContracts();
                        this.fetchInvoices();
                        this.fetchAssets();
                        this.fetchAssetLogs();

                        // Call icon creation on initial load after container becomes visible
                        this.$nextTick(() => {
                            if (window.lucide) {
                                window.lucide.createIcons();
                            }
                        });
                    } else {
                        window.location.href = '/login.html';
                    }
                })
                .catch(() => {
                    window.location.href = '/login.html';
                });

            // Watch for tab changes and render icons dynamically
            this.$watch('activeTab', () => {
                this.$nextTick(() => {
                    if (window.lucide) {
                        window.lucide.createIcons();
                    }
                });
            });

            // Render Lucide icons when Alpine DOM updates initially
            this.$nextTick(() => {
                if (window.lucide) {
                    window.lucide.createIcons();
                }
            });
        },

        // --- BACKEND API SYNCHRONIZERS ---
        fetchRooms() {
            axios.get('/api/rooms')
                .then(res => { 
                    this.rooms = res.data; 
                    if (this.selectedRoom) {
                        const updated = this.rooms.find(r => r.maPhong === this.selectedRoom.maPhong);
                        if (updated) this.selectedRoom = updated;
                    }
                })
                .catch(err => console.error('Lỗi tải danh sách phòng:', err));
        },
        fetchTenants() {
            axios.get('/api/tenants')
                .then(res => { this.tenants = res.data; })
                .catch(err => console.error('Lỗi tải danh sách khách thuê:', err));
        },
        fetchContracts() {
            axios.get('/api/contracts')
                .then(res => { this.contracts = res.data; })
                .catch(err => console.error('Lỗi tải danh sách hợp đồng:', err));
        },
        fetchInvoices() {
            axios.get('/api/invoices')
                .then(res => { this.invoices = res.data; })
                .catch(err => console.error('Lỗi tải danh sách hóa đơn:', err));
        },
        fetchAssets() {
            axios.get('/api/assets')
                .then(res => { this.assets = res.data; })
                .catch(err => console.error('Lỗi tải danh sách tài sản:', err));
        },
        fetchAssetLogs() {
            axios.get('/api/assets/logs')
                .then(res => { this.assetLogs = res.data; })
                .catch(err => console.error('Lỗi tải nhật ký đối soát:', err));
        },
        changeRole() {
            axios.defaults.headers.common['X-User-Role'] = this.userRole;
        },
        checkAdminPermission() {
            if (this.userRole !== 'admin') {
                alert('Yêu cầu bảo mật (NFR-03): Chỉ Quản trị viên mới được phép thực hiện các chức năng Thêm, Sửa, Xóa.');
                return false;
            }
            return true;
        },

        logoutUser() {
            axios.post('/api/logout')
                .then(() => {
                    window.location.href = '/login.html';
                })
                .catch(err => {
                    console.error('Lỗi đăng xuất:', err);
                    window.location.href = '/login.html';
                });
        },

        // Helper: Formatting Currency
        formatCurrency(value) {
            return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(value);
        },

        // Helper: Formatting Date
        formatDate(dateStr) {
            if (!dateStr) return '';
            const d = new Date(dateStr);
            return `${String(d.getDate()).padStart(2, '0')}/${String(d.getMonth() + 1).padStart(2, '0')}/${d.getFullYear()}`;
        },

        // Helper: Dynamic badge classes
        getRoomStatusClass(status) {
            if (status === 'Trống') return 'bg-emerald-50 text-emerald-600';
            if (status === 'Đã thuê') return 'bg-indigo-50 text-indigo-600';
            return 'bg-amber-50 text-amber-600';
        },

        // Statistics computations
        rentedRoomsCount() { return this.rooms.filter(r => r.trangThai === 'Đã thuê').length; },
        vacantRoomsCount() { return this.rooms.filter(r => r.trangThai === 'Trống').length; },
        maintenanceRoomsCount() { return this.rooms.filter(r => r.trangThai === 'Đang bảo trì').length; },
        activeContractsCount() { return this.contracts.filter(c => c.trangThai === 'Đang hiệu lực').length; },
        unpaidInvoicesCount() { return this.invoices.filter(i => i.trangThai === 'Chưa thanh toán').length; },
        unpaidInvoicesSum() { return this.invoices.filter(i => i.trangThai === 'Chưa thanh toán').reduce((sum, item) => sum + item.tongTien, 0); },
        totalRevenue() { return this.invoices.filter(i => i.trangThai === 'Đã thanh toán').reduce((sum, item) => sum + item.tongTien, 0); },

        // Filter methods
        filteredRooms() {
            let list = this.rooms;
            if (this.roomFilter === 'available') list = list.filter(r => r.trangThai === 'Trống');
            if (this.roomFilter === 'rented') list = list.filter(r => r.trangThai === 'Đã thuê');
            if (this.roomFilter === 'maintenance') list = list.filter(r => r.trangThai === 'Đang bảo trì');

            if (this.searchQuery.trim() !== '') {
                const q = this.searchQuery.toLowerCase();
                list = list.filter(r => r.maPhong.toLowerCase().includes(q) || r.tenPhong.toLowerCase().includes(q));
            }
            return list;
        },

        filteredTenants() {
            let list = this.tenants;
            if (this.searchQuery.trim() !== '') {
                const q = this.searchQuery.toLowerCase();
                list = list.filter(t => t.hoTen.toLowerCase().includes(q));
            }
            return list;
        },

        filteredContracts() {
            let list = this.contracts;
            if (this.contractFilter === 'active') list = list.filter(c => c.trangThai === 'Đang hiệu lực');
            if (this.contractFilter === 'terminated') list = list.filter(c => c.trangThai === 'Đã thanh lý');

            if (this.searchQuery.trim() !== '') {
                const q = this.searchQuery.toLowerCase();
                list = list.filter(c => c.maPhong.toLowerCase().includes(q) || this.getTenantName(c.maKhach).toLowerCase().includes(q));
            }
            return list;
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

        filteredAssets() {
            let list = this.assets;
            if (this.assetRoomFilter !== 'all') {
                list = list.filter(a => a.maPhong === this.assetRoomFilter);
            }

            if (this.searchQuery.trim() !== '') {
                const q = this.searchQuery.toLowerCase();
                list = list.filter(a => a.tenTaiSan.toLowerCase().includes(q) || (a.maPhong && a.maPhong.toLowerCase().includes(q)));
            }
            return list;
        },

        // Helper relations resolver
        getRoomContract(maPhong) {
            return this.contracts.find(c => c.maPhong === maPhong && c.trangThai === 'Đang hiệu lực');
        },

        getTenantName(maKhach) {
            const tenant = this.tenants.find(t => t.maKhach == maKhach);
            return tenant ? tenant.hoTen : 'Không rõ';
        },

        getContractInvoices(maHopDong) {
            return this.invoices.filter(i => i.maHopDong == maHopDong);
        },

        getRoomIdFromContract(maHopDong) {
            const contract = this.contracts.find(c => c.maHopDong == maHopDong);
            return contract ? contract.maPhong : 'Không rõ';
        },

        getRoomAssets(maPhong) {
            return this.assets.filter(a => a.maPhong === maPhong);
        },

        // --- ROOMS CRUD ---
        openAddRoom() {
            this.isEditingRoom = false;
            this.roomForm = { maPhong: '', tenPhong: '', tang: 1, giaPhong: 2500000, trangThai: 'Trống' };
            this.showRoomModal = true;
            this.$nextTick(() => lucide.createIcons());
        },

        openEditRoom(room) {
            this.isEditingRoom = true;
            this.roomForm = { ...room };
            this.showRoomModal = true;
            this.$nextTick(() => lucide.createIcons());
        },

        saveRoom() {
            if (!this.checkAdminPermission()) return;
            if (this.isEditingRoom) {
                axios.put('/api/rooms/' + this.roomForm.maPhong, this.roomForm)
                    .then(res => {
                        this.fetchRooms();
                        this.showRoomModal = false;
                    })
                    .catch(err => alert('Lỗi cập nhật phòng: ' + (err.response?.data?.message || err.message)));
            } else {
                axios.post('/api/rooms', this.roomForm)
                    .then(res => {
                        this.fetchRooms();
                        this.showRoomModal = false;
                    })
                    .catch(err => alert('Lỗi tạo phòng: ' + (err.response?.data?.message || err.message)));
            }
        },

        deleteRoom(maPhong) {
            if (!this.checkAdminPermission()) return;
            if (confirm(`Bạn có chắc muốn xóa phòng ${maPhong}?`)) {
                axios.delete('/api/rooms/' + maPhong)
                    .then(() => {
                        this.fetchRooms();
                        this.selectedRoom = null;
                    })
                    .catch(err => alert('Lỗi xóa phòng: ' + (err.response?.data?.message || err.message)));
            }
        },

        // --- TENANTS CRUD ---
        openAddTenant() {
            this.isEditingTenant = false;
            this.tenantForm = { maKhach: null, hoTen: '', cccd: '', sdt: '', email: '', gioiTinh: 'Nam', ngaySinh: '', queQuan: '' };
            this.showTenantModal = true;
            this.$nextTick(() => lucide.createIcons());
        },

        openEditTenant(tenant) {
            this.isEditingTenant = true;
            this.tenantForm = { ...tenant };
            this.showTenantModal = true;
            this.$nextTick(() => lucide.createIcons());
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
        },

        // --- CONTRACTS CRUD ---
        openAddContract() {
            this.contractForm = {
                maHopDong: null,
                maPhong: '',
                maKhach: '',
                ngayLap: new Date().toISOString().split('T')[0],
                ngayBatDau: new Date().toISOString().split('T')[0],
                ngayKetThuc: '',
                tienCoc: 0,
                giaThueThang: 0
            };
            this.showContractModal = true;
            this.$nextTick(() => lucide.createIcons());
        },

        openAddContractForRoom(roomId, roomPrice) {
            this.openAddContract();
            this.contractForm.maPhong = roomId;
            this.contractForm.giaThueThang = roomPrice;
            this.contractForm.tienCoc = roomPrice; 
        },

        openAddContractForTenant(tenantId) {
            this.openAddContract();
            this.contractForm.maKhach = tenantId;
            this.activeTab = 'contracts';
        },

        saveContract() {
            if (!this.checkAdminPermission()) return;
            axios.post('/api/contracts', this.contractForm)
                .then(res => {
                    this.fetchContracts();
                    this.fetchRooms();
                    this.showContractModal = false;
                })
                .catch(err => alert('Lỗi lập hợp đồng: ' + (err.response?.data?.message || err.message)));
        },

        terminateContract(maHopDong) {
            if (!this.checkAdminPermission()) return;
            if (confirm('Bạn có chắc chắn muốn thanh lý hợp đồng này? Trạng thái phòng sẽ được cập nhật về trống.')) {
                axios.put('/api/contracts/' + maHopDong + '/terminate')
                    .then(res => {
                        this.fetchContracts();
                        this.fetchRooms();
                    })
                    .catch(err => alert('Lỗi thanh lý hợp đồng: ' + (err.response?.data?.message || err.message)));
            }
        },

        deleteContract(maHopDong) {
            if (!this.checkAdminPermission()) return;
            if (confirm('Xóa hợp đồng này khỏi cơ sở dữ liệu?')) {
                axios.delete('/api/contracts/' + maHopDong)
                    .then(() => {
                        this.fetchContracts();
                        this.fetchRooms();
                    })
                    .catch(err => alert('Lỗi xóa hợp đồng: ' + (err.response?.data?.message || err.message)));
            }
        },

        sendEmailReminder(contract, event) {
            const tenant = this.tenants.find(t => t.maKhach === contract.maKhach);
            if (!tenant) {
                alert('Không tìm thấy thông tin khách thuê!');
                return;
            }
            const targetEmail = tenant.email || 'nguyenvana@gmail.com';
            
            const btn = event.currentTarget;
            const originalText = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<span class="animate-pulse">Đang gửi...</span>';

            axios.post('/api/contracts/' + contract.maHopDong + '/send-reminder', {
                contract: {
                    ...contract,
                    ngayKetThucFormatted: this.formatDate(contract.ngayKetThuc)
                },
                tenant: tenant,
                email: targetEmail
            })
            .then(response => {
                alert('Đã gửi email nhắc nhở hết hạn đến: ' + targetEmail + ' thành công! Hãy mở Mailpit tại http://localhost:8025 để xem email.');
            })
            .catch(error => {
                alert('Gửi mail thất bại: ' + (error.response?.data?.message || error.message));
            })
            .finally(() => {
                btn.disabled = false;
                btn.innerHTML = originalText;
            });
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
            this.$nextTick(() => lucide.createIcons());
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
        },

        selectRoom(room) {
            this.selectedRoom = room;
            this.$nextTick(() => lucide.createIcons());
        },

        // --- ASSETS CRUD ---
        openAddAsset() {
            this.isEditingAsset = false;
            this.assetForm = { maTaiSan: null, tenTaiSan: '', soLuong: 1, tinhTrang: 'Tốt', maPhong: '' };
            this.showAssetModal = true;
            this.$nextTick(() => lucide.createIcons());
        },

        openAddAssetForRoom(roomId) {
            this.openAddAsset();
            this.assetForm.maPhong = roomId;
        },

        openEditAsset(asset) {
            this.isEditingAsset = true;
            this.assetForm = { ...asset };
            this.showAssetModal = true;
            this.$nextTick(() => lucide.createIcons());
        },

        saveAsset() {
            if (this.isEditingAsset) {
                axios.put('/api/assets/' + this.assetForm.maTaiSan, this.assetForm)
                    .then(res => {
                        this.fetchAssets();
                        this.fetchAssetLogs();
                        this.showAssetModal = false;
                    })
                    .catch(err => alert('Lỗi cập nhật tài sản: ' + (err.response?.data?.message || err.message)));
            } else {
                axios.post('/api/assets', this.assetForm)
                    .then(res => {
                        this.fetchAssets();
                        this.fetchAssetLogs();
                        this.showAssetModal = false;
                    })
                    .catch(err => alert('Lỗi thêm tài sản: ' + (err.response?.data?.message || err.message)));
            }
        },

        deleteAsset(maTaiSan) {
            if (confirm('Bạn có chắc chắn muốn xóa tài sản này?')) {
                axios.delete('/api/assets/' + maTaiSan)
                    .then(() => {
                        this.fetchAssets();
                        this.fetchAssetLogs();
                    })
                    .catch(err => alert('Lỗi xóa tài sản: ' + (err.response?.data?.message || err.message)));
            }
        }
    }
};

window.loginForm = function() {
    return {
        email: '',
        password: '',
        remember: false,
        showPassword: false,
        errorMessage: '',
        submitLogin() {
            this.errorMessage = '';
            axios.post('/api/login', {
                email: this.email,
                password: this.password,
                remember: this.remember
            })
            .then(res => {
                if (res.data.success) {
                    window.location.href = '/index.html';
                }
            })
            .catch(err => {
                this.errorMessage = err.response?.data?.message || 'Đăng nhập thất bại. Vui lòng kiểm tra lại.';
            });
        }
    }
};

Alpine.start();

