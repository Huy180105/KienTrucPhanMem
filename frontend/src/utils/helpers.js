// Utility Helper Functions
export const helpers = {
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
    }
};
