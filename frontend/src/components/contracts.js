// Logic của Component Hợp Đồng (Contracts)
import axios from 'axios';

export function contractsComponent() {
    return {
        /**
         * Lấy danh sách toàn bộ hợp đồng từ Backend API
         * GET /api/contracts
         */
        fetchContracts() {
            axios.get('/api/contracts')
                .then(res => { 
                    this.contracts = res.data; 
                })
                .catch(err => console.error('Lỗi tải danh sách hợp đồng:', err));
        },

        /**
         * Lọc danh sách hợp đồng hiển thị trên giao diện theo bộ lọc (Tất cả / Đang hiệu lực / Đã thanh lý)
         * và theo thanh tìm kiếm searchQuery (mã phòng hoặc tên khách thuê)
         */
        filteredContracts() {
            let list = this.contracts;
            
            // Lọc theo trạng thái hiệu lực
            if (this.contractFilter === 'active') list = list.filter(c => c.trangThai === 'Đang hiệu lực');
            if (this.contractFilter === 'terminated') list = list.filter(c => c.trangThai === 'Đã thanh lý');

            // Lọc theo từ khóa tìm kiếm (mã phòng hoặc tên khách)
            if (this.searchQuery.trim() !== '') {
                const q = this.searchQuery.toLowerCase();
                list = list.filter(c => 
                    c.maPhong.toLowerCase().includes(q) || 
                    this.getTenantName(c.maKhach).toLowerCase().includes(q)
                );
            }
            return list;
        },

        /**
         * Tìm hợp đồng đang có hiệu lực của một phòng trọ cụ thể
         * Dùng để hiển thị thông tin thuê ở Drawer chi tiết phòng
         */
        getRoomContract(maPhong) {
            return this.contracts.find(c => c.maPhong === maPhong && c.trangThai === 'Đang hiệu lực');
        },

        /**
         * Lấy tên khách thuê theo mã khách
         */
        getTenantName(maKhach) {
            const tenant = this.tenants.find(t => t.maKhach == maKhach);
            return tenant ? tenant.hoTen : 'Không rõ';
        },

        /**
         * Lấy mã phòng trọ từ một hợp đồng cụ thể
         */
        getRoomIdFromContract(maHopDong) {
            const contract = this.contracts.find(c => c.maHopDong == maHopDong);
            return contract ? contract.maPhong : 'Không rõ';
        },

        // --- CÁC HÀM XỬ LÝ CRUD HỢP ĐỒNG ---

        /**
         * Mở modal thêm hợp đồng mới và reset form về mặc định
         */
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
            this.$nextTick(() => {
                if (window.lucide) window.lucide.createIcons(); // Vẽ lại icon Lucide
            });
        },

        /**
         * Mở modal thêm hợp đồng và tự điền trước thông tin phòng trọ + giá thuê
         */
        openAddContractForRoom(roomId, roomPrice) {
            this.openAddContract();
            this.contractForm.maPhong = roomId;
            this.contractForm.giaThueThang = roomPrice;
            this.contractForm.tienCoc = roomPrice; 
        },

        /**
         * Mở modal thêm hợp đồng và điền trước thông tin khách thuê tương ứng
         */
        openAddContractForTenant(tenantId) {
            this.openAddContract();
            this.contractForm.maKhach = tenantId;
            this.activeTab = 'contracts'; // Chuyển sang tab hợp đồng
        },

        /**
         * Lưu hợp đồng mới (gửi yêu cầu POST lên API Backend)
         * Kích hoạt Facade ở Backend để tự động tạo hợp đồng và chuyển trạng thái phòng
         */
        saveContract() {
            if (!this.checkAdminPermission()) return; // Kiểm tra quyền hạn bảo mật (NFR-03)
            axios.post('/api/contracts', this.contractForm)
                .then(res => {
                    this.fetchContracts(); // Tải lại danh sách hợp đồng
                    this.fetchRooms();     // Tải lại danh sách phòng trọ (do trạng thái đã đổi)
                    this.showContractModal = false; // Đóng modal
                })
                .catch(err => alert('Lỗi lập hợp đồng: ' + (err.response?.data?.message || err.message)));
        },

        /**
         * Thanh lý hợp đồng đang hoạt động
         * PUT /api/contracts/{id}/terminate
         * Chuyển trạng thái hợp đồng thành Đã thanh lý, giải phóng phòng thành Trống
         */
        terminateContract(maHopDong) {
            if (!this.checkAdminPermission()) return; // Bảo mật NFR-03
            if (confirm('Bạn có chắc chắn muốn thanh lý hợp đồng này? Trạng thái phòng sẽ được cập nhật về trống.')) {
                axios.put('/api/contracts/' + maHopDong + '/terminate')
                    .then(res => {
                        this.fetchContracts();
                        this.fetchRooms();
                    })
                    .catch(err => alert('Lỗi thanh lý hợp đồng: ' + (err.response?.data?.message || err.message)));
            }
        },

        /**
         * Xóa hẳn hợp đồng khỏi cơ sở dữ liệu
         * DELETE /api/contracts/{id}
         */
        deleteContract(maHopDong) {
            if (!this.checkAdminPermission()) return; // Bảo mật NFR-03
            if (confirm('Xóa hợp đồng này khỏi cơ sở dữ liệu?')) {
                axios.delete('/api/contracts/' + maHopDong)
                    .then(() => {
                        this.fetchContracts();
                        this.fetchRooms();
                    })
                    .catch(err => alert('Lỗi xóa hợp đồng: ' + (err.response?.data?.message || err.message)));
            }
        },

        /**
         * Gửi email nhắc nhở hết hạn hợp đồng thuê phòng tới hòm thư khách thuê
         * POST /api/contracts/{id}/send-reminder
         */
        sendEmailReminder(contract, event) {
            const tenant = this.tenants.find(t => t.maKhach === contract.maKhach);
            if (!tenant) {
                alert('Không tìm thấy thông tin khách thuê!');
                return;
            }
            const targetEmail = tenant.email || 'khachthue@gmail.com';
            
            // Đổi trạng thái nút bấm sang Đang gửi... để tránh người dùng click nhiều lần
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
                // Trả lại trạng thái ban đầu cho nút bấm
                btn.disabled = false;
                btn.innerHTML = originalText;
            });
        }
    };
}
