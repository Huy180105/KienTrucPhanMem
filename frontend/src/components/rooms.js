// Logic của Component Phòng Trọ (Rooms)
import axios from 'axios';

export function roomsComponent() {
    return {
        /**
         * Lấy danh sách phòng trọ từ Backend API
         * GET /api/rooms
         */
        fetchRooms() {
            axios.get('/api/rooms')
                .then(res => { 
                    this.rooms = res.data; 
                    // Nếu đang xem chi tiết một phòng trọ, tự động cập nhật thông tin phòng mới nhất
                    if (this.selectedRoom) {
                        const updated = this.rooms.find(r => r.maPhong === this.selectedRoom.maPhong);
                        if (updated) this.selectedRoom = updated;
                    }
                })
                .catch(err => console.error('Lỗi tải danh sách phòng:', err));
        },

        /**
         * Lọc danh sách phòng hiển thị trên giao diện theo bộ lọc (Tất cả / Trống / Đã thuê / Bảo trì)
         * và theo từ khóa tìm kiếm searchQuery (mã phòng hoặc tên phòng)
         */
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

        // --- CÁC HÀM XỬ LÝ CRUD PHÒNG TRỌ ---

        /**
         * Mở modal thêm phòng trọ mới
         */
        openAddRoom() {
            this.isEditingRoom = false;
            this.roomForm = { maPhong: '', tenPhong: '', tang: 1, giaPhong: 2500000, trangThai: 'Trống' };
            this.showRoomModal = true;
            this.$nextTick(() => {
                if (window.lucide) window.lucide.createIcons();
            });
        },

        /**
         * Mở modal sửa thông tin phòng trọ có sẵn
         */
        openEditRoom(room) {
            this.isEditingRoom = true;
            this.roomForm = { ...room };
            this.showRoomModal = true;
            this.$nextTick(() => {
                if (window.lucide) window.lucide.createIcons();
            });
        },

        /**
         * Lưu phòng trọ (Thêm mới hoặc Cập nhật tùy thuộc vào cờ isEditingRoom)
         * Gửi dữ liệu qua API tương ứng ở Backend
         */
        saveRoom() {
            if (!this.checkAdminPermission()) return; // Bảo mật NFR-03
            if (this.isEditingRoom) {
                // Gửi yêu cầu PUT để cập nhật phòng trọ
                axios.put('/api/rooms/' + this.roomForm.maPhong, this.roomForm)
                    .then(res => {
                        this.fetchRooms();
                        this.showRoomModal = false;
                    })
                    .catch(err => alert('Lỗi cập nhật phòng: ' + (err.response?.data?.message || err.message)));
            } else {
                // Gửi yêu cầu POST để thêm phòng trọ mới
                axios.post('/api/rooms', this.roomForm)
                    .then(res => {
                        this.fetchRooms();
                        this.showRoomModal = false;
                    })
                    .catch(err => alert('Lỗi tạo phòng: ' + (err.response?.data?.message || err.message)));
            }
        },

        /**
         * Xóa phòng trọ khỏi cơ sở dữ liệu
         * DELETE /api/rooms/{id}
         */
        deleteRoom(maPhong) {
            if (!this.checkAdminPermission()) return; // Bảo mật NFR-03
            if (confirm(`Bạn có chắc muốn xóa phòng ${maPhong}?`)) {
                axios.delete('/api/rooms/' + maPhong)
                    .then(() => {
                        this.fetchRooms();
                        this.selectedRoom = null; // Đóng khung chi tiết phòng
                    })
                    .catch(err => alert('Lỗi xóa phòng: ' + (err.response?.data?.message || err.message)));
            }
        },

        /**
         * Lựa chọn phòng trọ để hiển thị chi tiết ở khung Drawer (Right sidebar)
         */
        selectRoom(room) {
            this.selectedRoom = room;
            this.$nextTick(() => {
                if (window.lucide) window.lucide.createIcons();
            });
        }
    };
}
