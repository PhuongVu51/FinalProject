<?php
// 1. KẾT NỐI & AUTH
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

// 2. LẤY THỐNG KÊ
$stats = [
    'total' => mysqli_fetch_assoc(runQuery($link, "SELECT COUNT(*) as c FROM students"))['c'],
    'assigned' => mysqli_fetch_assoc(runQuery($link, "SELECT COUNT(*) as c FROM students WHERE class_id IS NOT NULL"))['c'],
    'unassigned' => mysqli_fetch_assoc(runQuery($link, "SELECT COUNT(*) as c FROM students WHERE class_id IS NULL"))['c'],
];

// 3. XỬ LÝ THÊM HỌC SINH
if(isset($_POST['add'])) {
    $name = mysqli_real_escape_string($link, $_POST['name']); 
    $email = mysqli_real_escape_string($link, $_POST['email']); 
    $pass = md5($_POST['pass']);
    $student_code = mysqli_real_escape_string($link, $_POST['student_code']);
    $class_id = intval($_POST['class_id']);

    // Check trùng email
    $check = runQuery($link, "SELECT id FROM users WHERE username='$email'");
    if(mysqli_num_rows($check) > 0){
        echo "<script>alert('Email này đã được sử dụng!');</script>";
    } else {
        // Thêm vào users
        runQuery($link, "INSERT INTO users (username,password,role,full_name) VALUES ('$email','$pass','student','$name')");
        $uid = mysqli_insert_id($link);
        // Thêm vào students
        $cid_sql = ($class_id > 0) ? $class_id : "NULL";
        runQuery($link, "INSERT INTO students (user_id, student_code, class_id) VALUES ($uid, '$student_code', $cid_sql)");
        header("Location: manage_students.php"); exit;
    }
}

// 4. XỬ LÝ XÓA
if(isset($_GET['del'])){
    $sid = intval($_GET['del']);
    // Lấy user_id để xóa tài khoản
    $user_id_res = runQuery($link, "SELECT user_id FROM students WHERE id=$sid");
    $user_id = mysqli_fetch_assoc($user_id_res)['user_id'];
    
    // Xóa student (sẽ tự động xóa user nếu có Foreign Key)
    runQuery($link, "DELETE FROM students WHERE id=$sid");
    runQuery($link, "DELETE FROM users WHERE id=$user_id");
    
    header("Location: manage_students.php"); exit;
}

