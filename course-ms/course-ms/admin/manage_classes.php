<?php
include "../connection.php"; 
include "../auth.php"; 
requireRole(['admin']);

function runQuery($link, $sql) {
    $res = mysqli_query($link, $sql);
    if(!$res) die("Lỗi SQL: " . mysqli_error($link));
    return $res;
}

// --- XỬ LÝ LOGIC ---

// 1. Lấy thống kê (Để lấp đầy khoảng trống phía trên)
$stats = [
    'classes' => mysqli_fetch_assoc(runQuery($link, "SELECT COUNT(*) as c FROM classes"))['c'],
    'students' => mysqli_fetch_assoc(runQuery($link, "SELECT COUNT(*) as c FROM students WHERE class_id IS NOT NULL"))['c'],
    'no_teacher' => mysqli_fetch_assoc(runQuery($link, "SELECT COUNT(*) as c FROM classes WHERE teacher_id IS NULL"))['c']
];

// 2. Lấy danh sách GV
$teachers = [];
$tRes = runQuery($link, "SELECT t.id, u.full_name FROM teachers t JOIN users u ON t.user_id=u.id ORDER BY u.full_name");
while($t = mysqli_fetch_assoc($tRes)) $teachers[] = $t;

// 3. Xử lý Thêm lớp
if(isset($_POST['add'])){
    $name = mysqli_real_escape_string($link, $_POST['name']);
    $tid = intval($_POST['tid']);
    $desc = mysqli_real_escape_string($link, $_POST['description'] ?? ''); // Thêm mô tả cho form đỡ trống
    $tsql = ($tid > 0) ? $tid : "NULL";
    
    // Giả sử bảng classes có cột description, nếu chưa có bạn có thể bỏ dòng này hoặc alter table
    // Tạm thời mình chỉ insert name và teacher_id như cũ
    runQuery($link, "INSERT INTO classes (name, teacher_id) VALUES ('$name', $tsql)");
    header("Location: manage_classes.php"); exit;
}

