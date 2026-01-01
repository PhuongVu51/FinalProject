<?php
// 1. KẾT NỐI & AUTH
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

// 2. LẤY THỐNG KÊ
$stats = [
    'total' => mysqli_fetch_assoc(runQuery($link, "SELECT COUNT(*) as c FROM students"))['c'],
    'assigned' => mysqli_fetch_assoc(runQuery($link, "SELECT COUNT(*) as c FROM students WHERE class_id IS NOT NULL"))['c'],
    'unassigned' => mysqli_fetch_assoc(runQuery($link, "SELECT COUNT(*) as c FROM students WHERE class_id IS NULL"))['c'],
];

// --- LOGIC TỰ ĐỘNG TẠO MÃ SINH VIÊN ---
$lastCodeRes = runQuery($link, "SELECT student_code FROM students ORDER BY id DESC LIMIT 1");
$lastCodeData = mysqli_fetch_assoc($lastCodeRes);
$nextStudentCode = ($lastCodeData) ? intval($lastCodeData['student_code']) + 1 : 20230001;

// 3. SEARCH & FILTER
$search = isset($_GET['q']) ? mysqli_real_escape_string($link, trim($_GET['q'])) : '';
$filter = isset($_GET['filter']) ? $_GET['filter'] : '';

$conditions = [];
if ($search !== '') {
    $conditions[] = "(u.full_name LIKE '%$search%' OR u.username LIKE '%$search%' OR s.student_code LIKE '%$search%' OR c.name LIKE '%$search%')";
}
if ($filter === 'assigned') {
    $conditions[] = "s.class_id IS NOT NULL";
} elseif ($filter === 'unassigned') {
    $conditions[] = "s.class_id IS NULL";
}

$where = count($conditions) > 0 ? "WHERE " . implode(' AND ', $conditions) : "";

// 4. XỬ LÝ THÊM HỌC SINH
if(isset($_POST['add'])) {
    $name = mysqli_real_escape_string($link, $_POST['name']); 
    $email = mysqli_real_escape_string($link, $_POST['email']); 
    $pass = md5($_POST['pass']);
    $class_id = intval($_POST['class_id']);

    // Kiểm tra định dạng Email bắt buộc có @ và hợp lệ
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('Định dạng Email không hợp lệ!'); window.history.back();</script>";
        exit;
    }

    // Check trùng email
    $check = runQuery($link, "SELECT id FROM users WHERE username='$email'");
    if(mysqli_num_rows($check) > 0){
        echo "<script>alert('Email này đã được sử dụng!');</script>";
    } else {
        runQuery($link, "INSERT INTO users (username,password,role,full_name) VALUES ('$email','$pass','student','$name')");
        $uid = mysqli_insert_id($link);
        $cid_sql = ($class_id > 0) ? $class_id : "NULL";
        
        // Sử dụng $nextStudentCode tự động
        runQuery($link, "INSERT INTO students (user_id, student_code, class_id) VALUES ($uid, '$nextStudentCode', $cid_sql)");
        header("Location: manage_students.php"); exit;
    }
}

// 5. XỬ LÝ XÓA
if(isset($_GET['del'])){
    $sid = intval($_GET['del']);
    $user_id_res = runQuery($link, "SELECT user_id FROM students WHERE id=$sid");
    $user_id = mysqli_fetch_assoc($user_id_res)['user_id'];
    runQuery($link, "DELETE FROM students WHERE id=$sid");
    runQuery($link, "DELETE FROM users WHERE id=$user_id");
    header("Location: manage_students.php"); exit;
}

$classes_list = [];
$cRes = runQuery($link, "SELECT id, name FROM classes ORDER BY name");
while($c = mysqli_fetch_assoc($cRes)) $classes_list[] = $c;
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <title>Quản lý Học sinh | Admin</title>
    <?php include $rootPath . "/includes/header_config.php"; ?>
