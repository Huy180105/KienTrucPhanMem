// Auth Component Logic
import axios from 'axios';

export function authComponent() {
    return {
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

        loginForm() {
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
            };
        }
    };
}
