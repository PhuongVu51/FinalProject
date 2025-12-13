<?php
/**
 * SCRIPT SỬA ĐƯỜNG DẪN SAU KHI MIGRATION
 * Chạy file này NGAY SAU KHI chạy migrate_structure.php
 * 
 * CÁCH DÙNG:
 * 1. Đặt file này vào thư mục gốc course-ms/
 * 2. Truy cập: http://localhost/FinalProject/course-ms/course-ms/fix_paths.php
 */

echo "<pre>";
echo "🔧 BẮT ĐẦU SỬA ĐƯỜNG DẪN...\n";
echo str_repeat("=", 60) . "\n\n";

$baseDir = __DIR__;

// ============================================================
// BƯỚC 1: SỬA LOGIN.PHP - REDIRECT ĐÚNG ROLE
// ============================================================
echo "📝 Bước 1: Sửa login.php redirects...\n";

$loginFile = $baseDir . '/public/login.php';
if (file_exists($loginFile)) {
    $content = file_get_contents($loginFile);
    
    // Tìm và thay thế redirect cho student
    $content = preg_replace(
        '/\$redirect\s*=\s*\(\$_SESSION\[\'role\'\]\s*==\s*\'student\'\)\s*\?\s*[\'"].*?[\'"]\s*:\s*[\'"].*?[\'"];/',
        '$redirect = ($_SESSION[\'role\'] == \'student\') ? \'../student/student_home.php\' : (($_SESSION[\'role\'] == \'teacher\') ? \'../teacher/home.php\' : \'../admin/home.php\');',
        $content
    );
    
    // Thay thế các header location cũ
    $content = str_replace('header("Location: student_home.php")', 'header("Location: ../student/student_home.php")', $content);
    $content = str_replace('header("Location: home.php")', 'header("Location: ../admin/home.php")', $content);
    $content = str_replace('header("Location: $redirect")', 'header("Location: $redirect")', $content);
    
    file_put_contents($loginFile, $content);
    echo "   ✅ Đã sửa: public/login.php\n";
}

// ============================================================
// BƯỚC 2: SỬA AUTH.PHP - REDIRECT VỀ LOGIN
// ============================================================
echo "\n📝 Bước 2: Sửa auth.php...\n";

$authFile = $baseDir . '/config/auth.php';
if (file_exists($authFile)) {
    $content = file_get_contents($authFile);
    
    // Sửa checkLogin redirect
    $content = preg_replace(
        '/header\("Location:\s*login\.php"\)/',
        'header("Location: /FinalProject/course-ms/course-ms/public/login.php")',
        $content
    );
    
    // Sửa requireRole redirect
    $content = preg_replace(
        '/if\s*\(!checkLogin\(\$link\)\)\s*{\s*header\("Location:\s*[^"]+"\)/',
        'if (!checkLogin($link)) { header("Location: /FinalProject/course-ms/course-ms/public/login.php")',
        $content
    );
    
    // Sửa logout link trong die message
    $content = str_replace(
        '<a href=\'logout.php\'>',
        '<a href=\'/FinalProject/course-ms/course-ms/public/logout.php\'>',
        $content
    );
    
    file_put_contents($authFile, $content);
    echo "   ✅ Đã sửa: config/auth.php\n";
}

// ============================================================
// BƯỚC 3: SỬA LOGOUT.PHP
// ============================================================
echo "\n📝 Bước 3: Sửa logout.php...\n";

$logoutFile = $baseDir . '/public/logout.php';
if (file_exists($logoutFile)) {
    $content = file_get_contents($logoutFile);
    $content = str_replace("header('location:login.php')", "header('Location: login.php')", $content);
    file_put_contents($logoutFile, $content);
    echo "   ✅ Đã sửa: public/logout.php\n";
}

// ============================================================
// BƯỚC 4: SỬA CÁC SIDEBAR - LINKS ĐIỀU HƯỚNG
// ============================================================
echo "\n📝 Bước 4: Sửa sidebar links...\n";

// Admin Sidebar
$adminSidebar = $baseDir . '/admin/includes/sidebar.php';
if (file_exists($adminSidebar)) {
    $content = file_get_contents($adminSidebar);
    
    // Sửa logout link
    $content = str_replace(
        'href="logout.php"',
        'href="../public/logout.php"',
        $content
    );
    
    file_put_contents($adminSidebar, $content);
    echo "   ✅ Đã sửa: admin/includes/sidebar.php\n";
}

