// Dashboard Component Logic
export function dashboardComponent() {
    return {
        // Statistics computations
        rentedRoomsCount() { 
            return this.rooms.filter(r => r.trangThai === 'Đã thuê').length; 
        },
        vacantRoomsCount() { 
            return this.rooms.filter(r => r.trangThai === 'Trống').length; 
        },
        maintenanceRoomsCount() { 
            return this.rooms.filter(r => r.trangThai === 'Đang bảo trì').length; 
        },
        activeContractsCount() { 
            return this.contracts.filter(c => c.trangThai === 'Đang hiệu lực').length; 
        },
        unpaidInvoicesCount() { 
            return this.invoices.filter(i => i.trangThai === 'Chưa thanh toán').length; 
        },
        unpaidInvoicesSum() { 
            return this.invoices.filter(i => i.trangThai === 'Chưa thanh toán')
                               .reduce((sum, item) => sum + item.tongTien, 0); 
        },
        totalRevenue() { 
            return this.invoices.filter(i => i.trangThai === 'Đã thanh toán')
                               .reduce((sum, item) => sum + item.tongTien, 0); 
        }
    };
}
