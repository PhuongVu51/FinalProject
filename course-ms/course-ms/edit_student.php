<?php
// 1. KẾT NỐI & AUTH
$rootPath = dirname(__DIR__); 
include $rootPath . "/connection.php"; 
include $rootPath . "/auth.php"; 
requireRole(['admin']);

function runQuery($link, $sql) {
    $res = mysqli_query($link, $sql);
    if(!$res) die("Lỗi SQL: " . mysqli_error($link));
    return $res;
}

$id = isset($_GET["id"]) ? intval($_GET["id"]) : 0;
if($id == 0) {
    header("Location: manage_students.php"); exit;
}

// 2. LẤY THÔNG TIN CHI TIẾT
$std_sql = "SELECT s.*, u.full_name, u.username as email 
            FROM students s 
            JOIN users u ON s.user_id=u.id 
            WHERE s.id=$id";
$std = mysqli_fetch_assoc(runQuery($link, $std_sql));

if(!$std) {
    header("Location: manage_students.php"); exit;
}

$classes = runQuery($link, "SELECT id, name FROM classes ORDER BY name");

// 3. XỬ LÝ CẬP NHẬT
$message = '';
if(isset($_POST["update"])) {
    // Không nhận 'student_code' từ POST nữa vì nó là readonly, tránh bị tấn công đổi mã
    $name = mysqli_real_escape_string($link, $_POST['full_name']);
    $cid = intval($_POST['class_id']);
    $cid_sql = ($cid > 0) ? $cid : "NULL";
    
    // Chỉ cập nhật class_id (Mã SV giữ nguyên để bảo toàn logic tự động tăng)
    runQuery($link, "UPDATE students SET class_id=$cid_sql WHERE id=$id");
    // Cập nhật họ tên trong bảng users
    runQuery($link, "UPDATE users SET full_name='$name' WHERE id={$std['user_id']}");
    
    $message = '<div class="bg-green-100 border border-green-200 text-green-700 px-4 py-3 rounded-xl mb-6 font-bold">Cập nhật thông tin học sinh thành công.</div>';
    // Lấy lại dữ liệu mới nhất
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
                <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-2">Cập nhật Học sinh</h1>
                <p class="text-sm text-gray-500 font-mono">Mã số định danh hệ thống: #<?php echo $std['student_code']; ?></p>
            </div>
        </div>
        
        <?php if($message) echo $message; ?>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2">
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                    <h3 class="font-bold text-xl mb-6 text-gray-800 flex items-center gap-2">
                        <i class="ph-bold ph-user-gear text-honey-500"></i> Hồ sơ học sinh
                    </h3>
                    
                    <form method="post" class="space-y-6">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-2 text-honey-600">Mã SV (Cố định)</label>
                                <input type="text" value="<?php echo $std['student_code']; ?>" class="w-full px-4 py-3 bg-gray-100 border border-gray-200 rounded-xl font-bold text-gray-500 cursor-not-allowed outline-none" readonly>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Email tài khoản</label>
                                <input type="email" value="<?php echo $std['email']; ?>" class="w-full px-4 py-3 bg-gray-100 border border-gray-200 rounded-xl text-gray-400 cursor-not-allowed outline-none" readonly disabled>
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Họ và Tên Học sinh <span class="text-red-500">*</span></label>
                            <input type="text" name="full_name" value="<?php echo $std['full_name']; ?>" class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl focus:border-honey-500 focus:ring-2 focus:ring-honey-100 outline-none transition font-medium" required>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-2 text-blue-600">Phân lớp hiện tại</label>
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

                        <div class="flex gap-3 pt-4 border-t border-gray-100">
                            <button name="update" class="px-8 py-3 bg-honey-500 text-white font-bold rounded-xl hover:bg-honey-600 transition shadow-lg shadow-honey-500/20 flex items-center justify-center gap-2 transform active:scale-95">
                                <i class="ph-bold ph-check-circle"></i> Cập nhật hồ sơ
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="lg:col-span-1 h-fit sticky top-8">
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                    <h3 class="font-bold text-xl mb-4 text-gray-800 flex items-center gap-2">
                        <i class="ph-bold ph-shield-check text-green-500"></i> Quản trị viên
                    </h3>
                    
                    <div class="bg-gray-50 p-4 rounded-xl text-sm space-y-4">
                        <p class="text-gray-600 italic leading-relaxed">Lưu ý: Bạn đang chỉnh sửa thông tin người dùng với quyền Admin. Các thay đổi sẽ có hiệu lực ngay lập tức tại Dashboard của học sinh.</p>
                        <div class="pt-2">
                             <p class="font-bold text-gray-700 uppercase text-[10px]">Ghi chú hệ thống:</p>
                             <ul class="list-disc pl-4 text-xs text-gray-500 mt-1 space-y-1">
                                 <li>Học sinh không tự đổi được tên.</li>
                                 <li>Mã SV được dùng để tra cứu điểm.</li>
                             </ul>
                        </div>
                    </div>
                    
                    <div class="mt-6 border-t border-gray-100 pt-4">
                        <a href="manage_students.php?del=<?php echo $std['id']; ?>" onclick="return confirm('Cảnh báo: Xóa hoàn toàn tài khoản học sinh này?')" class="w-full py-3 flex items-center justify-center gap-2 text-red-600 bg-red-50 border border-red-100 rounded-xl font-bold hover:bg-red-500 hover:text-white transition-all text-sm">
                            <i class="ph-bold ph-trash"></i> Xóa tài khoản vĩnh viễn
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>