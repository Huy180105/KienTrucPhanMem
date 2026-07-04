<?php

namespace App\Mail;

use App\Models\HoaDon;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class HoaDonMail extends Mailable
{
    use Queueable, SerializesModels;

    public $invoice;

    public function __construct(HoaDon $invoice)
    {
        $this->invoice = $invoice;
    }

    public function build()
    {
        $tenantName = $this->invoice->hopDong->khachThue->hoTen ?? 'Khách thuê';
        $period = 'Tháng ' . $this->invoice->thang . '/' . $this->invoice->nam;

        return $this->subject("[Thông Báo Tiền Phòng] Hóa đơn $period - Phòng " . ($this->invoice->hopDong->maPhong ?? ''))
                    ->html($this->getEmailHtml());
    }

    private function getEmailHtml()
    {
        $tenant = $this->invoice->hopDong->khachThue;
        $room = $this->invoice->hopDong->phongTro;
        $contract = $this->invoice->hopDong;

        $tenantName = $tenant->hoTen ?? 'Quý khách';
        $roomName = $room->tenPhong ?? ($contract->maPhong ?? 'Không rõ');
        $period = 'Tháng ' . $this->invoice->thang . '/' . $this->invoice->nam;
        $totalAmount = number_format($this->invoice->tongTien, 0, ',', '.') . ' VNĐ';
        $baseRent = number_format($contract->giaThueThang ?? 0, 0, ',', '.') . ' VNĐ';
        $createdDate = date('d/m/Y', strtotime($this->invoice->ngayLap));
        $status = $this->invoice->trangThai;
        $statusColor = $status === 'Đã thanh toán' ? '#10b981' : '#ef4444';

        return "
        <div style=\"font-family: 'Plus Jakarta Sans', Helvetica, Arial, sans-serif; background-color: #f8fafc; padding: 40px 20px; color: #334155; line-height: 1.6;\">
            <div style=\"max-width: 600px; margin: 0 auto; bg-color: #ffffff; background: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05), 0 2px 4px -1px rgba(0,0,0,0.05); border: 1px solid #e2e8f0;\">
                
                <!-- Header -->
                <div style=\"background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%); padding: 32px; text-align: center; color: #ffffff;\">
                    <h2 style=\"margin: 0; font-size: 24px; font-weight: 800; letter-spacing: 0.5px; text-transform: uppercase;\">Hóa Đơn Tiền Phòng</h2>
                    <p style=\"margin: 4px 0 0 0; font-size: 14px; opacity: 0.85; font-weight: 500;\">Kỳ thanh toán: $period</p>
                </div>
                
                <!-- Body -->
                <div style=\"padding: 32px;\">
                    <p style=\"margin-top: 0; font-size: 16px; font-weight: 600; color: #1e293b;\">Kính gửi anh/chị $tenantName,</p>
                    <p style=\"font-size: 14px; color: #64748b; margin-bottom: 24px;\">Hệ thống quản lý phòng trọ xin thông báo chi tiết hóa đơn tiền phòng của anh/chị trong kỳ <strong>$period</strong> như sau:</p>
                    
                    <!-- Box Chi Tiết -->
                    <div style=\"background-color: #f8fafc; border: 1px solid #f1f5f9; border-radius: 12px; padding: 20px; margin-bottom: 24px;\">
                        <table style=\"width: 100%; border-collapse: collapse; font-size: 14px;\">
                            <tr style=\"border-b: 1px solid #e2e8f0;\">
                                <td style=\"padding: 8px 0; color: #64748b;\">Phòng trọ:</td>
                                <td style=\"padding: 8px 0; text-align: right; font-weight: 700; color: #1e293b;\">$roomName</td>
                            </tr>
                            <tr style=\"border-b: 1px solid #e2e8f0;\">
                                <td style=\"padding: 8px 0; color: #64748b;\">Ngày lập:</td>
                                <td style=\"padding: 8px 0; text-align: right; font-weight: 500; color: #1e293b;\">$createdDate</td>
                            </tr>
                            <tr style=\"border-b: 1px solid #e2e8f0;\">
                                <td style=\"padding: 8px 0; color: #64748b;\">Tiền thuê gốc:</td>
                                <td style=\"padding: 8px 0; text-align: right; font-weight: 600; color: #1e293b;\">$baseRent</td>
                            </tr>
                            <tr style=\"border-b: 1px solid #e2e8f0;\">
                                <td style=\"padding: 8px 0; color: #64748b;\">Trạng thái:</td>
                                <td style=\"padding: 8px 0; text-align: right; font-weight: 700; color: $statusColor;\">$status</td>
                            </tr>
                            <tr>
                                <td style=\"padding: 12px 0 0 0; color: #1e293b; font-weight: 800; font-size: 16px;\">Tổng cộng:</td>
                                <td style=\"padding: 12px 0 0 0; text-align: right; font-weight: 800; font-size: 18px; color: #4f46e5;\">$totalAmount</td>
                            </tr>
                        </table>
                    </div>

                    <p style=\"font-size: 13px; color: #94a3b8; font-style: italic; margin-bottom: 24px;\">Lưu ý: Vui lòng thanh toán hóa đơn sớm để đảm bảo các quyền lợi dịch vụ. Bỏ qua thư này nếu anh/chị đã hoàn tất thanh toán.</p>
                    
                    <div style=\"text-align: center;\">
                        <a href=\"#\" style=\"display: inline-block; background: #4f46e5; color: #ffffff; padding: 12px 32px; border-radius: 8px; font-weight: 700; text-decoration: none; font-size: 14px; box-shadow: 0 4px 6px -1px rgba(79, 70, 229, 0.2);\">Chi Tiết Hệ Thống</a>
                    </div>
                </div>

                <!-- Footer -->
                <div style=\"background-color: #f1f5f9; padding: 24px; text-align: center; font-size: 12px; color: #94a3b8; border-top: 1px solid #e2e8f0;\">
                    <p style=\"margin: 0;\">Hệ Thống Quản Lý Phòng Trọ Tự Động</p>
                    <p style=\"margin: 4px 0 0 0;\">Địa chỉ: Khu công nghệ cao, TP. Hồ Chí Minh</p>
                </div>
            </div>
        </div>
        ";
    }
}