// Teacher Sidebar
$teacherSidebar = $baseDir . '/teacher/includes/sidebar.php';
if (file_exists($teacherSidebar)) {
    $content = file_get_contents($teacherSidebar);
    
    // Xóa menu không cần thiết cho teacher
    $content = preg_replace(
        '/<li class="menu-label">Quản Trị<\/li>.*?<li class="menu-label">Hệ Thống<\/li>/s',
        '<li class="menu-label">Giảng Dạy</li>',
        $content
    );
    
    // Sửa logout link
    $content = str_replace(
        'href="logout.php"',
        'href="../public/logout.php"',
        $content
    );
    
    file_put_contents($teacherSidebar, $content);
    echo "   ✅ Đã sửa: teacher/includes/sidebar.php\n";
}

// Student Sidebar
$studentSidebar = $baseDir . '/student/includes/sidebar.php';
if (file_exists($studentSidebar)) {
    $content = file_get_contents($studentSidebar);
    
    // Xóa toàn bộ menu admin/teacher, chỉ giữ menu student
    $newContent = '<?php 
$cp = basename($_SERVER[\'PHP_SELF\']); 
$role = $_SESSION[\'role\'] ?? \'\';
?>
<aside class="sidebar">
    <div class="brand">
        <i class="fa-solid fa-bee"></i> TeacherBee
    </div>
    <ul class="menu-list">
        <li class="menu-label">Học Tập</li>
        <li><a href="student_home.php" class="menu-link <?php echo ($cp==\'student_home.php\')?\'active\':\'\'; ?>"><i class="fa-solid fa-house"></i> Trang Chủ</a></li>
        <li><a href="student_classes.php" class="menu-link <?php echo ($cp==\'student_classes.php\')?\'active\':\'\'; ?>"><i class="fa-solid fa-chalkboard-user"></i> Lớp Học</a></li>
        <li><a href="student_dashboard.php" class="menu-link <?php echo ($cp==\'student_dashboard.php\')?\'active\':\'\'; ?>"><i class="fa-solid fa-star"></i> Xem Điểm</a></li>
    </ul>
    <div class="sidebar-footer">
        <a href="../public/logout.php" class="menu-link" style="color:#EF4444; justify-content:center; background:#FEF2F2;">
            <i class="fa-solid fa-arrow-right-from-bracket"></i> Đăng Xuất
        </a>
    </div>
</aside>';
    
    file_put_contents($studentSidebar, $newContent);
    echo "   ✅ Đã sửa: student/includes/sidebar.php\n";
}

// ============================================================
// BƯỚC 5: SỬA CÁC FILE ADMIN
// ============================================================
echo "\n📝 Bước 5: Sửa file admin...\n";

$adminFiles = [
    'manage_classes.php',
    'edit_class.php',
    'manage_teachers.php',
    'manage_students.php',
    'edit_student.php',
    'delete_student.php',
    'manage_applications.php',
    'manage_news.php',
    'home.php'
];

foreach ($adminFiles as $file) {
    $filePath = $baseDir . '/admin/' . $file;
    if (!file_exists($filePath)) continue;
    
    $content = file_get_contents($filePath);
    
    // Sửa các header location nội bộ admin
    $content = preg_replace(
        '/header\("Location:\s*([a-z_]+\.php)"\)/',
        'header("Location: $1")',
        $content
    );
    
    // Sửa delete confirm links
    $content = preg_replace(
        '/href="\?del=/',
        'href="?del=',
        $content
    );
    
    file_put_contents($filePath, $content);
    echo "   ✅ Đã sửa: admin/$file\n";
}

// ============================================================
// BƯỚC 6: SỬA CÁC FILE TEACHER
// ============================================================
echo "\n📝 Bước 6: Sửa file teacher...\n";

$teacherFiles = [
    'manage_exams.php',
    'edit_exam.php',
    'delete_exam.php',
    'enter_scores.php',
    'home.php'
];

foreach ($teacherFiles as $file) {
    $filePath = $baseDir . '/teacher/' . $file;
    if (!file_exists($filePath)) continue;
    
    $content = file_get_contents($filePath);
    
    // Sửa các header location nội bộ teacher
    $content = preg_replace(
        '/header\("Location:\s*manage_exams\.php"\)/',
        'header("Location: manage_exams.php")',
        $content
    );
    
    file_put_contents($filePath, $content);
    echo "   ✅ Đã sửa: teacher/$file\n";
}

// ============================================================
// BƯỚC 7: SỬA CÁC FILE STUDENT
// ============================================================
echo "\n📝 Bước 7: Sửa file student...\n";

$studentFiles = [
    'student_home.php',
    'student_classes.php',
    'student_dashboard.php'
];

foreach ($studentFiles as $file) {
    $filePath = $baseDir . '/student/' . $file;
    if (!file_exists($filePath)) continue;
    
    $content = file_get_contents($filePath);
    
    // Sửa các header location nội bộ student
    $content = preg_replace(
        '/header\("Location:\s*student_classes\.php"\)/',
        'header("Location: student_classes.php")',
        $content
    );
    
    file_put_contents($filePath, $content);
    echo "   ✅ Đã sửa: student/$file\n";
}

// ============================================================
// BƯỚC 8: SỬA REGISTER.PHP
// ============================================================
echo "\n📝 Bước 8: Sửa register.php...\n";

$registerFile = $baseDir . '/public/register.php';
if (file_exists($registerFile)) {
    $content = file_get_contents($registerFile);
    
    // Sửa redirect sau khi đăng ký thành công
    $content = str_replace(
        'window.location=\'login.php\'',
        'window.location=\'login.php\'',
        $content
    );
    
    file_put_contents($registerFile, $content);
    echo "   ✅ Đã sửa: public/register.php\n";
}

// ============================================================
// BƯỚC 9: TẠO INDEX.PHP REDIRECT TỰ ĐỘNG
// ============================================================
echo "\n📝 Bước 9: Tạo index.php redirect...\n";

$rootIndex = '<?php
// Auto redirect to public/index.php
header("Location: public/index.php");
exit();
?>';

file_put_contents($baseDir . '/index.php', $rootIndex);
echo "   ✅ Đã tạo: index.php (root redirect)\n";

// ============================================================
// BƯỚC 10: TẠO FILE HƯỚNG DẪN
// ============================================================
echo "\n📝 Bước 10: Tạo file hướng dẫn...\n";

$guide = "# ĐƯỜNG DẪN TRUY CẬP SAU KHI SỬA

## 🌐 Các URL cần nhớ:

### Trang công khai:
- Trang chủ: http://localhost/FinalProject/course-ms/course-ms/public/index.php
- Đăng nhập: http://localhost/FinalProject/course-ms/course-ms/public/login.php
- Đăng ký: http://localhost/FinalProject/course-ms/course-ms/public/register.php

### Dashboard (sau khi login):
- Admin: http://localhost/FinalProject/course-ms/course-ms/admin/home.php
- Teacher: http://localhost/FinalProject/course-ms/course-ms/teacher/home.php
- Student: http://localhost/FinalProject/course-ms/course-ms/student/student_home.php

## ✅ Testing Checklist:

1. [ ] Truy cập trang login
2. [ ] Đăng nhập bằng tài khoản Admin (mt@lms.com / 123456)
3. [ ] Kiểm tra menu sidebar hiển thị đúng
4. [ ] Thử các chức năng: Quản lý lớp, giáo viên, học sinh
5. [ ] Đăng xuất và login lại bằng Teacher (mp@lms.com / 123456)
6. [ ] Kiểm tra tạo bài thi, nhập điểm
7. [ ] Đăng xuất và login lại bằng Student (251267 / 123456)
8. [ ] Kiểm tra xem điểm, đăng ký lớp

## 🐛 Nếu còn lỗi:

1. Kiểm tra Console (F12) xem lỗi CSS/JS gì
2. Kiểm tra đường dẫn include trong từng file
3. Đảm bảo XAMPP Apache đang chạy
4. Clear cache trình duyệt (Ctrl + Shift + Del)

## 📧 Tài khoản test:

| Role    | Username       | Password |
|---------|----------------|----------|
| Admin   | mt@lms.com     | 123456   |
| Teacher | mp@lms.com     | 123456   |
| Student | 251267         | 123456   |

";

file_put_contents($baseDir . '/FIXED_PATHS_GUIDE.md', $guide);
echo "   ✅ Đã tạo: FIXED_PATHS_GUIDE.md\n";

// ============================================================
// HOÀN THÀNH
// ============================================================
echo "\n" . str_repeat("=", 60) . "\n";
echo "✅ ĐÃ SỬA XONG TẤT CẢ ĐƯỜNG DẪN!\n\n";

echo "🎯 BẮT ĐẦU TESTING:\n";
echo "1. Truy cập: http://localhost/FinalProject/course-ms/course-ms/public/login.php\n";
echo "2. Đăng nhập bằng:\n";
echo "   - Admin: mt@lms.com / 123456\n";
echo "   - Teacher: mp@lms.com / 123456\n";
echo "   - Student: 251267 / 123456\n\n";

echo "📖 Xem chi tiết: FIXED_PATHS_GUIDE.md\n";
echo str_repeat("=", 60) . "\n";
echo "</pre>";
?>