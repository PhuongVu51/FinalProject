<?php
include "../connection.php"; 
include "../auth.php"; 
requireRole(['admin']);

// Hàm helper (copy lại để dùng)
function runQuery($link, $sql) {
    $res = mysqli_query($link, $sql);
    if(!$res) die("Lỗi SQL: " . mysqli_error($link));
    return $res;
}

// Lấy ID lớp từ URL
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if($id == 0) {
    header("Location: manage_classes.php"); exit;
}

// XỬ LÝ: Xóa học sinh khỏi lớp (Set class_id về NULL)
if(isset($_GET['remove_std'])){
    $sid = intval($_GET['remove_std']);
    runQuery($link, "UPDATE students SET class_id = NULL WHERE id = $sid AND class_id = $id");
    // Refresh lại trang
    header("Location: view_class.php?id=$id"); exit;
}

// 1. Lấy thông tin LỚP + Tên GVCN
$sqlClass = "SELECT c.*, u.full_name as teacher_name 
             FROM classes c 
             LEFT JOIN teachers t ON c.teacher_id = t.id 
             LEFT JOIN users u ON t.user_id = u.id 
             WHERE c.id = $id";
$class = mysqli_fetch_assoc(runQuery($link, $sqlClass));

if(!$class) die("Không tìm thấy lớp học!");

// 2. Lấy danh sách HỌC SINH trong lớp
// Giả định bảng students có các cột: full_name, email, dob, gender, status...
// Bỏ order by first_name đi vì có thể không có cột này
$sqlStudents = "SELECT * FROM students WHERE class_id = $id ORDER BY id DESC";
$resStudents = runQuery($link, $sqlStudents);
$studentCount = mysqli_num_rows($resStudents);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <title>Lớp <?php echo $class['name']; ?> | Admin</title>
    <?php include "../includes/header_config.php"; ?>
