// Contracts Component Logic
import axios from 'axios';

export function contractsComponent() {
    return {
        fetchContracts() {
            axios.get('/api/contracts')
                .then(res => { this.contracts = res.data; })
                .catch(err => console.error('Lỗi tải danh sách hợp đồng:', err));
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

        // Helper relations resolver
        getRoomContract(maPhong) {
            return this.contracts.find(c => c.maPhong === maPhong && c.trangThai === 'Đang hiệu lực');
        },

        getTenantName(maKhach) {
            const tenant = this.tenants.find(t => t.maKhach == maKhach);
            return tenant ? tenant.hoTen : 'Không rõ';
        },

        getRoomIdFromContract(maHopDong) {
            const contract = this.contracts.find(c => c.maHopDong == maHopDong);
            return contract ? contract.maPhong : 'Không rõ';
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
            this.$nextTick(() => {
                if (window.lucide) window.lucide.createIcons();
            });
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
        }
    };
}
