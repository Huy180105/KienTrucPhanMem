// Rooms Component Logic
import axios from 'axios';

export function roomsComponent() {
    return {
        // State defined here will merge with general Alpine state
        // fetchRooms() updates the shared list of rooms
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

        // --- ROOMS CRUD ---
        openAddRoom() {
            this.isEditingRoom = false;
            this.roomForm = { maPhong: '', tenPhong: '', tang: 1, giaPhong: 2500000, trangThai: 'Trống' };
            this.showRoomModal = true;
            this.$nextTick(() => {
                if (window.lucide) window.lucide.createIcons();
            });
        },

        openEditRoom(room) {
            this.isEditingRoom = true;
            this.roomForm = { ...room };
            this.showRoomModal = true;
            this.$nextTick(() => {
                if (window.lucide) window.lucide.createIcons();
            });
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

        selectRoom(room) {
            this.selectedRoom = room;
            this.$nextTick(() => {
                if (window.lucide) window.lucide.createIcons();
            });
        }
    };
}