</head>
<body class="bg-gray-50 flex font-sans text-gray-900">
    
    <?php include $rootPath . "/includes/sidebar.php"; ?>
    
    <div class="flex-1 p-8 ml-[260px]">
        
        <div class="flex justify-between items-end mb-8">
            <div>
                <h1 class="text-2xl font-bold flex items-center gap-3">
                    <i class="ph-duotone ph-student text-honey-500 text-3xl"></i> Quản lý Học sinh
                </h1>
                <p class="text-gray-500 text-sm mt-1">Quản lý danh sách tài khoản học sinh và phân lớp.</p>
            </div>
            <div class="text-right">
                <span class="text-sm font-medium text-gray-500">Hôm nay: <?php echo date('d/m/Y'); ?></span>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <a href="manage_students.php" class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-4 hover:shadow-md transition cursor-pointer <?php echo $filter=='' ? 'ring-2 ring-honey-500' : ''; ?>">
                <div class="w-12 h-12 rounded-full bg-honey-100 text-honey-600 flex items-center justify-center text-xl">
                    <i class="ph-fill ph-users-three"></i>
                </div>
                <div>
                    <p class="text-gray-500 text-xs font-bold uppercase">Tổng số Học sinh</p>
                    <p class="text-2xl font-bold text-gray-800"><?php echo $stats['total']; ?></p>
                </div>
            </a>
            <a href="?filter=assigned" class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-4 hover:shadow-md transition cursor-pointer <?php echo $filter=='assigned' ? 'ring-2 ring-green-500' : ''; ?>">
                <div class="w-12 h-12 rounded-full bg-green-100 text-green-600 flex items-center justify-center text-xl">
                    <i class="ph-fill ph-check-circle"></i>
                </div>
                <div>
                    <p class="text-gray-500 text-xs font-bold uppercase">Đã phân lớp</p>
                    <p class="text-2xl font-bold text-gray-800"><?php echo $stats['assigned']; ?></p>
                </div>
            </a>
            <a href="?filter=unassigned" class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-4 hover:shadow-md transition cursor-pointer <?php echo $filter=='unassigned' ? 'ring-2 ring-red-500' : ''; ?>">
                <div class="w-12 h-12 rounded-full bg-red-100 text-red-600 flex items-center justify-center text-xl">
                    <i class="ph-fill ph-warning-circle"></i>
                </div>
                <div>
                    <p class="text-gray-500 text-xs font-bold uppercase">Chưa phân lớp</p>
                    <p class="text-2xl font-bold text-gray-800"><?php echo $stats['unassigned']; ?></p>
                </div>
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            <div class="lg:col-span-4 h-fit sticky top-8">
                <div class="bg-white p-6 rounded-2xl shadow-lg border border-gray-100 relative overflow-hidden">
                    <div class="absolute -top-10 -right-10 w-32 h-32 bg-honey-50 rounded-full blur-2xl opacity-60"></div>
                    <h3 class="font-bold text-lg mb-6 text-gray-800 flex items-center gap-2 relative z-10">
                        <span class="w-8 h-8 rounded-lg bg-honey-100 text-honey-600 flex items-center justify-center text-sm">
                            <i class="ph-bold ph-user-plus"></i>
                        </span>
                        Thêm tài khoản Học sinh
                    </h3>

                    <form method="post" class="space-y-5 relative z-10">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Họ và Tên <span class="text-red-500">*</span></label>
                            <input type="text" name="name" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:border-honey-500 outline-none transition font-medium" placeholder="Nhập họ tên..." required>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Mã SV (Tự động)</label>
                            <input type="text" class="w-full px-4 py-3 bg-gray-100 border border-gray-200 rounded-xl font-mono font-bold text-honey-600 cursor-not-allowed" value="<?php echo $nextStudentCode; ?>" readonly>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Email <span class="text-red-500">*</span></label>
                            <input type="email" name="email" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:border-honey-500 outline-none transition font-medium" placeholder="user@student.com" required>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Mật khẩu <span class="text-red-500">*</span></label>
                            <input type="password" name="pass" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:border-honey-500 outline-none transition font-medium" placeholder="••••••" required>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Phân lớp</label>
                            <select name="class_id" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:border-honey-500 outline-none transition cursor-pointer">
                                <option value="0">-- Chưa phân lớp --</option>
                                <?php foreach($classes_list as $c): ?>
                                    <option value="<?php echo $c['id']; ?>"><?php echo $c['name']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <button name="add" class="w-full py-3.5 bg-honey-500 text-white font-bold rounded-xl hover:bg-honey-600 transition-all flex items-center justify-center gap-2">
                            <i class="ph-bold ph-check-circle"></i> Xác nhận thêm mới
                        </button>
                    </form>
                </div>
            </div>

            <div class="lg:col-span-8">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 flex flex-col h-full">
                    <div class="p-5 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-gray-50/50">
                        <h3 class="font-bold text-gray-800">
                            <?php 
                                if($filter=='assigned') echo "Danh sách Đã phân lớp";
                                elseif($filter=='unassigned') echo "Danh sách Chưa phân lớp";
                                else echo "Danh sách Học sinh";
                            ?>
                        </h3>
                        <form method="get" class="flex gap-2">
                            <input type="hidden" name="filter" value="<?php echo $filter; ?>">
                            <div class="relative">
                                <i class="ph-bold ph-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                                <input type="text" name="q" value="<?php echo htmlspecialchars($search); ?>" placeholder="Tìm kiếm..." class="pl-9 pr-4 py-2 border border-gray-200 rounded-lg text-sm focus:border-honey-500 outline-none">
                            </div>
                            <button type="submit" class="px-3 py-2 bg-honey-500 text-white rounded-lg hover:bg-honey-600 transition"><i class="ph-bold ph-funnel"></i></button>
                        </form>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead class="bg-white border-b text-gray-500 uppercase font-bold text-xs tracking-wider">
                                <tr>
                                    <th class="px-6 py-4">Học sinh</th>
                                    <th class="px-6 py-4">Mã SV</th>
                                    <th class="px-6 py-4 text-center">Lớp học</th>
                                    <th class="px-6 py-4 text-right">Tác vụ</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
    <?php 
    // ĐỊNH NGHĨA LẠI SQL TẠI ĐÂY (Sửa lỗi biến $sql bị trống)
    // Sử dụng LEFT JOIN để đảm bảo hiện cả học sinh lỗi user
    $sql = "SELECT s.id, s.student_code, u.full_name, u.username as email, c.name as class_name
            FROM students s 
            LEFT JOIN users u ON s.user_id = u.id 
            LEFT JOIN classes c ON s.class_id = c.id 
            $where
            ORDER BY s.id DESC";
            
    $res = runQuery($link, $sql);
    
    if(mysqli_num_rows($res) == 0):
    ?>
        <tr><td colspan="4" class="px-6 py-12 text-center text-gray-400 italic">Không tìm thấy dữ liệu phù hợp.</td></tr>
    <?php else: while($r = mysqli_fetch_assoc($res)): ?>
    <tr class="hover:bg-honey-50/10 transition group">
        <td class="px-6 py-4">
            <div class="flex items-center gap-3">
                <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($r['full_name'] ?? 'Student'); ?>&background=random&color=fff&size=40" class="w-10 h-10 rounded-full border-2 border-white shadow-sm">
                <a href="edit_student.php?id=<?php echo $r['id']; ?>" class="font-bold text-gray-800 hover:text-honey-500 transition">
                    <?php echo $r['full_name'] ?? 'N/A'; ?>
                </a>
            </div>
        </td>
        <td class="px-6 py-4 text-gray-700 font-mono text-sm font-bold"><?php echo $r['student_code']; ?></td>
        <td class="px-6 py-4 text-center">
            <?php if($r['class_name']): ?>
                <span class="bg-blue-50 text-blue-600 px-3 py-1 rounded-full text-xs font-bold"><?php echo $r['class_name']; ?></span>
            <?php else: ?>
                <span class="bg-red-50 text-red-600 px-3 py-1 rounded-full text-xs font-bold">Chưa gán</span>
            <?php endif; ?>
        </td>
        <td class="px-6 py-4 text-right">
            <div class="flex justify-end gap-1">
                <a href="edit_student.php?id=<?php echo $r['id']; ?>" class="w-8 h-8 rounded-lg text-gray-400 hover:bg-blue-50 hover:text-blue-600 flex items-center justify-center transition" title="Sửa thông tin">
                    <i class="ph-bold ph-pencil-simple"></i>
                </a>
                <a href="?del=<?php echo $r['id']; ?>" onclick="return confirm('Xóa học sinh này?')" class="w-8 h-8 rounded-lg text-gray-400 hover:bg-red-50 hover:text-red-600 flex items-center justify-center transition" title="Xóa tài khoản">
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