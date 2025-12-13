<?php
/**
 * SCRIPT TỰ ĐỘNG TỔ CHỨC LẠI CẤU TRÚC THƯ MỤC
 * Teacher Bee - Course Management System
 * 
 * HƯỚNG DẪN SỬ DỤNG:
 * 1. Đặt file này vào thư mục gốc course-ms/
 * 2. Chạy: php migrate_structure.php
 * 3. Hoặc truy cập qua browser: http://localhost/course-ms/migrate_structure.php
 */

// Ngăn chặn chạy nhiều lần
if (file_exists('migration_completed.lock')) {
    die("❌ Migration đã được chạy rồi! Xóa file 'migration_completed.lock' nếu muốn chạy lại.\n");
}

echo "🚀 BẮT ĐẦU MIGRATION - TEACHER BEE\n";
echo str_repeat("=", 60) . "\n\n";

// Đường dẫn gốc
$baseDir = __DIR__;

// 1. TẠO CẤU TRÚC THƯ MỤC MỚI
echo "📁 BƯỚC 1: Tạo cấu trúc thư mục mới...\n";

$directories = [
    'config',
    'shared',
    'shared/css',
    'shared/includes',
    'shared/assets',
    'admin',
    'admin/includes',
    'teacher',
    'teacher/includes',
    'student',
    'student/includes',
    'public',
    '_backup' // Thư mục backup
];

foreach ($directories as $dir) {
    $path = $baseDir . '/' . $dir;
    if (!file_exists($path)) {
        mkdir($path, 0755, true);
        echo "   ✅ Đã tạo: $dir/\n";
    } else {
        echo "   ⚠️  Đã tồn tại: $dir/\n";
    }
}
echo "\n";

// 2. BACKUP CODE CŨ
echo "💾 BƯỚC 2: Backup code hiện tại...\n";
$backupDir = $baseDir . '/_backup/' . date('Y-m-d_H-i-s');
if (!file_exists($backupDir)) {
    mkdir($backupDir, 0755, true);
}

// Danh sách file cần backup (không bao gồm thư mục mới tạo)
$filesToBackup = glob($baseDir . '/*.{php,css}', GLOB_BRACE);
$includesBackup = glob($baseDir . '/includes/*.php');
$filesToBackup = array_merge($filesToBackup, $includesBackup);

foreach ($filesToBackup as $file) {
    $filename = basename($file);
    if (is_dir($file) || $filename == basename(__FILE__)) continue;
    
    copy($file, $backupDir . '/' . $filename);
    echo "   ✅ Backup: $filename\n";
}
echo "   📦 Backup hoàn tất tại: _backup/" . basename($backupDir) . "/\n\n";

// 3. DI CHUYỂN FILE
echo "🔄 BƯỚC 3: Di chuyển file đến thư mục mới...\n";

$fileMapping = [
    // Config files
    'connection.php' => 'config/connection.php',
    'auth.php' => 'config/auth.php',
    
    // Shared CSS
    'dashboard_style.css' => 'shared/css/dashboard_style.css',
    'style.css' => 'shared/css/style.css',
    
    // Shared includes
    'includes/footer.php' => 'shared/includes/footer.php',
    
    // Admin files
    'manage_classes.php' => 'admin/manage_classes.php',
    'edit_class.php' => 'admin/edit_class.php',
    'manage_teachers.php' => 'admin/manage_teachers.php',
    'manage_students.php' => 'admin/manage_students.php',
    'edit_student.php' => 'admin/edit_student.php',
    'delete_student.php' => 'admin/delete_student.php',
    'manage_applications.php' => 'admin/manage_applications.php',
    'manage_news.php' => 'admin/manage_news.php',
    'includes/sidebar.php' => 'admin/includes/sidebar.php', // Sẽ tạo 3 bản riêng
    'includes/topbar.php' => 'admin/includes/topbar.php',
    
    // Teacher files
    'manage_exams.php' => 'teacher/manage_exams.php',
    'edit_exam.php' => 'teacher/edit_exam.php',
    'delete_exam.php' => 'teacher/delete_exam.php',
    'enter_scores.php' => 'teacher/enter_scores.php',
    
    // Student files
    'student_home.php' => 'student/student_home.php',
    'student_classes.php' => 'student/student_classes.php',
    'student_dashboard.php' => 'student/student_dashboard.php',
    'student_login.php' => 'student/student_login.php',
    
    // Public files
    'index.php' => 'public/index.php',
    'login.php' => 'public/login.php',
    'register.php' => 'public/register.php',
    'logout.php' => 'public/logout.php',
    'validation.php' => 'public/validation.php',
    'registration.php' => 'public/registration.php',
];