// Lấy danh sách lớp cho dropdown
$classes_list = [];
$cRes = runQuery($link, "SELECT id, name FROM classes ORDER BY name");
while($c = mysqli_fetch_assoc($cRes)) $classes_list[] = $c;
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <title>Quản lý Học sinh | Admin</title>
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
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-4 hover:shadow-md transition">
                <div class="w-12 h-12 rounded-full bg-honey-100 text-honey-600 flex items-center justify-center text-xl">
                    <i class="ph-fill ph-users-three"></i>
                </div>
                <div>
                    <p class="text-gray-500 text-xs font-bold uppercase">Tổng số Học sinh</p>
                    <p class="text-2xl font-bold text-gray-800"><?php echo $stats['total']; ?></p>
                </div>
            </div>
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-4 hover:shadow-md transition">
                <div class="w-12 h-12 rounded-full bg-green-100 text-green-600 flex items-center justify-center text-xl">
                    <i class="ph-fill ph-check-circle"></i>
                </div>
                <div>
                    <p class="text-gray-500 text-xs font-bold uppercase">Đã phân lớp</p>
                    <p class="text-2xl font-bold text-gray-800"><?php echo $stats['assigned']; ?></p>
                </div>
            </div>
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-4 hover:shadow-md transition">
                <div class="w-12 h-12 rounded-full bg-red-100 text-red-600 flex items-center justify-center text-xl">
                    <i class="ph-fill ph-warning-circle"></i>
                </div>
                <div>
                    <p class="text-gray-500 text-xs font-bold uppercase">Chưa phân lớp</p>
                    <p class="text-2xl font-bold text-gray-800"><?php echo $stats['unassigned']; ?></p>
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
                        Thêm tài khoản Học sinh
                    </h3>

                    <form method="post" class="space-y-5 relative z-10">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Họ và Tên <span class="text-red-500">*</span></label>
                            <input type="text" name="name" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:border-honey-500 focus:ring-2 focus:ring-honey-100 outline-none transition font-medium" placeholder="Nhập họ tên..." required>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Mã SV/HS <span class="text-red-500">*</span></label>
                            <input type="text" name="student_code" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:border-honey-500 focus:ring-2 focus:ring-honey-100 outline-none transition font-medium" placeholder="VD: 20230001" required>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Email (Tên đăng nhập) <span class="text-red-500">*</span></label>
                            <input type="email" name="email" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:border-honey-500 focus:ring-2 focus:ring-honey-100 outline-none transition font-medium" placeholder="user@student.com" required>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Mật khẩu <span class="text-red-500">*</span></label>
                            <input type="password" name="pass" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:border-honey-500 focus:ring-2 focus:ring-honey-100 outline-none transition font-medium" placeholder="••••••" required>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Phân lớp</label>
                            <div class="relative">
                                <select name="class_id" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:border-honey-500 focus:ring-2 focus:ring-honey-100 outline-none appearance-none transition cursor-pointer">
                                    <option value="0">-- Chưa phân lớp --</option>
                                    <?php foreach($classes_list as $c): ?>
                                        <option value="<?php echo $c['id']; ?>"><?php echo $c['name']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="absolute inset-y-0 right-4 flex items-center pointer-events-none text-gray-400">
                                    <i class="ph-bold ph-caret-down"></i>
                                </div>
                            </div>
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
                        <h3 class="font-bold text-gray-800 whitespace-nowrap">Danh sách Học sinh</h3>
                        
                        <div class="flex gap-2 w-full sm:w-auto">
                            <div class="relative flex-1 sm:w-64">
                                <i class="ph-bold ph-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                                <input type="text" placeholder="Tìm tên/Mã SV..." class="w-full pl-9 pr-4 py-2 border border-gray-200 rounded-lg text-sm focus:border-honey-500 outline-none">
                            </div>
                            <button class="px-3 py-2 border border-gray-200 rounded-lg text-gray-500 hover:bg-gray-100">
                                <i class="ph-bold ph-faders"></i>
                            </button>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead class="bg-white border-b text-gray-500 uppercase font-bold text-xs tracking-wider">
                                <tr>
                                    <th class="px-6 py-4">Học sinh</th>
                                    <th class="px-6 py-4">Mã SV</th>
                                    <th class="px-6 py-4">Email</th>
                                    <th class="px-6 py-4 text-center">Lớp học</th>
                                    <th class="px-6 py-4 text-right">Tác vụ</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <?php 
                                $sql = "SELECT s.id, s.student_code, u.full_name, u.username as email, c.name as class_name
                                        FROM students s 
                                        JOIN users u ON s.user_id=u.id 
                                        LEFT JOIN classes c ON s.class_id=c.id 
                                        ORDER BY u.full_name ASC";
                                $res = runQuery($link, $sql);
                                
                                if(mysqli_num_rows($res) == 0):
                                ?>
                                    <tr>
                                        <td colspan="5" class="px-6 py-12 text-center text-gray-400">
                                            <div class="flex flex-col items-center gap-2">
                                                <i class="ph-duotone ph-student text-4xl text-gray-300"></i>
                                                <span>Chưa có tài khoản học sinh nào được tạo.</span>
                                            </div>
                                        </td>
                                    </tr>
                                <?php else: while($r = mysqli_fetch_assoc($res)): ?>
                                <tr class="hover:bg-honey-50/10 transition group">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($r['full_name']); ?>&background=random&color=fff&size=40" class="w-10 h-10 rounded-full border-2 border-white shadow-sm">
                                            <a href="edit_student.php?id=<?php echo $r['id']; ?>" class="font-bold text-gray-800 text-base hover:text-honey-500 hover:underline transition">
                                                <?php echo $r['full_name']; ?>
                                            </a>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-gray-700 font-mono text-sm font-bold"><?php echo $r['student_code']; ?></td>
                                    <td class="px-6 py-4 text-gray-500 font-mono text-xs"><?php echo $r['email']; ?></td>
                                    <td class="px-6 py-4 text-center">
                                        <?php if($r['class_name']): ?>
                                            <span class="bg-blue-50 text-blue-600 px-3 py-1 rounded-full text-xs font-bold"><?php echo $r['class_name']; ?></span>
                                        <?php else: ?>
                                            <span class="bg-red-50 text-red-600 px-3 py-1 rounded-full text-xs font-bold">Chưa gán</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex justify-end gap-1 opacity-60 group-hover:opacity-100 transition">
                                            <a href="edit_student.php?id=<?php echo $r['id']; ?>" class="w-8 h-8 rounded-lg text-gray-400 hover:bg-blue-50 hover:text-blue-600 flex items-center justify-center transition" title="Sửa thông tin">
                                                <i class="ph-bold ph-pencil-simple"></i>
                                            </a>
                                            <a href="?del=<?php echo $r['id']; ?>" onclick="return confirm('Xóa học sinh <?php echo $r['full_name']; ?>?')" class="w-8 h-8 rounded-lg text-gray-400 hover:bg-red-50 hover:text-red-600 flex items-center justify-center transition" title="Xóa tài khoản">
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