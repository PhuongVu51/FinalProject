# FinalProject

# 🐝 Teacher Bee - Hệ Thống Quản Lý Học Tập (CourseMS)

**Teacher Bee** là một hệ thống quản lý học tập (LMS/Course Management System) nhẹ, hiện đại và dễ sử dụng. Hệ thống giúp kết nối Admin, Giáo viên và Học sinh, hỗ trợ quy trình quản lý lớp học, thi cử và chấm điểm một cách hiệu quả.

<img width="2880" height="1560" alt="image" src="https://github.com/user-attachments/assets/0ea86fd8-df42-4399-bf50-ebb5896e5f02" />


---

## 🚀 Tính Năng Nổi Bật

### 1. Phân Quyền (Role-Based Access Control)
Hệ thống chia làm 3 vai trò riêng biệt với giao diện và chức năng khác nhau:

* **👨‍💼 Admin (Quản Trị Viên):**
    * **Dashboard:** Xem thống kê tổng quan (số lượng HS, GV, Lớp, Bài thi) và biểu đồ học lực.
    * **Quản lý Lớp học:** Thêm, sửa, xóa lớp; Phân công giáo viên chủ nhiệm.
    * **Quản lý Giáo viên:** Thêm mới giáo viên, cấp tài khoản tự động.
    * **Quản lý Học sinh:** Thêm mới học sinh, quản lý thông tin.
    * **Duyệt đơn:** Phê duyệt hoặc từ chối đơn xin vào lớp của học sinh.
    * **Tin tức:** Đăng thông báo, tin tức chung cho toàn trường.

* **👩‍🏫 Teacher (Giáo Viên):**
    * **Quản lý Bài thi:** Tạo bài kiểm tra cho các lớp mình phụ trách.
    * **Chấm điểm:** Nhập điểm và nhận xét cho từng học sinh.
    * **Dashboard:** Xem thống kê liên quan đến việc giảng dạy.

* **👨‍🎓 Student (Học Sinh):**
    * **Đăng ký lớp:** Xem danh sách lớp mở và gửi đơn đăng ký.
    * **Xem điểm:** Tra cứu kết quả học tập, xem nhận xét của giáo viên.
    * **Tin tức:** Cập nhật thông báo từ nhà trường.

### 2. Giao Diện & Trải Nghiệm (UI/UX)
* **Modern Dashboard:** Thiết kế theo phong cách hiện đại, sạch sẽ.
* **Glassmorphism:** Hiệu ứng kính mờ sang trọng ở trang đăng nhập.
* **Responsive:** Hiển thị tốt trên máy tính.
* **Cookie Login:** Tính năng "Ghi nhớ đăng nhập" (Remember Me) trong 30 ngày.

---

## 🛠 Công Nghệ Sử Dụng

* **Backend:** PHP (Native/Procedural)
* **Database:** MySQL (MariaDB)
* **Frontend:** HTML5, CSS3 (Custom Dashboard Style), FontAwesome 6 (Icons)
* **Charts:** Chart.js (Biểu đồ thống kê)
* **Server:** XAMPP / Apache

---

## ⚙️ Hướng Dẫn Cài Đặt

### Bước 1: Chuẩn bị môi trường
1.  Cài đặt **XAMPP** (hoặc WAMP/MAMP).
2.  Khởi động **Apache** và **MySQL** trong XAMPP Control Panel.

### Bước 2: Cài đặt mã nguồn
1.  Tải source code về.
2.  Giải nén và copy thư mục dự án vào đường dẫn: `C:\xampp\htdocs\course-ms` (hoặc tên thư mục bạn muốn).

### Bước 3: Cấu hình Cơ sở dữ liệu
1.  Truy cập **phpMyAdmin** (thường là `http://localhost/phpmyadmin`).
2.  Tạo một database mới tên là: `teacher_bee_db`.
3.  Nhấn vào tab **Import** (Nhập), chọn file `db_setup.sql` (hoặc file SQL mới nhất bạn có) và nhấn **Go**.

### Bước 4: Kiểm tra kết nối
1.  Mở file `connection.php` trong code.
2.  Đảm bảo thông tin cấu hình đúng với máy của bạn:
    ```php
    $link = mysqli_connect("127.0.0.1", "root", "", "teacher_bee_db", 3306);
    // Lưu ý: Nếu dùng MAMP trên Mac, port có thể là 8889. Nếu đổi port XAMPP, hãy sửa số 3306.
    ```

### Bước 5: Chạy dự án
Mở trình duyệt và truy cập: `http://localhost/course-ms/`

---

## 🔐 Tài Khoản Truy Cập Mẫu

Dữ liệu mẫu đã được nạp sẵn để bạn kiểm tra (Mật khẩu chung: **123456**):

| Vai Trò | Username / Email | Mật khẩu |
| :--- | :--- | :--- |
| **Admin** | `admin` | `123456` |
| **Giáo Viên** | `teacher1@bee.com` | `123456` |
| **Giáo Viên** | `mp@lms.com` | `123456` |
| **Học Sinh** | `student1` | `123456` |
| **Học Sinh** | `123123` | `123456` |

---

## 📂 Cấu Trúc Thư Mục
course-ms/ ├── includes/ # Các phần giao diện chung │ ├── sidebar.php # Menu trái (tự động đổi theo role) │ ├── topbar.php # Thanh tiêu đề trên cùng │ └── footer.php # Chân trang ├── css/ │ ├── dashboard_style.css # Style chính cho trang quản trị │ └── style.css # Style cho trang Login/Register ├── connection.php # Kết nối Database ├── auth.php # Hàm kiểm tra quyền & Cookie ├── login.php # Trang đăng nhập ├── register.php # Trang đăng ký GV ├── home.php # Dashboard Admin/GV ├── student_home.php # Dashboard Học sinh ├── manage_*.php # Các file quản lý (Lớp, GV, HS, Tin tức...) └── ...

---

## 📝 Ghi Chú
* Để hiển thị icon, máy tính cần có kết nối Internet để tải thư viện **FontAwesome CDN**.
* Mật khẩu trong database được mã hóa bằng `MD5`.

---
*Developed with ❤️ by Group 1*
