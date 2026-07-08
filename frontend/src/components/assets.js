// Assets Component Logic
import axios from 'axios';

export function assetsComponent() {
    return {
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

        getRoomAssets(maPhong) {
            return this.assets.filter(a => a.maPhong === maPhong);
        },

        // --- ASSETS CRUD ---
        openAddAsset() {
            this.isEditingAsset = false;
            this.assetForm = { maTaiSan: null, tenTaiSan: '', soLuong: 1, tinhTrang: 'Tốt', maPhong: '' };
            this.showAssetModal = true;
            this.$nextTick(() => {
                if (window.lucide) window.lucide.createIcons();
            });
        },

        openAddAssetForRoom(roomId) {
            this.openAddAsset();
            this.assetForm.maPhong = roomId;
        },

        openEditAsset(asset) {
            this.isEditingAsset = true;
            this.assetForm = { ...asset };
            this.showAssetModal = true;
            this.$nextTick(() => {
                if (window.lucide) window.lucide.createIcons();
            });
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
    };
}
