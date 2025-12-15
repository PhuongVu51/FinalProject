<?php
// 1. KẾT NỐI & AUTH
// Bật hiển thị lỗi chi tiết (Nên giữ lại lúc này để kiểm tra)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include "../connection.php"; 
include "../auth.php"; 
requireRole(['admin']);

// Helper bắt lỗi SQL
function runQuery($link, $sql) {
    $res = mysqli_query($link, $sql);
    if(!$res) die("Lỗi SQL: " . mysqli_error($link));
    return $res;
}

// 2. Lấy thống kê (ĐÃ SỬA LỖI CÚ PHÁP Ở ĐÂY)
$stats = [
    'total' => mysqli_fetch_assoc(runQuery($link, "SELECT COUNT(*) as c FROM teachers"))['c'],
    'assigned' => mysqli_fetch_assoc(runQuery($link, "SELECT COUNT(DISTINCT teacher_id) as c FROM classes WHERE teacher_id IS NOT NULL"))['c'],
    'unassigned' => 0, // Tính toán sau
    'inactive' => 0 // Tạm thời là 0 vì không có cột status
];
$stats['unassigned'] = $stats['total'] - $stats['assigned'];

// 3. SEARCH
$search = '';
$where = '';
if(isset($_GET['q']) && trim($_GET['q']) !== ''){
    $search = mysqli_real_escape_string($link, trim($_GET['q']));
    $like = "%$search%";
    $where = "WHERE (u.full_name LIKE '$like' OR u.username LIKE '$like' OR t.email LIKE '$like')";
}


// 3. XỬ LÝ THÊM GIÁO VIÊN
if(isset($_POST['add'])) {
    $name = mysqli_real_escape_string($link, $_POST['name']); 
    $email = mysqli_real_escape_string($link, $_POST['email']); 
    $pass = md5($_POST['pass']);

    // Check trùng email
    $check = runQuery($link, "SELECT id FROM users WHERE username='$email'");
    if(mysqli_num_rows($check) > 0){
        echo "<script>alert('Email này đã được sử dụng!');</script>";
    } else {
        // Thêm vào users (Không dùng cột status)
        runQuery($link, "INSERT INTO users (username,password,role,full_name) VALUES ('$email','$pass','teacher','$name')");
        $uid = mysqli_insert_id($link);
        // Thêm vào teachers
        runQuery($link, "INSERT INTO teachers (user_id,email) VALUES ($uid,'$email')");
        header("Location: manage_teachers.php"); exit;
    }
}