</head>
<body class="bg-gray-50 flex font-sans text-gray-900">
    
    <?php include "../includes/sidebar.php"; ?>
    
    <div class="flex-1 p-8 ml-[260px]">
        
        <div class="mb-6">
            <a href="manage_classes.php" class="inline-flex items-center gap-2 text-gray-500 hover:text-honey-500 transition font-bold text-sm">
                <i class="ph-bold ph-arrow-left"></i> Quay lại Danh sách lớp
            </a>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 mb-8 relative overflow-hidden">
            <div class="absolute top-0 right-0 p-10 opacity-5 pointer-events-none">
                <i class="ph-duotone ph-chalkboard-teacher text-9xl text-honey-500"></i>
            </div>
            
            <div class="relative z-10">
                <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
                    <div>
                        <div class="flex items-center gap-3 mb-2">
                            <span class="w-10 h-10 rounded-lg bg-honey-500 text-white flex items-center justify-center font-bold text-xl shadow-lg shadow-honey-500/30">
                                <?php echo substr($class['name'], 0, 1); ?>
                            </span>
                            <h1 class="text-3xl font-bold text-gray-800"><?php echo $class['name']; ?></h1>
                        </div>
                        <p class="text-gray-500 flex items-center gap-2 pl-1">
                            <i class="ph-bold ph-hash"></i> Mã lớp: <span class="font-mono font-bold text-gray-700">CLASS-<?php echo str_pad($class['id'], 3, '0', STR_PAD_LEFT); ?></span>
                        </p>
                    </div>
                    
                    <div class="flex gap-3">
                        <a href="edit_class.php?id=<?php echo $class['id']; ?>" class="px-5 py-2.5 bg-white border border-gray-200 text-gray-700 rounded-xl font-bold hover:bg-gray-50 hover:border-gray-300 transition flex items-center gap-2 shadow-sm">
                            <i class="ph-bold ph-pencil-simple"></i> Sửa lớp
                        </a>
                        <button class="px-5 py-2.5 bg-honey-500 text-white rounded-xl font-bold hover:bg-honey-600 transition shadow-lg shadow-honey-500/20 flex items-center gap-2">
                            <i class="ph-bold ph-check-square"></i> Điểm danh
                        </button>
                    </div>
                </div>

                <div class="mt-8 flex flex-col md:flex-row gap-8 border-t border-gray-100 pt-6">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center text-2xl border border-blue-100">
                             <i class="ph-fill ph-user-circle"></i>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 font-bold uppercase tracking-wider">Giáo viên chủ nhiệm</p>
                            <?php if($class['teacher_name']): ?>
                                <p class="text-lg font-bold text-gray-800"><?php echo $class['teacher_name']; ?></p>
                            <?php else: ?>
                                <p class="text-lg font-bold text-red-500 italic flex items-center gap-1">
                                    <i class="ph-fill ph-warning-circle"></i> Chưa phân công
                                </p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="hidden md:block w-px bg-gray-200"></div>

                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-full bg-green-50 text-green-600 flex items-center justify-center text-2xl border border-green-100">
                             <i class="ph-fill ph-student"></i>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 font-bold uppercase tracking-wider">Tổng số học sinh</p>
                            <p class="text-lg font-bold text-gray-800"><?php echo $studentCount; ?> / 40 <span class="text-xs text-gray-400 font-normal">(Tiêu chuẩn)</span></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                <h3 class="font-bold text-gray-800 flex items-center gap-2">
                    <i class="ph-fill ph-users-three text-honey-500"></i> Danh sách thành viên
                </h3>
                
                <div class="relative">
                    <i class="ph-bold ph-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                    <input type="text" placeholder="Tìm học sinh..." class="pl-8 pr-3 py-1.5 text-sm border border-gray-200 rounded-lg focus:border-honey-500 outline-none bg-white">
                </div>
            </div>
            
            <table class="w-full text-left text-sm">
                <thead class="bg-white border-b border-gray-100 text-gray-400 uppercase font-bold text-xs">
                    <tr>
                        <th class="px-6 py-4 w-10">#</th>
                        <th class="px-6 py-4">Họ và tên</th>
                        <th class="px-6 py-4">Ngày sinh</th>
                        <th class="px-6 py-4">Giới tính</th>
                        <th class="px-6 py-4 text-right">Tác vụ</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <?php if($studentCount == 0): ?>
                        <tr>
                            <td colspan="5" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center gap-3">
                                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center text-gray-300 text-3xl">
                                        <i class="ph-duotone ph-student"></i>
                                    </div>
                                    <p class="text-gray-500 font-medium">Lớp này chưa có học sinh nào.</p>
                                    <button class="text-honey-500 font-bold hover:underline text-sm">Thêm học sinh ngay</button>
                                </div>
                            </td>
                        </tr>
                    <?php else: 
                        $stt = 0;
                        while($s = mysqli_fetch_assoc($resStudents)): 
                        $stt++;
                    ?>
                    <tr class="group hover:bg-honey-50/10 transition">
                        <td class="px-6 py-4 text-gray-400 font-mono text-xs"><?php echo $stt; ?></td>
                        <td class="px-6 py-4">
                             <div class="flex items-center gap-3">
                                <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($s['full_name']); ?>&background=random&color=fff&size=40" class="w-10 h-10 rounded-full border-2 border-white shadow-sm">
                                <div>
                                    <p class="font-bold text-gray-800 text-base"><?php echo $s['full_name']; ?></p>
                                    <p class="text-xs text-gray-400"><?php echo $s['email'] ?? 'Chưa cập nhật email'; ?></p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-gray-600 font-medium">
                            <?php echo ($s['dob']) ? date('d/m/Y', strtotime($s['dob'])) : '--/--/----'; ?>
                        </td>
                        <td class="px-6 py-4">
                            <?php if($s['gender'] == 'Male' || $s['gender'] == 'Nam'): ?>
                                <span class="text-blue-600 bg-blue-50 px-2.5 py-1 rounded-md text-xs font-bold border border-blue-100">Nam</span>
                            <?php elseif($s['gender'] == 'Female' || $s['gender'] == 'Nữ'): ?>
                                <span class="text-pink-600 bg-pink-50 px-2.5 py-1 rounded-md text-xs font-bold border border-pink-100">Nữ</span>
                            <?php else: ?>
                                <span class="text-gray-500 text-xs">--</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="?id=<?php echo $id; ?>&remove_std=<?php echo $s['id']; ?>" 
                               onclick="return confirm('Bạn có chắc muốn xóa học sinh <?php echo $s['full_name']; ?> khỏi lớp này không?')" 
                               class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-gray-400 hover:text-red-600 hover:bg-red-50 transition" 
                               title="Xóa khỏi lớp">
                                <i class="ph-bold ph-minus-circle text-lg"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>