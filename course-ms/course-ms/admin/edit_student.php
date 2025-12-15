<?php
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

$id = isset($_GET["id"]) ? intval($_GET["id"]) : 0;
if($id == 0) {
    header("Location: manage_students.php"); exit;
}

$std_sql = "SELECT s.*, u.full_name, u.username as email 
            FROM students s 
            JOIN users u ON s.user_id=u.id 
            WHERE s.id=$id";
$std = mysqli_fetch_assoc(runQuery($link, $std_sql));

if(!$std) {
    header("Location: manage_students.php"); exit;
}

// Danh sách lớp
$classes = runQuery($link, "SELECT id, name FROM classes ORDER BY name");

// XỬ LÝ CẬP NHẬT
$message = '';
if(isset($_POST["update"])) {
    $code = mysqli_real_escape_string($link, $_POST['student_code']);
    $name = mysqli_real_escape_string($link, $_POST['full_name']);
    $cid = intval($_POST['class_id']);
    $cid_sql = ($cid > 0) ? $cid : "NULL";
    
    // Update thông tin trong bảng students
    runQuery($link, "UPDATE students SET student_code='$code', class_id=$cid_sql WHERE id=$id");
    // Update tên trong bảng users
    runQuery($link, "UPDATE users SET full_name='$name' WHERE id={$std['user_id']}");
    
    $message = '<p class="text-green-600 font-bold">Cập nhật thông tin học sinh thành công.</p>';
    // Lấy lại dữ liệu mới
    $std = mysqli_fetch_assoc(runQuery($link, $std_sql));
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <title>Chỉnh sửa HS: <?php echo $std['full_name']; ?> | Admin</title>
    <?php include $rootPath . "/includes/header_config.php"; ?>
</head>
<body class="bg-gray-50 flex font-sans text-gray-900">
    
    <?php include $rootPath . "/includes/sidebar.php"; ?>
    
    <div class="flex-1 p-8 ml-[260px]">
        
        <div class="mb-6">
            <a href="manage_students.php" class="inline-flex items-center gap-2 text-gray-500 hover:text-honey-500 transition font-bold text-sm">
                <i class="ph-bold ph-arrow-left"></i> Quay lại Quản lý Học sinh
            </a>
        </div>

        <div class="flex items-center gap-4 mb-8">
            <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($std['full_name']); ?>&background=random&color=fff&size=50" class="w-14 h-14 rounded-full border-2 border-white shadow-md" alt="Avatar">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                    Cập nhật Học sinh
                </h1>
                <p class="text-sm text-gray-500">Mã HS: **<?php echo $std['student_code']; ?>**</p>
            </div>
        </div>
        
        <?php if($message): ?>
            <div class="bg-green-100 border border-green-200 text-green-700 px-4 py-3 rounded-xl mb-6">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <div class="lg:col-span-2">
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                    <h3 class="font-bold text-xl mb-6 text-gray-800 flex items-center gap-2">
                        <i class="ph-bold ph-user-gear text-honey-500"></i> Thông tin cơ bản
                    </h3>
                    
                    <form method="post" class="space-y-6">
                        
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Mã SV/HS</label>
                            <input type="text" name="student_code" value="<?php echo $std['student_code']; ?>" class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl focus:border-honey-500 focus:ring-2 focus:ring-honey-100 outline-none transition font-medium" required>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Email (Tên đăng nhập)</label>
                            <input type="email" name="email" value="<?php echo $std['email']; ?>" class="w-full px-4 py-3 bg-gray-100 border border-gray-200 rounded-xl outline-none transition font-medium cursor-not-allowed" readonly disabled>
                            <p class="text-xs text-red-500 mt-1 italic">* Email đăng nhập không thể thay đổi.</p>
                        </div>
                        
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Họ và Tên</label>
                            <input type="text" name="full_name" value="<?php echo $std['full_name']; ?>" class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl focus:border-honey-500 focus:ring-2 focus:ring-honey-100 outline-none transition font-medium" required>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Phân lớp</label>
                            <div class="relative">
                                <select name="class_id" class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl focus:border-honey-500 focus:ring-2 focus:ring-honey-100 outline-none appearance-none transition cursor-pointer">
                                    <option value="0">-- Chưa phân lớp --</option>
                                    <?php while($c = mysqli_fetch_assoc($classes)): ?>
                                        <option value="<?php echo $c['id']; ?>" <?php if($std['class_id'] == $c['id']) echo 'selected'; ?>>
                                            <?php echo $c['name']; ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                                <div class="absolute inset-y-0 right-4 flex items-center pointer-events-none text-gray-400">
                                    <i class="ph-bold ph-caret-down"></i>
                                </div>
                            </div>
                        </div>

                        <div class="flex gap-3 pt-4">
                            <button name="update" class="px-6 py-3 bg-honey-500 text-white font-bold rounded-xl hover:bg-honey-600 transition shadow-lg shadow-honey-500/20 flex items-center justify-center gap-2 transform active:scale-95">
                                <i class="ph-bold ph-check-circle"></i> Lưu thay đổi
                            </button>
                            <a href="manage_students.php" class="px-6 py-3 bg-gray-100 text-gray-600 font-bold rounded-xl hover:bg-gray-200 transition flex items-center gap-2">
                                <i class="ph-bold ph-x"></i> Hủy
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="lg:col-span-1 h-fit sticky top-8">
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                    <h3 class="font-bold text-xl mb-4 text-gray-800 flex items-center gap-2">
                        <i class="ph-bold ph-info text-blue-500"></i> Thông tin khác
                    </h3>
                    
                    <div class="bg-gray-50 p-4 rounded-xl text-sm space-y-3">
                        <p class="font-medium text-gray-700">Trạng thái tài khoản: <span class="bg-green-100 text-green-700 px-2 py-0.5 rounded-full font-bold text-xs">Active (Giả định)</span></p>
                        <p class="font-medium text-gray-700">Ngày tạo: <span class="text-gray-500">--/--/----</span></p>
                        <p class="font-medium text-gray-700">ID người dùng (UID): <span class="font-mono text-gray-600">#<?php echo $std['user_id']; ?></span></p>
                    </div>
                    
                    <div class="mt-6 border-t border-gray-100 pt-4">
                        <h4 class="font-bold text-gray-800 mb-2">Tác vụ khẩn cấp</h4>
                        <a href="manage_students.php?del=<?php echo $std['id']; ?>" onclick="return confirm('Cảnh báo: Xóa hoàn toàn tài khoản học sinh này?')" class="w-full py-2 flex items-center justify-center gap-2 text-red-600 bg-red-50 border border-red-100 rounded-xl font-bold hover:bg-red-100 transition transform active:scale-95 text-sm">
                            <i class="ph-bold ph-trash"></i> Xóa hoàn toàn Tài khoản
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>