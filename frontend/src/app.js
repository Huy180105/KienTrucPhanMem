import Alpine from 'alpinejs';
import axios from 'axios';
import { helpers } from './utils/helpers';
import { dashboardComponent } from './components/dashboard';
import { roomsComponent } from './components/rooms';
import { tenantsComponent } from './components/tenants';
import { contractsComponent } from './components/contracts';
import { invoicesComponent } from './components/invoices';
import { assetsComponent } from './components/assets';
import { authComponent } from './components/auth';

window.Alpine = Alpine;
window.axios = axios;

// Configure Axios defaults
axios.defaults.withCredentials = true;

// Setup Alpine JS App
window.rentalApp = function() {
    return {
        // Shared states between components
        currentUser: null,
        userRole: 'admin',
        activeTab: 'dashboard',
        tabTitles: {
            dashboard: 'Bảng Điều Khiển Tổng Quan',
            rooms: 'Quản Lý Phòng Trọ',
            tenants: 'Danh Sách Khách Thuê',
            contracts: 'Quản Lý Hợp Đồng',
            invoices: 'Danh Sách Hóa Đơn',
            assets: 'Quản Lý Tài Sản'
        },
        
        searchQuery: '',
        roomFilter: 'all',
        contractFilter: 'all',
        invoiceFilter: 'all',
        assetRoomFilter: 'all',

        selectedRoom: null,
        sendingEmails: [],

        rooms: [],
        tenants: [],
        contracts: [],
        invoices: [],
        assets: [],
        assetLogs: [],

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

        // Spread all modular behaviors into single object
        ...helpers,
        ...dashboardComponent(),
        ...roomsComponent(),
        ...tenantsComponent(),
        ...contractsComponent(),
        ...invoicesComponent(),
        ...assetsComponent(),
        ...authComponent(),

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

                        // Call icon creation on initial load
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
        }
    };
};

// Bind loginForm to window for login page compatibility
window.loginForm = authComponent().loginForm;

Alpine.start();