// 4. Xử lý Xóa lớp
if(isset($_GET['del'])){
    $cid = intval($_GET['del']);
    runQuery($link, "UPDATE students SET class_id=NULL WHERE class_id=$cid");
    runQuery($link, "DELETE FROM classes WHERE id=$cid");
    header("Location: manage_classes.php"); exit;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <title>Quản lý Lớp học | Admin</title>
    <?php include "../includes/header_config.php"; ?>
</head>
<body class="bg-gray-50 flex font-sans text-gray-900">
    
    <?php include "../includes/sidebar.php"; ?>
    
    <div class="flex-1 p-8 ml-[260px]">
        <div class="flex justify-between items-end mb-8">
            <div>
                <h1 class="text-2xl font-bold flex items-center gap-3">
                    <i class="ph-duotone ph-chalkboard-teacher text-honey-500 text-3xl"></i> Quản lý Lớp học
                </h1>
                <p class="text-gray-500 text-sm mt-1">Quản lý danh sách lớp, phân công giáo viên và theo dõi sĩ số.</p>
            </div>
            <div class="text-right">
                <span class="text-sm font-medium text-gray-500">Hôm nay: <?php echo date('d/m/Y'); ?></span>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-4 hover:shadow-md transition">
                <div class="w-12 h-12 rounded-full bg-honey-100 text-honey-600 flex items-center justify-center text-xl">
                    <i class="ph-fill ph-books"></i>
                </div>
                <div>
                    <p class="text-gray-500 text-xs font-bold uppercase">Tổng số lớp</p>
                    <p class="text-2xl font-bold text-gray-800"><?php echo $stats['classes']; ?></p>
                </div>
            </div>
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-4 hover:shadow-md transition">
                <div class="w-12 h-12 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xl">
                    <i class="ph-fill ph-student"></i>
                </div>
                <div>
                    <p class="text-gray-500 text-xs font-bold uppercase">Học sinh đã xếp lớp</p>
                    <p class="text-2xl font-bold text-gray-800"><?php echo $stats['students']; ?></p>
                </div>
            </div>
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-4 hover:shadow-md transition">
                <div class="w-12 h-12 rounded-full bg-red-100 text-red-600 flex items-center justify-center text-xl">
                    <i class="ph-fill ph-warning-circle"></i>
                </div>
                <div>
                    <p class="text-gray-500 text-xs font-bold uppercase">Lớp thiếu GVCN</p>
                    <p class="text-2xl font-bold text-gray-800"><?php echo $stats['no_teacher']; ?></p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            
            <div class="lg:col-span-4 h-fit sticky top-8">
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 relative overflow-hidden">
                    <div class="absolute -top-10 -right-10 w-32 h-32 bg-honey-50 rounded-full blur-2xl opacity-60"></div>

                    <h3 class="font-bold text-lg mb-6 text-gray-800 flex items-center gap-2 relative z-10">
                        <span class="w-8 h-8 rounded-lg bg-honey-100 text-honey-600 flex items-center justify-center text-sm">
                            <i class="ph-bold ph-plus"></i>
                        </span>
                        Tạo lớp mới
                    </h3>
                    
                    <form method="post" class="space-y-5 relative z-10">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Tên lớp học <span class="text-red-500">*</span></label>
                            <input type="text" name="name" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:border-honey-500 focus:ring-2 focus:ring-honey-100 outline-none transition font-medium" placeholder="VD: Toán 10A..." required>
                        </div>
                        
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Mô tả (Tùy chọn)</label>
                            <textarea name="description" rows="2" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:border-honey-500 focus:ring-2 focus:ring-honey-100 outline-none transition resize-none" placeholder="Nhập ghi chú về lớp học..."></textarea>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Giáo viên chủ nhiệm</label>
                            <div class="relative">
                                <select name="tid" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:border-honey-500 focus:ring-2 focus:ring-honey-100 outline-none appearance-none transition cursor-pointer">
                                    <option value="0">-- Chọn giáo viên --</option>
                                    <?php foreach($teachers as $t): ?>
                                        <option value="<?php echo $t['id']; ?>"><?php echo $t['full_name']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="absolute inset-y-0 right-4 flex items-center pointer-events-none text-gray-400">
                                    <i class="ph-bold ph-caret-down"></i>
                                </div>
                            </div>
                        </div>

                        <div class="pt-2">
                            <button name="add" class="w-full py-3.5 bg-honey-500 text-white font-bold rounded-xl hover:bg-honey-600 hover:-translate-y-1 transition-all shadow-lg shadow-honey-500/20 flex items-center justify-center gap-2">
                                <i class="ph-bold ph-check-circle"></i> Xác nhận tạo lớp
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="lg:col-span-8">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 flex flex-col h-full">
                    
                    <div class="p-5 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <h3 class="font-bold text-gray-800 whitespace-nowrap">Danh sách lớp học</h3>
                        
                        <div class="flex gap-2 w-full sm:w-auto">
                            <div class="relative flex-1 sm:w-64">
                                <i class="ph-bold ph-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                                <input type="text" placeholder="Tìm tên lớp..." class="w-full pl-9 pr-4 py-2 border border-gray-200 rounded-lg text-sm focus:border-honey-500 outline-none">
                            </div>
                            <button class="px-3 py-2 border border-gray-200 rounded-lg text-gray-500 hover:bg-gray-50">
                                <i class="ph-bold ph-faders"></i>
                            </button>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead class="bg-gray-50 text-gray-500 font-bold text-xs uppercase tracking-wider">
                                <tr>
                                    <th class="px-6 py-4 rounded-tl-lg">Thông tin Lớp</th>
                                    <th class="px-6 py-4">GVCN</th>
                                    <th class="px-6 py-4 text-center">Sĩ số</th>
                                    <th class="px-6 py-4 text-right rounded-tr-lg">Tác vụ</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <?php 
                                $sql = "SELECT c.*, u.full_name, 
                                        (SELECT COUNT(*) FROM students WHERE class_id=c.id) as std_count 
                                        FROM classes c 
                                        LEFT JOIN teachers t ON c.teacher_id=t.id 
                                        LEFT JOIN users u ON t.user_id=u.id 
                                        ORDER BY c.id DESC";
                                $res = runQuery($link, $sql);
                                
                                // State: Empty List
                                if(mysqli_num_rows($res) == 0): 
                                ?>
                                    <tr>
                                        <td colspan="4" class="px-6 py-12 text-center text-gray-400">
                                            <div class="flex flex-col items-center gap-2">
                                                <i class="ph-duotone ph-folder-open text-4xl text-gray-300"></i>
                                                <span>Chưa có lớp học nào được tạo.</span>
                                            </div>
                                        </td>
                                    </tr>
                                <?php else: while($r = mysqli_fetch_assoc($res)): ?>
                                <tr class="group hover:bg-honey-50/10 transition">
                                <td class="px-6 py-4">
    <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-lg bg-gray-100 text-gray-500 flex items-center justify-center font-bold text-lg border border-gray-200 group-hover:border-honey-200 group-hover:bg-honey-100 group-hover:text-honey-600 transition">
            <?php echo substr($r['name'], 0, 1); ?>
        </div>
        
        <div>
            <a href="view_class.php?id=<?php echo $r['id']; ?>" class="font-bold text-gray-800 text-base hover:text-honey-500 hover:underline transition">
                <?php echo $r['name']; ?>
            </a>
            <div class="text-xs text-gray-400">ID: #<?php echo str_pad($r['id'], 3, '0', STR_PAD_LEFT); ?></div>
        </div>
    </div>
</td>
                                    <td class="px-6 py-4">
                                        <?php if($r['full_name']): ?>
                                            <div class="flex items-center gap-2">
                                                <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($r['full_name']); ?>&background=random&size=32" class="w-6 h-6 rounded-full" alt="Avatar">
                                                <span class="font-medium text-gray-700"><?php echo $r['full_name']; ?></span>
                                            </div>
                                        <?php else: ?>
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-red-50 text-red-600 text-xs font-bold border border-red-100">
                                                <i class="ph-bold ph-warning"></i> Trống
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <div class="inline-block relative">
                                            <span class="px-3 py-1 rounded-full text-xs font-bold <?php echo ($r['std_count'] > 0) ? 'bg-green-50 text-green-600 border border-green-100' : 'bg-gray-100 text-gray-500'; ?>">
                                                <?php echo $r['std_count']; ?> HS
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex justify-end gap-1 opacity-60 group-hover:opacity-100 transition">
                                            <a href="edit_class.php?id=<?php echo $r['id']; ?>" class="w-8 h-8 rounded-lg text-gray-400 hover:bg-blue-50 hover:text-blue-600 flex items-center justify-center transition" title="Chỉnh sửa">
                                                <i class="ph-bold ph-pencil-simple"></i>
                                            </a>
                                            <a href="?del=<?php echo $r['id']; ?>" onclick="return confirm('Xóa lớp này?')" class="w-8 h-8 rounded-lg text-gray-400 hover:bg-red-50 hover:text-red-600 flex items-center justify-center transition" title="Xóa">
                                                <i class="ph-bold ph-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endwhile; endif; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="p-4 border-t border-gray-100 flex justify-center">
                        <span class="text-xs text-gray-400">Hiển thị toàn bộ kết quả</span>
                    </div>

                </div>
            </div>
        </div>
    </div>
</body>
</html>