foreach ($fileMapping as $source => $destination) {
    $sourcePath = $baseDir . '/' . $source;
    $destPath = $baseDir . '/' . $destination;
    
    if (file_exists($sourcePath)) {
        // Copy thay vì move để giữ file gốc
        copy($sourcePath, $destPath);
        echo "   ✅ Di chuyển: $source → $destination\n";
    } else {
        echo "   ⚠️  Không tìm thấy: $source\n";
    }
}

// Tạo home.php cho Admin và Teacher (copy từ home.php gốc)
if (file_exists($baseDir . '/home.php')) {
    copy($baseDir . '/home.php', $baseDir . '/admin/home.php');
    copy($baseDir . '/home.php', $baseDir . '/teacher/home.php');
    echo "   ✅ Tạo: admin/home.php\n";
    echo "   ✅ Tạo: teacher/home.php\n";
}

// Tạo 3 bản sidebar riêng
copy($baseDir . '/admin/includes/sidebar.php', $baseDir . '/teacher/includes/sidebar.php');
copy($baseDir . '/admin/includes/sidebar.php', $baseDir . '/student/includes/sidebar.php');
echo "   ✅ Tạo: teacher/includes/sidebar.php\n";
echo "   ✅ Tạo: student/includes/sidebar.php\n";

// Tạo 3 bản topbar riêng
copy($baseDir . '/admin/includes/topbar.php', $baseDir . '/teacher/includes/topbar.php');
copy($baseDir . '/admin/includes/topbar.php', $baseDir . '/student/includes/topbar.php');
echo "   ✅ Tạo: teacher/includes/topbar.php\n";
echo "   ✅ Tạo: student/includes/topbar.php\n";

echo "\n";

// 4. CẬP NHẬT ĐƯỜNG DẪN TRONG FILE
echo "🔧 BƯỚC 4: Cập nhật đường dẫn trong các file...\n";

// Hàm cập nhật đường dẫn include
function updateIncludes($filePath, $level) {
    if (!file_exists($filePath)) return;
    
    $content = file_get_contents($filePath);
    $original = $content;
    
    // Cập nhật include paths dựa trên level
    $prefix = str_repeat('../', $level);
    
    // Config includes
    $content = preg_replace(
        '/include\s+"connection\.php"/',
        'include "' . $prefix . 'config/connection.php"',
        $content
    );
    $content = preg_replace(
        '/include\s+"auth\.php"/',
        'include "' . $prefix . 'config/auth.php"',
        $content
    );
    
    // CSS includes
    $content = preg_replace(
        '/href="dashboard_style\.css"/',
        'href="' . $prefix . 'shared/css/dashboard_style.css"',
        $content
    );
    $content = preg_replace(
        '/href="style\.css"/',
        'href="' . $prefix . 'shared/css/style.css"',
        $content
    );
    
    // Sidebar/Topbar includes (giữ nguyên vì đã ở cùng folder)
    $content = preg_replace(
        '/include\s+"includes\/(sidebar|topbar|footer)\.php"/',
        'include "includes/$1.php"',
        $content
    );
    
    if ($content !== $original) {
        file_put_contents($filePath, $content);
        echo "   ✅ Cập nhật: " . basename($filePath) . "\n";
    }
}

// Cập nhật file trong admin/ (level 1)
$adminFiles = glob($baseDir . '/admin/*.php');
foreach ($adminFiles as $file) {
    updateIncludes($file, 1);
}

// Cập nhật file trong teacher/ (level 1)
$teacherFiles = glob($baseDir . '/teacher/*.php');
foreach ($teacherFiles as $file) {
    updateIncludes($file, 1);
}

