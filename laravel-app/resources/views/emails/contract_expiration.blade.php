<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thông Báo Sắp Hết Hạn Hợp Đồng</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            background-color: #f8fafc;
            margin: 0;
            padding: 20px;
            color: #334155;
        }
        .container {
            max-width: 600px;
            background-color: #ffffff;
            margin: 0 auto;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -2px rgba(0, 0, 0, 0.1);
            border: 1px solid #e2e8f0;
        }
        .header {
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            color: #ffffff;
            padding: 30px 20px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 22px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .content {
            padding: 30px 25px;
        }
        .welcome {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 20px;
            color: #1e293b;
        }
        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            background-color: #f8fafc;
            border-radius: 12px;
            overflow: hidden;
            border: 1px solid #f1f5f9;
        }
        .details-table td {
            padding: 14px 18px;
            font-size: 14px;
            border-bottom: 1px solid #f1f5f9;
        }
        .details-table td.label {
            font-weight: 600;
            color: #64748b;
            width: 40%;
        }
        .details-table td.value {
            font-weight: 700;
            color: #0f172a;
            text-align: right;
        }
        .warning-box {
            background-color: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 15px;
            margin: 25px 0;
            border-radius: 0 8px 8px 0;
            font-size: 13.5px;
            line-height: 1.6;
            color: #78350f;
        }
        .footer {
            background-color: #f1f5f9;
            text-align: center;
            padding: 20px;
            font-size: 12px;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h1>Thông Báo Hợp Đồng Sắp Hết Hạn</h1>
    </div>
    
    <div class="content">
        <div class="welcome">Kính gửi anh/chị: {{ $tenantData['hoTen'] }},</div>
        
        <p style="font-size: 14px; line-height: 1.6; color: #475569; margin: 0 0 20px 0;">
            Ban quản lý nhà trọ xin trân trọng thông báo hợp đồng thuê phòng của anh/chị sắp hết thời hạn hiệu lực. Dưới đây là thông tin chi tiết:
        </p>

        <table class="details-table">
            <tr>
                <td class="label">Mã hợp đồng:</td>
                <td class="value">HĐ{{ $contractData['maHopDong'] }}</td>
            </tr>
            <tr>
                <td class="label">Phòng thuê:</td>
                <td class="value" style="color: #4f46e5;">{{ $contractData['maPhong'] }}</td>
            </tr>
            <tr>
                <td class="label">Ngày kết thúc:</td>
                <td class="value">{{ $contractData['ngayKetThucFormatted'] ?? $contractData['ngayKetThuc'] }}</td>
            </tr>
            <tr>
                <td class="label">Giá thuê tháng:</td>
                <td class="value">{{ number_format($contractData['giaThueThang'], 0, ',', '.') }} VND</td>
            </tr>
            <tr>
                <td class="label">Tiền đặt cọc:</td>
                <td class="value" style="color: #10b981;">{{ number_format($contractData['tienCoc'], 0, ',', '.') }} VND</td>
            </tr>
        </table>

        <div class="warning-box">
            <strong>LƯU Ý QUAN TRỌNG:</strong><br>
            - Nếu anh/chị có nhu cầu <strong>gia hạn tiếp tục thuê phòng</strong>, vui lòng liên hệ Ban quản lý trước 3 ngày để làm thủ tục gia hạn.<br>
            - Nếu anh/chị <strong>muốn trả phòng</strong>, vui lòng dọn dẹp phòng sạch sẽ và bàn giao lại đầy đủ tài sản để nhận lại tiền đặt cọc.
        </div>

        <p style="font-size: 13px; color: #64748b; margin: 20px 0 0 0;">
            Cảm ơn quý khách đã tin tưởng và đồng hành cùng chúng tôi!
        </p>
    </div>

    <div class="footer">
        Hệ Thống Nhà Trọ Antigravity © 2026 - Email tự động gửi từ hệ thống
    </div>
</div>

</body>
</html>
