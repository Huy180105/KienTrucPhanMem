# Hệ Thống Quản Lý Phòng Trọ - Kiến Trúc Phần Mềm

Dự án môn học **Kiến trúc phần mềm** xây dựng ứng dụng Quản lý Phòng trọ chuyên nghiệp. Hệ thống được phát triển trên nền tảng **PHP Laravel 12** kết hợp **MySQL**, vận hành hoàn toàn trên **Docker (WSL 2)**. Dự án tuân thủ nghiêm ngặt **Kiến trúc phân tầng (Layered Architecture)** và cài đặt **6 mẫu thiết kế (Design Patterns)** trong code thực tế.

---

## 1. Thành Phần & Địa Chỉ Truy Cập Dịch Vụ

Khi hệ thống vận hành, các dịch vụ hoạt động tại các địa chỉ sau:

### **🚀 Địa Chỉ Truy Cập Trực Tuyến (Bản Deploy Online):**
*   **🌐 Giao diện Web chính thức (Railway Cloud 24/7):** [https://kientrucphanmem.up.railway.app](https://kientrucphanmem.up.railway.app)
*   **🌐 Giao diện Web chia sẻ tạm thời (Ngrok Tunnel):** [https://upcountry-startle-unselfish.ngrok-free.dev](https://upcountry-startle-unselfish.ngrok-free.dev)

### **💻 Địa Chỉ Triển Khai Cục Bộ (Localhost Docker):**
*   **🌐 Giao diện Web local:** [http://localhost:8880](http://localhost:8880)
*   **🗃️ phpMyAdmin local:** [http://localhost:8881](http://localhost:8881)
    *   *Tài khoản:* Username: `sail` | Password: `password`
*   **✉️ Mailpit local (Hòm thư ảo gửi mail nhắc hợp đồng):** [http://localhost:8025](http://localhost:8025)
*   **💾 Cổng MySQL local (Kết nối ngoài):** `localhost:8882`

---

## 2. Hướng Dẫn Cài Đặt Hệ Thống Từ Đầu (Chạy Local)

### **Bước 1: Cài đặt Linux cho Windows (WSL 2)**
Docker Desktop trên Windows yêu cầu WSL 2 làm backend để tối ưu hóa hiệu năng:
1. Mở **PowerShell** bằng quyền Administrator (Run as Administrator).
2. Chạy lệnh cài đặt WSL:
   ```powershell
   wsl --install
   ```
3. Sau khi cài đặt hoàn tất, hãy **khởi động lại máy tính**.
4. Khi máy tính khởi động lại, một cửa sổ Ubuntu sẽ tự động hiện lên, bạn chỉ cần nhập *Username* và *Password* tùy ý để hoàn tất thiết lập Linux.

### **Bước 2: Cài đặt Docker Desktop**
1. Tải bộ cài đặt **Docker Desktop** cho Windows tại: [Docker Official Website](https://www.docker.com/products/docker-desktop/).
2. Chạy file cài đặt, tích chọn **"Use WSL 2 instead of Hyper-V"** (đây là lựa chọn mặc định và khuyên dùng).
3. Đợi quá trình cài đặt hoàn thành và bấm **Close and Restart**.
4. Mở Docker Desktop lên và chấp nhận các điều khoản sử dụng.

### **Bước 3: Clone Mã Nguồn Dự Án**
Mở PowerShell hoặc Git Bash và chạy lệnh sau để tải mã nguồn từ GitHub về máy:
```powershell
git clone https://github.com/Huy180105/KienTrucPhanMem.git
cd KienTrucPhanMem/laravel-app
```

### **Bước 4: Cấu Hình Môi Trường (.env)**
Tạo tệp cấu hình môi trường `.env` từ file mẫu:
```powershell
Copy-Item .env.example .env
```

---

## 3. Các Lệnh PowerShell Vận Hành Hệ Thống (Local)

*Các lệnh dưới đây phải được chạy tại thư mục `KienTrucPhanMem/laravel-app`:*

### **1. Khởi động các container Docker (Chạy ngầm - Detached mode)**
```powershell
docker compose up -d
```
*(Lần đầu chạy lệnh này sẽ mất khoảng 3-5 phút để Docker tự động tải hình ảnh ứng dụng và thiết lập hệ thống mạng nội bộ).*

### **2. Dừng toàn bộ hệ thống container**
```powershell
docker compose down
```

### **3. Dựng lại Cơ sở dữ liệu và nạp dữ liệu mẫu (Seeding)**
Khi muốn thiết lập lại toàn bộ bảng CSDL MySQL và nạp sẵn dữ liệu mẫu tiếng Việt vào phòng trọ, khách thuê, hợp đồng, hóa đơn:
```powershell
docker compose exec laravel.test php artisan migrate:fresh --seed
```

---

## 4. Hướng Dẫn Kiểm Thử Bằng Postman (Nhanh & Tiện Lợi)

Dự án đã đính kèm sẵn tệp tin cấu hình Postman Collection ở thư mục gốc: **`KienTrucPhanMem.postman_collection.json`**. Các thành viên trong nhóm làm theo các bước sau để test:

1. **Import vào Postman:** Mở Postman, chọn **Import** ở góc trên bên trái, kéo thả file `KienTrucPhanMem.postman_collection.json` vào.
2. **Cấu hình URL động (Biến `base_url`):**
   * Click vào tên thư mục Collection *"Quản Lý Phòng Trọ - Kiến Trúc Phần Mềm"* vừa import ở cột trái.
   * Chuyển sang tab **Variables** ở khung giữa.
   * Tại biến `base_url`, nhập giá trị tại cột *Current Value* là địa chỉ đang chạy (ví dụ chạy offline: `http://localhost:8880` hoặc link Ngrok online dạng `https://xxx.ngrok-free.dev` / link Railway dạng `https://xxx.up.railway.app`).
   * Bấm **Save (Ctrl + S)**.
3. **Tiến hành gửi Request:** Chọn các API trong thư mục con (Phòng trọ, Khách thuê, Hợp đồng, Hóa đơn, Tài sản) và bấm **Send** để kiểm thử.

> **💡 Điểm cải tiến của API Gửi Email Nhắc Nhở:**
> API gửi mail (`POST /api/contracts/{id}/send-reminder`) đã được chuyển sang `api.php` để không bị lỗi bảo mật CSRF (419 Expired) trên Postman. Đồng thời, API này được lập trình thông minh: Nếu gửi Body trống, hệ thống sẽ tự động truy vấn MySQL lấy thông tin hợp đồng và khách thuê theo `{id}` để gửi đi, giúp việc test trên Postman cực kỳ nhanh gọn.

---

## 5. Hướng Dẫn Chia Sẻ Web & API Ra Ngoài Internet Tạm Thời (Ngrok)

Để thầy cô hoặc các thành viên khác trong nhóm có thể truy cập Web và test API trực tiếp từ điện thoại hoặc máy tính cá nhân của họ thông qua đường hầm public tạm thời:

1. **Đăng ký tài khoản Ngrok:** Truy cập [ngrok.com](https://ngrok.com/) đăng ký tài khoản miễn phí để lấy mã Authtoken.
2. **Cài đặt Token:** Mở Command Prompt hoặc PowerShell trên Windows và chạy lệnh sau (bỏ dấu ngoặc nhọn `< >` khi paste token):
   ```cmd
   ngrok config add-authtoken <YOUR_AUTHTOKEN>
   ```
3. **Mở cổng public:** Chạy lệnh sau để ánh xạ cổng Web Laravel lên internet:
   ```cmd
   ngrok http 8880
   ```
4. **Sử dụng:** Ngrok sẽ cung cấp một đường dẫn HTTPS dạng `https://xxx.ngrok-free.dev`. Chỉ cần gửi link này cho thầy cô/thành viên khác truy cập.
   * *Lưu ý:* Giữ cửa sổ dòng lệnh chạy ngrok luôn mở trong suốt quá trình demo.

---

## 6. Hướng Dẫn Triển Khai Trực Tuyến 24/7 (Railway Cloud)

Để đưa dự án lên vận hành trực tuyến vĩnh viễn không phụ thuộc vào máy tính cá nhân chạy Docker:

1. **Khởi tạo dự án:** Đăng nhập vào [railway.app](https://railway.app) bằng tài khoản GitHub, tạo New Project trỏ tới Repo `KienTrucPhanMem`.
2. **Cấu hình Root Directory:** Vào **Settings** của dịch vụ Web trên Railway ➔ chọn **Root Directory** ➔ nhập **`laravel-app`** và lưu lại.
3. **Tạo CSDL MySQL online:** Bấm **`+ Add`** ở góc phải màn hình Railway ➔ Chọn **Database** ➔ Chọn **Add MySQL**.
4. **Cấu hình biến môi trường kết nối:**
   * Chọn dịch vụ Web `KienTrucPhanMem` ➔ chọn tab **Variables** ➔ chọn **Bulk Import** (hoặc *Raw Editor*).
   * Sao chép và dán toàn bộ danh sách biến sau rồi lưu lại:
     ```env
     DB_CONNECTION=mysql
     DB_HOST=${{MySQL.MYSQLHOST}}
     DB_PORT=${{MySQL.MYSQLPORT}}
     DB_DATABASE=${{MySQL.MYSQLDATABASE}}
     DB_USERNAME=${{MySQL.MYSQLUSER}}
     DB_PASSWORD=${{MySQL.MYSQLPASSWORD}}
     APP_KEY=base64:EQnL1VARailXyzVKN9B/9bluzrxeej5rubpHrqs+Sd0=
     APP_ENV=production
     APP_DEBUG=true
     SESSION_DRIVER=database
     CACHE_STORE=database
     ```
5. **Mở cổng Public (Generate Domain):** Vào tab **Settings** của dịch vụ Web ➔ Chọn phần **Networking** ➔ Chọn **Generate Domain** để nhận địa chỉ HTTPS truy cập 24/24.

---

## 7. Kiến Trúc Phần Mềm & Thiết Kế Hệ Thống

Dự án áp dụng cấu trúc phân tầng rõ ràng giúp tăng tính bảo trì và mở rộng:

1.  **Presentation Layer (Tầng hiển thị):** Sử dụng Blade template kết hợp TailwindCSS và Alpine.js. Giao diện thực hiện các cuộc gọi AJAX thông qua thư viện Axios để trao đổi dữ liệu JSON với tầng API.
2.  **Controller Layer (Tầng điều phối):** Tiếp nhận HTTP Request từ API route, thực hiện validate dữ liệu đầu vào và chuyển tiếp công việc xuống tầng nghiệp vụ (Service).
3.  **Business/Service Layer (Tầng nghiệp vụ):** Nơi chứa toàn bộ logic xử lý tính toán của hệ thống.
4.  **Data Access Layer (Tầng dữ liệu):** Sử dụng Laravel Eloquent ORM và kết nối MySQL để thực hiện lưu trữ dữ liệu bền vững.

---

## 8. Áp Dụng 6 Mẫu Thiết Kế (Design Patterns)

### **1. Mẫu Singleton**
*   **Chi tiết:** Áp dụng cho kết nối CSDL MySQL thông qua Laravel Service Container. Kết nối chỉ được tạo một lần duy nhất trong chu kỳ request để tiết kiệm tài nguyên.

### **2. Mẫu Factory Method**
*   **Vị trí file:** `laravel-app/app/Services/DichVuFactory.php`
*   **Chi tiết:** Cung cấp phương thức tĩnh `DichVuFactory::make(string $type)` để khởi tạo động các service nghiệp vụ (`PhongTroService`, `KhachThueService`,...) mà không cần gọi `new` trực tiếp ở Controller.

### **3. Mẫu Facade**
*   **Vị trí file:** `laravel-app/app/Facades/QuanLyThueFacade.php`
*   **Chi tiết:** Đơn giản hóa quy trình lập hợp đồng phức tạp (bao gồm tạo hợp đồng mới và đồng thời cập nhật trạng thái phòng trọ sang "Đã thuê") thành một lệnh gọi duy nhất.

### **4. Mẫu Adapter**
*   **Vị trí file:** `laravel-app/app/Adapters/HoaDonRequestAdapter.php`
*   **Chi tiết:** Chuyển đổi và chuẩn hóa định dạng các trường dữ liệu đầu vào của request từ client (như `contractId`, `month`, `year`) thành các khóa thuộc tính tiếng Việt chuẩn trước khi chuyển giao cho tầng nghiệp vụ xử lý.

### **5. Mẫu Observer**
*   **Vị trí file:** `laravel-app/app/Observers/HopDongObserver.php`
*   **Chi tiết:** Đăng ký lắng nghe các sự kiện của Model `HopDong`. Khi một hợp đồng được khởi tạo mới hoặc được thanh lý, Observer sẽ tự động cập nhật trạng thái phòng trọ tương ứng sang "Đã thuê" hoặc "Trống" trong CSDL một cách hoàn toàn tự động.

### **6. Mẫu Strategy**
*   **Vị trí file:** `laravel-app/app/Strategies/TinhHoaDonStrategy.php`
*   **Chi tiết:** Cho phép hệ thống thay đổi thuật toán tính hóa đơn hàng tháng linh hoạt:
    *   `TinhHoaDonMacDinhStrategy`: Tính tiền phòng + số điện cũ/mới + số nước cũ/mới.
    *   `TinhHoaDonTreHanStrategy`: Tính tương tự như mặc định nhưng cộng thêm phí đóng muộn cố định 150.000đ.