// Cập nhật file trong student/ (level 1)
$studentFiles = glob($baseDir . '/student/*.php');
foreach ($studentFiles as $file) {
    updateIncludes($file, 1);
}

// Cập nhật file trong public/ (level 1)
$publicFiles = glob($baseDir . '/public/*.php');
foreach ($publicFiles as $file) {
    updateIncludes($file, 1);
}

echo "\n";

// 5. CẬP NHẬT LOGIN REDIRECT
echo "🔐 BƯỚC 5: Cập nhật login redirect...\n";

$loginFile = $baseDir . '/public/login.php';
if (file_exists($loginFile)) {
    $content = file_get_contents($loginFile);
    
    // Cập nhật redirect sau khi login
    $content = preg_replace(
        '/header\("Location: student_home\.php"\)/',
        'header("Location: ../student/student_home.php")',
        $content
    );
    $content = preg_replace(
        '/header\("Location: home\.php"\)/',
        'header("Location: ../admin/home.php")', // Mặc định admin
        $content
    );
    
    file_put_contents($loginFile, $content);
    echo "   ✅ Cập nhật login.php redirects\n";
}

// Cập nhật auth.php
$authFile = $baseDir . '/config/auth.php';
if (file_exists($authFile)) {
    $content = file_get_contents($authFile);
    
    $content = preg_replace(
        '/header\("Location: login\.php"\)/',
        'header("Location: /course-ms/public/login.php")', // Absolute path
        $content
    );
    
    file_put_contents($authFile, $content);
    echo "   ✅ Cập nhật auth.php redirects\n";
}

echo "\n";

// 6. TẠO .HTACCESS CHO BẢO MẬT
echo "🔒 BƯỚC 6: Tạo file bảo mật...\n";

// .htaccess cho config/
$htaccessConfig = "# Chặn truy cập trực tiếp vào config
Order Deny,Allow
Deny from all
";
file_put_contents($baseDir . '/config/.htaccess', $htaccessConfig);
echo "   ✅ Tạo: config/.htaccess\n";

// .htaccess cho thư mục gốc (redirect về public)
$htaccessRoot = "<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteCond %{REQUEST_URI} !^/course-ms/public/
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^(.*)$ public/$1 [L]
</IfModule>
";
file_put_contents($baseDir . '/.htaccess', $htaccessRoot);
echo "   ✅ Tạo: .htaccess (root redirect)\n";

echo "\n";

// 7. TẠO FILE README
echo "📝 BƯỚC 7: Tạo README...\n";

$readme = "# Teacher Bee - Cấu trúc mới

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

Generated: " . date('Y-m-d H:i:s') . "
";

file_put_contents($baseDir . '/MIGRATION_README.md', $readme);
echo "   ✅ Tạo: MIGRATION_README.md\n";

echo "\n";

// 8. TẠO FILE LOCK
touch($baseDir . '/migration_completed.lock');

// 9. HOÀN TẤT
echo str_repeat("=", 60) . "\n";
echo "✅ MIGRATION HOÀN TẤT!\n\n";

echo "📋 BƯỚC TIẾP THEO:\n";
echo "1. Kiểm tra file MIGRATION_README.md để xem hướng dẫn\n";
echo "2. Test các chức năng:\n";
echo "   - http://localhost/course-ms/public/login.php\n";
echo "   - Đăng nhập Admin, Teacher, Student\n";
echo "3. Nếu mọi thứ OK, có thể xóa các file PHP cũ ở thư mục root\n";
echo "4. Backup đã lưu tại: _backup/\n\n";

echo "⚠️  LƯU Ý:\n";
echo "- Một số đường dẫn có thể cần điều chỉnh thủ công\n";
echo "- Kiểm tra kỹ sidebar links trong từng role\n";
echo "- Đảm bảo CSS load đúng trước khi xóa file cũ\n\n";

echo "🎉 Chúc mừng! Dự án đã được tổ chức lại theo cấu trúc mới.\n";
echo str_repeat("=", 60) . "\n";
?>