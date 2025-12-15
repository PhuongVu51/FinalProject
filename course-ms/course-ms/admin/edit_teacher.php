<?php
// 1. KẾT NỐI & AUTH
// Bật hiển thị lỗi chi tiết (có thể tắt sau)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// KHẮC PHỤC LỖI ĐƯỜNG DẪN
$rootPath = dirname(__DIR__); 
include $rootPath . "/connection.php"; 
include $rootPath . "/auth.php"; 
requireRole(['admin']);

// Helper bắt lỗi SQL
function runQuery($link, $sql) {
    $res = mysqli_query($link, $sql);
    if(!$res) die("Lỗi SQL: " . mysqli_error($link));
    return $res;
}

// 2. LẤY ID VÀ THÔNG TIN GIÁO VIÊN
$uid = isset($_GET['id']) ? intval($_GET['id']) : 0;
if($uid == 0) {
    header("Location: manage_teachers.php"); exit;
}

$sql = "SELECT u.id, u.username, u.full_name, t.id as tid
        FROM users u 
        JOIN teachers t ON u.id = t.user_id 
        WHERE u.id = $uid AND u.role = 'teacher'";
$teacher = mysqli_fetch_assoc(runQuery($link, $sql));

if(!$teacher) die("Không tìm thấy Giáo viên!");

// 3. XỬ LÝ CẬP NHẬT THÔNG TIN
$message = '';
if(isset($_POST['update'])){
    $name = mysqli_real_escape_string($link, $_POST['name']);
    $new_pass = $_POST['new_pass'];
    
    // Cập nhật tên
    runQuery($link, "UPDATE users SET full_name='$name' WHERE id=$uid");

    // Cập nhật mật khẩu (nếu có nhập)
    if(!empty($new_pass)){
        $pass_md5 = md5($new_pass);
        runQuery($link, "UPDATE users SET password='$pass_md5' WHERE id=$uid");
        $message = '<p class="text-green-600 font-bold">Cập nhật thành công! Mật khẩu đã được thay đổi.</p>';
    } else {
        $message = '<p class="text-green-600 font-bold">Cập nhật thành công tên giáo viên.</p>';
    }

    // Refresh lại dữ liệu sau khi update
    $teacher = mysqli_fetch_assoc(runQuery($link, $sql));
}

// 4. Lấy thông tin lớp học do GV này phụ trách
$sqlClasses = "SELECT name FROM classes WHERE teacher_id = {$teacher['tid']}";
$resClasses = runQuery($link, $sqlClasses);
$classes = [];
while($row = mysqli_fetch_assoc($resClasses)) $classes[] = $row['name'];

?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <title>Chỉnh sửa GV: <?php echo $teacher['full_name']; ?> | Admin</title>
</head>
<body class="bg-gray-50 flex font-sans text-gray-900">
    
    <?php include $rootPath . "/includes/sidebar.php"; ?>
    
    <div class="flex-1 p-8 ml-[260px]">
        
        <div class="mb-6">
            <a href="manage_teachers.php" class="inline-flex items-center gap-2 text-gray-500 hover:text-honey-500 transition font-bold text-sm">
                <i class="ph-bold ph-arrow-left"></i> Quay lại Quản lý Giáo viên
            </a>
        </div>

        <div class="flex items-center gap-4 mb-8">
            <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($teacher['full_name']); ?>&background=random&color=fff&size=50" class="w-14 h-14 rounded-full border-2 border-white shadow-md" alt="Avatar">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                    Cập nhật Giáo viên
                </h1>
                <p class="text-sm text-gray-500">Mã GV: #<?php echo str_pad($teacher['id'], 4, '0', STR_PAD_LEFT); ?></p>
            </div>
        </div>
        
        <?php if($message): ?>
            <div class="bg-green-100 border border-green-200 text-green-700 px-4 py-3 rounded-xl mb-4">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <div class="lg:col-span-2">
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                    <h3 class="font-bold text-xl mb-6 text-gray-800 flex items-center gap-2">
                        <i class="ph-bold ph-user-gear text-honey-500"></i> Thông tin cá nhân & Tài khoản
                    </h3>
                    
                    <form method="post" class="space-y-6">
                        
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Email (Tên đăng nhập)</label>
                            <input type="email" name="email" value="<?php echo $teacher['username']; ?>" class="w-full px-4 py-3 bg-gray-100 border border-gray-200 rounded-xl outline-none transition font-medium cursor-not-allowed" readonly disabled>
                            <p class="text-xs text-red-500 mt-1 italic">* Không thể thay đổi email đăng nhập.</p>
                        </div>
                        
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Họ và Tên</label>
                            <input type="text" name="name" value="<?php echo $teacher['full_name']; ?>" class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl focus:border-honey-500 focus:ring-2 focus:ring-honey-100 outline-none transition font-medium" required>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Mật khẩu mới (Để trống nếu không đổi)</label>
                            <input type="password" name="new_pass" class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl focus:border-honey-500 focus:ring-2 focus:ring-honey-100 outline-none transition font-medium" placeholder="••••••">
                            <p class="text-xs text-gray-500 mt-1 italic">Mật khẩu sẽ được cập nhật nếu bạn nhập giá trị mới.</p>
                        </div>
                        
                        <div class="pt-4">
                            <button name="update" class="px-6 py-3 bg-honey-500 text-white font-bold rounded-xl hover:bg-honey-600 transition shadow-lg shadow-honey-500/20 flex items-center justify-center gap-2 transform active:scale-95">
                                <i class="ph-bold ph-check-circle"></i> Lưu thay đổi
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="lg:col-span-1 h-fit sticky top-8">
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                    <h3 class="font-bold text-xl mb-4 text-gray-800 flex items-center gap-2">
                        <i class="ph-bold ph-books text-blue-500"></i> Phân công lớp
                    </h3>
                    
                    <?php if(!empty($classes)): ?>
                        <p class="text-sm text-gray-600 mb-4">Giáo viên này đang chủ nhiệm các lớp sau:</p>
                        <div class="space-y-2">
                            <?php foreach($classes as $c): ?>
                                <a href="manage_classes.php" class="flex items-center gap-2 bg-blue-50 text-blue-700 px-4 py-2 rounded-lg font-medium hover:bg-blue-100 transition border border-blue-200">
                                    <i class="ph-bold ph-chalkboard-teacher text-blue-500"></i> <?php echo $c; ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="bg-yellow-50 text-yellow-700 p-4 rounded-xl border border-yellow-200 text-sm">
                            <p class="flex items-center gap-2 font-bold"><i class="ph-bold ph-warning"></i> Lưu ý:</p>
                            <p class="mt-1">Giáo viên này hiện chưa được phân công chủ nhiệm lớp nào. Cần phân công tại trang <a href="manage_classes.php" class="underline hover:text-yellow-800 font-bold">Quản lý Lớp học</a>.</p>
                        </div>
                    <?php endif; ?>
                    
                    <div class="mt-6 border-t border-gray-100 pt-4">
                        <h4 class="font-bold text-gray-800 mb-2">Tác vụ khẩn cấp</h4>
                        <a href="manage_teachers.php?toggle_status=<?php echo $teacher['id']; ?>" class="w-full py-2 flex items-center justify-center gap-2 text-red-600 bg-red-50 border border-red-100 rounded-xl font-bold hover:bg-red-100 transition transform active:scale-95 text-sm">
                            <i class="ph-bold ph-lock-key"></i> Khóa/Mở Khóa Tài khoản
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </div>
</body>
</html>