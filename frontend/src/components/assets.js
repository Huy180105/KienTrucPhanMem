// Logic của Component Tài Sản (Assets)
import axios from 'axios';

export function assetsComponent() {
    return {
        /**
         * Lấy danh sách toàn bộ tài sản từ Backend API
         * GET /api/assets
         */
        fetchAssets() {
            axios.get('/api/assets')
                .then(res => { this.assets = res.data; })
                .catch(err => console.error('Lỗi tải danh sách tài sản:', err));
        },

        /**
         * Lấy danh sách nhật ký đối soát tài sản (Audit Log - NFR-02) từ Backend API
         * GET /api/assets/logs
         */
        fetchAssetLogs() {
            axios.get('/api/assets/logs')
                .then(res => { this.assetLogs = res.data; })
                .catch(err => console.error('Lỗi tải nhật ký đối soát:', err));
        },

        /**
         * Lọc danh sách tài sản hiển thị theo phòng trọ và theo từ khóa tìm kiếm (Tên tài sản hoặc mã phòng)
         */
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

        /**
         * Lấy danh sách tài sản thuộc một phòng trọ cụ thể
         */
        getRoomAssets(maPhong) {
            return this.assets.filter(a => a.maPhong === maPhong);
        },

        // --- CÁC HÀM XỬ LÝ CRUD TÀI SẢN ---

        /**
         * Mở modal thêm tài sản mới
         */
        openAddAsset() {
            this.isEditingAsset = false;
            this.assetForm = { maTaiSan: null, tenTaiSan: '', soLuong: 1, tinhTrang: 'Tốt', maPhong: '' };
            this.showAssetModal = true;
            this.$nextTick(() => {
                if (window.lucide) window.lucide.createIcons();
            });
        },

        /**
         * Mở modal thêm tài sản cho một phòng cụ thể
         */
        openAddAssetForRoom(roomId) {
            this.openAddAsset();
            this.assetForm.maPhong = roomId;
        },

        /**
         * Mở modal chỉnh sửa tài sản có sẵn
         */
        openEditAsset(asset) {
            this.isEditingAsset = true;
            this.assetForm = { ...asset };
            this.showAssetModal = true;
            this.$nextTick(() => {
                if (window.lucide) window.lucide.createIcons();
            });
        },

        /**
         * Lưu tài sản (Thêm mới hoặc Cập nhật)
         * Khi lưu thành công, hệ thống tự động tải lại Nhật ký đối soát tài sản (Audit Log)
         * do có Trigger/Observer ghi nhận lịch sử thay đổi ở Backend
         */
        saveAsset() {
            if (this.isEditingAsset) {
                // Cập nhật thông tin tài sản
                axios.put('/api/assets/' + this.assetForm.maTaiSan, this.assetForm)
                    .then(res => {
                        this.fetchAssets();
                        this.fetchAssetLogs(); // Cập nhật Audit Log
                        this.showAssetModal = false;
                    })
                    .catch(err => alert('Lỗi cập nhật tài sản: ' + (err.response?.data?.message || err.message)));
            } else {
                // Thêm tài sản mới
                axios.post('/api/assets', this.assetForm)
                    .then(res => {
                        this.fetchAssets();
                        this.fetchAssetLogs(); // Cập nhật Audit Log
                        this.showAssetModal = false;
                    })
                    .catch(err => alert('Lỗi thêm tài sản: ' + (err.response?.data?.message || err.message)));
            }
        },

        /**
         * Xóa tài sản khỏi cơ sở dữ liệu
         */
        deleteAsset(maTaiSan) {
            if (confirm('Bạn có chắc chắn muốn xóa tài sản này?')) {
                axios.delete('/api/assets/' + maTaiSan)
                    .then(() => {
                        this.fetchAssets();
                        this.fetchAssetLogs(); // Cập nhật Audit Log sau khi xóa
                    })
                    .catch(err => alert('Lỗi xóa tài sản: ' + (err.response?.data?.message || err.message)));
            }
        }
    };
}