// 4. XỬ LÝ XÓA
if(isset($_GET['del'])){
    $uid = intval($_GET['del']);
    // Cần set lại teacher_id của các lớp do GV này phụ trách thành NULL trước khi xóa
    runQuery($link, "UPDATE classes SET teacher_id=NULL WHERE teacher_id=(SELECT id FROM teachers WHERE user_id=$uid)");
    // Xóa user (và teacher - nếu có FK)
    runQuery($link, "DELETE FROM users WHERE id=$uid");
    header("Location: manage_teachers.php"); exit;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <title>Quản lý Giáo viên | Admin</title>
</head>
<body class="bg-gray-50 flex font-sans text-gray-900">
    
    <?php include "../includes/sidebar.php"; ?>
    
    <div class="flex-1 p-8 ml-[260px]">
        
        <div class="flex justify-between items-end mb-8">
            <div>
                <h1 class="text-2xl font-bold flex items-center gap-3">
                    <i class="ph-duotone ph-chalkboard-teacher text-honey-500 text-3xl"></i> Quản lý Giáo viên
                </h1>
                <p class="text-gray-500 text-sm mt-1">Quản lý danh sách tài khoản giáo viên và theo dõi phân công lớp học.</p>
            </div>
            <div class="text-right">
                <span class="text-sm font-medium text-gray-500">Hôm nay: <?php echo date('d/m/Y'); ?></span>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-4 hover:shadow-md transition">
                <div class="w-12 h-12 rounded-full bg-honey-100 text-honey-600 flex items-center justify-center text-xl">
                    <i class="ph-fill ph-users-three"></i>
                </div>
                <div>
                    <p class="text-gray-500 text-xs font-bold uppercase">Tổng số GV</p>
                    <p class="text-2xl font-bold text-gray-800"><?php echo $stats['total']; ?></p>
                </div>
            </div>
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-4 hover:shadow-md transition">
                <div class="w-12 h-12 rounded-full bg-green-100 text-green-600 flex items-center justify-center text-xl">
                    <i class="ph-fill ph-check-circle"></i>
                </div>
                <div>
                    <p class="text-gray-500 text-xs font-bold uppercase">Đã phân công</p>
                    <p class="text-2xl font-bold text-gray-800"><?php echo $stats['assigned']; ?></p>
                </div>
            </div>
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-4 hover:shadow-md transition">
                <div class="w-12 h-12 rounded-full bg-yellow-100 text-yellow-600 flex items-center justify-center text-xl">
                    <i class="ph-fill ph-star"></i>
                </div>
                <div>
                    <p class="text-gray-500 text-xs font-bold uppercase">GV chưa có lớp</p>
                    <p class="text-2xl font-bold text-gray-800"><?php echo $stats['unassigned']; ?></p>
                </div>
            </div>
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-4 hover:shadow-md transition">
                <div class="w-12 h-12 rounded-full bg-red-100 text-red-600 flex items-center justify-center text-xl">
                    <i class="ph-fill ph-prohibit"></i>
                </div>
                <div>
                    <p class="text-gray-500 text-xs font-bold uppercase">Tài khoản bị khóa</p>
                    <p class="text-2xl font-bold text-gray-800"><?php echo $stats['inactive']; ?></p>
                </div>
            </div>
        </div>


        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            
            <div class="lg:col-span-4 h-fit sticky top-8">
                <div class="bg-white p-6 rounded-2xl shadow-lg border border-gray-100 relative overflow-hidden">
                    <div class="absolute -top-10 -right-10 w-32 h-32 bg-honey-50 rounded-full blur-2xl opacity-60"></div>
                    
                    <h3 class="font-bold text-lg mb-6 text-gray-800 flex items-center gap-2 relative z-10">
                        <span class="w-8 h-8 rounded-lg bg-honey-100 text-honey-600 flex items-center justify-center text-sm">
                            <i class="ph-bold ph-user-plus"></i>
                        </span>
                        Thêm tài khoản Giáo viên
                    </h3>

                    <form method="post" class="space-y-5 relative z-10">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Họ và Tên <span class="text-red-500">*</span></label>
                            <input type="text" name="name" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:border-honey-500 focus:ring-2 focus:ring-honey-100 outline-none transition font-medium" placeholder="Nhập họ tên..." required>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Email (Tên đăng nhập) <span class="text-red-500">*</span></label>
                            <input type="email" name="email" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:border-honey-500 focus:ring-2 focus:ring-honey-100 outline-none transition font-medium" placeholder="mt@bgs.com" required>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Mật khẩu <span class="text-red-500">*</span></label>
                            <input type="password" name="pass" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:border-honey-500 focus:ring-2 focus:ring-honey-100 outline-none transition font-medium" placeholder="••••••" required>
                        </div>
                        <div class="pt-2">
                            <button name="add" class="w-full py-3.5 bg-honey-500 text-white font-bold rounded-xl hover:bg-honey-600 hover:-translate-y-1 transition-all shadow-lg shadow-honey-500/20 flex items-center justify-center gap-2">
                                <i class="ph-bold ph-check-circle"></i> Xác nhận thêm mới
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="lg:col-span-8">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 flex flex-col h-full">
                    
                    <div class="p-5 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-gray-50/50">
                        <h3 class="font-bold text-gray-800 whitespace-nowrap">Danh sách hiện tại</h3>
                        
                        <form method="get" class="flex gap-2 w-full sm:w-auto">
                            <div class="relative flex-1 sm:w-64">
                                <i class="ph-bold ph-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                                <input type="text" name="q" value="<?php echo htmlspecialchars($search); ?>" placeholder="Tìm tên giáo viên..." class="w-full pl-9 pr-4 py-2 border border-gray-200 rounded-lg text-sm focus:border-honey-500 outline-none">
                            </div>
                            <button type="submit" class="px-3 py-2 border border-gray-200 rounded-lg text-gray-500 hover:bg-gray-100">
                                <i class="ph-bold ph-faders"></i>
                            </button>
                        </form>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead class="bg-white border-b text-gray-500 uppercase font-bold text-xs tracking-wider">
                                <tr>
                                    <th class="px-6 py-4">Giáo viên</th>
                                    <th class="px-6 py-4">Email</th>
                                    <th class="px-6 py-4 text-center">Lớp phụ trách</th>
                                    <th class="px-6 py-4 text-center">Trạng thái</th>
                                    <th class="px-6 py-4 text-right">Tác vụ</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <?php 
                                $sql = "SELECT t.*, u.full_name, u.id as uid, 'active' as status,
                                (SELECT COUNT(*) FROM classes WHERE teacher_id=t.id) as class_count 
                                FROM teachers t JOIN users u ON t.user_id=u.id 
                                $where
                                ORDER BY u.id DESC";
                                $res = runQuery($link, $sql);
                                
                                if(mysqli_num_rows($res) == 0):
                                ?>
                                    <tr>
                                        <td colspan="5" class="px-6 py-12 text-center text-gray-400">
                                            <div class="flex flex-col items-center gap-2">
                                                <i class="ph-duotone ph-users-three text-4xl text-gray-300"></i>
                                                <span>Chưa có tài khoản giáo viên nào được tạo.</span>
                                            </div>
                                        </td>
                                    </tr>
                                <?php else: while($r = mysqli_fetch_assoc($res)): ?>
                                <tr class="hover:bg-honey-50/10 transition group">
                                    <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
        <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($r['full_name']); ?>&background=random&color=fff&size=40" class="w-10 h-10 rounded-full border-2 border-white shadow-sm">
        <div>
            <span class="font-bold text-gray-800 text-base group-hover:text-honey-600 transition"><?php echo $r['full_name']; ?></span>
            <div class="text-xs text-gray-400 font-mono mt-0.5">Mã GV: #<?php echo str_pad($r['uid'], 4, '0', STR_PAD_LEFT); ?></div>
        </div>
    </div>
                                    </td>
                                    <td class="px-6 py-4 text-gray-500 font-mono text-xs"><?php echo $r['email']; ?></td>
                                    <td class="px-6 py-4 text-center">
                                        <?php if($r['class_count'] > 0): ?>
                                            <a href="manage_classes.php?teacher=<?php echo $r['uid']; ?>" title="Xem các lớp" class="bg-blue-50 text-blue-600 px-3 py-1 rounded-full text-xs font-bold hover:bg-blue-100 transition">
                                                <?php echo $r['class_count']; ?> lớp
                                            </a>
                                        <?php else: ?>
                                            <span class="text-gray-400 italic text-xs">Chưa gán</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <?php if($r['status'] == 'active'): ?>
                                            <span class="bg-green-50 text-green-600 px-3 py-1 rounded-full text-xs font-bold border border-green-100">Hoạt động</span>
                                        <?php else: ?>
                                            <span class="bg-red-50 text-red-600 px-3 py-1 rounded-full text-xs font-bold border border-red-100">Đã khóa</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex justify-end gap-1 opacity-60 group-hover:opacity-100 transition">
                                            <a href="edit_teacher.php?id=<?php echo $r['uid']; ?>" class="w-8 h-8 rounded-lg text-gray-400 hover:bg-blue-50 hover:text-blue-600 flex items-center justify-center transition" title="Sửa thông tin">
                                                <i class="ph-bold ph-pencil-simple"></i>
                                            </a>
                                            <a href="?toggle_status=<?php echo $r['uid']; ?>" class="w-8 h-8 rounded-lg text-gray-400 hover:bg-yellow-50 hover:text-yellow-600 flex items-center justify-center transition" title="Khóa/Mở khóa">
                                                <i class="ph-bold ph-lock-key"></i>
                                            </a>
                                            <a href="?del=<?php echo $r['uid']; ?>" onclick="return confirm('Xóa giáo viên <?php echo $r['full_name']; ?>?')" class="w-8 h-8 rounded-lg text-gray-400 hover:bg-red-50 hover:text-red-600 flex items-center justify-center transition" title="Xóa tài khoản">
                                                <i class="ph-bold ph-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endwhile; endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>