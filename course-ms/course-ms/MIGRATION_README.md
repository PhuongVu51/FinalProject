# Teacher Bee - Cấu trúc mới

## Đường dẫn truy cập:

- **Trang chủ**: http://localhost/course-ms/public/
- **Login**: http://localhost/course-ms/public/login.php
- **Admin Dashboard**: http://localhost/course-ms/admin/home.php
- **Teacher Dashboard**: http://localhost/course-ms/teacher/home.php
- **Student Dashboard**: http://localhost/course-ms/student/student_home.php

## Cấu trúc:

```
course-ms/
├── config/          # Cấu hình chung (DB, Auth)
├── shared/          # Tài nguyên dùng chung (CSS, JS)
├── admin/           # Khu vực Admin
├── teacher/         # Khu vực Teacher
├── student/         # Khu vực Student
├── public/          # Trang công khai (Login, Register)
└── _backup/         # Backup code cũ
```

## Lưu ý:

1. Code cũ đã được backup tại: _backup/
2. File gốc vẫn còn ở thư mục root (chưa xóa)
3. Kiểm tra kỹ các chức năng trước khi xóa file cũ
4. Database không thay đổi, chỉ cấu trúc folder thay đổi

## Testing Checklist:

- [ ] Admin login → Dashboard
- [ ] Teacher login → Dashboard → Tạo bài thi
- [ ] Student login → Dashboard → Xem điểm
- [ ] CSS hiển thị đúng
- [ ] Các link chuyển trang hoạt động
- [ ] Upload file (nếu có)

Generated: 2025-12-13 19:29:57
