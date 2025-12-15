<?php
// SỬA ĐƯỜNG DẪN: Thêm ../
include "../connection.php"; 
include "../auth.php"; 
requireRole(['teacher']);

$tid = $_SESSION['teacher_id'];

// 1. LẤY DANH SÁCH LỚP (Để làm bộ lọc)
$classes = [];
$c_res = mysqli_query($link, "SELECT * FROM classes WHERE teacher_id=$tid");
while($c = mysqli_fetch_assoc($c_res)) $classes[] = $c;

// 2. XỬ LÝ LỌC & TÌM KIẾM
$where_clause = "WHERE e.teacher_id = $tid";

// Lọc theo tên bài thi
$search = "";
if(isset($_GET['q']) && !empty($_GET['q'])){
    $search = mysqli_real_escape_string($link, $_GET['q']);
    $where_clause .= " AND (e.exam_title LIKE '%$search%' OR e.subject LIKE '%$search%')";
}

// Lọc theo lớp
$filter_class = 0;
if(isset($_GET['class_id']) && $_GET['class_id'] > 0){
    $filter_class = intval($_GET['class_id']);
    $where_clause .= " AND e.class_id = $filter_class";
}

// 3. XỬ LÝ XÓA
if(isset($_GET['del'])){
    $eid = intval($_GET['del']);
    // Kiểm tra quyền trước khi xóa
    $check = mysqli_query($link, "SELECT id FROM exams WHERE id=$eid AND teacher_id=$tid");
    if(mysqli_num_rows($check) > 0){
        mysqli_query($link, "DELETE FROM exams WHERE id=$eid");
        header("Location: manage_exams.php"); exit;
    }
}

// 4. TRUY VẤN CHÍNH
$sql = "SELECT e.*, c.name as class_name 
        FROM exams e 
        JOIN classes c ON e.class_id = c.id 
        $where_clause 
        ORDER BY e.exam_date DESC";
$exams = mysqli_query($link, $sql);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <title>Quản lý bài kiểm tra | Teacher Bee</title>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script> tailwind.config = { theme: { extend: { fontFamily: { sans: ['"Be Vietnam Pro"', 'sans-serif'] }, colors: { honey: { 50:'#FFF8E1', 500:'#FFB300', 600:'#FFA000' } } } } } </script>
</head>
<body class="bg-gray-50 flex font-sans text-gray-900">
    <?php include "../includes/sidebar.php"; ?>
    
    <div class="flex-1 p-8 ml-[260px]">
        
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
            <div>
                <h1 class="text-2xl font-bold flex items-center gap-2">
                    <i class="ph-duotone ph-file-text text-honey-500"></i> Quản lý Bài Kiểm Tra
                </h1>
                <p class="text-gray-500 text-sm mt-1">Danh sách tất cả các bài thi đã tạo.</p>
            </div>
            <a href="create_exam.php" class="px-5 py-2.5 bg-honey-500 hover:bg-honey-600 text-white font-bold rounded-xl shadow-lg shadow-honey-500/30 flex items-center gap-2 transition transform hover:-translate-y-0.5">
                <i class="ph-bold ph-plus-circle"></i> Tạo bài thi mới
            </a>
        </div>

        <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 mb-6 flex flex-col md:flex-row gap-4">
            <form class="flex-1 flex gap-4 w-full" method="GET">
                <div class="relative flex-1">
                    <i class="ph-bold ph-magnifying-glass absolute left-3 top-3 text-gray-400"></i>
                    <input type="text" name="q" value="<?php echo htmlspecialchars($search); ?>" placeholder="Tìm theo tên bài hoặc môn..." class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:border-honey-500 outline-none transition">
                </div>
                
                <div class="w-48">
                    <select name="class_id" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:border-honey-500 outline-none cursor-pointer" onchange="this.form.submit()">
                        <option value="0">Tất cả các lớp</option>
                        <?php foreach($classes as $c): ?>
                            <option value="<?php echo $c['id']; ?>" <?php if($filter_class == $c['id']) echo 'selected'; ?>>
                                <?php echo $c['name']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <?php if($search || $filter_class): ?>
                    <a href="manage_exams.php" class="px-4 py-2.5 bg-gray-100 text-gray-500 rounded-xl hover:bg-gray-200 font-bold flex items-center" title="Xóa bộ lọc">
                        <i class="ph-bold ph-arrow-counter-clockwise"></i>
                    </a>
                <?php endif; ?>
            </form>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-gray-50 border-b border-gray-100 text-gray-500 uppercase font-bold text-xs">
                        <tr>
                            <th class="px-6 py-4 w-20">#ID</th>
                            <th class="px-6 py-4">Tên bài thi / Môn</th>
                            <th class="px-6 py-4">Lớp áp dụng</th>
                            <th class="px-6 py-4">Thời gian</th>
                            <th class="px-6 py-4 text-center">Trạng thái</th>
                            <th class="px-6 py-4 text-right">Hành động</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php 
                        if(mysqli_num_rows($exams) == 0):
                            echo "<tr><td colspan='6' class='text-center py-12 text-gray-400 italic'>Không tìm thấy bài thi nào phù hợp.</td></tr>";
                        else: 
                            while($e = mysqli_fetch_assoc($exams)): 
                                // Logic trạng thái
                                $now = time();
                                $start = strtotime($e['exam_date']);
                                $end = !empty($e['end_date']) ? strtotime($e['end_date']) : $start + ($e['duration']*60);
                                
                                if($now < $start) {
                                    $stt_badge = '<span class="px-2 py-1 rounded-md bg-yellow-100 text-yellow-700 text-xs font-bold border border-yellow-200">Sắp diễn ra</span>';
                                } elseif($now > $end) {
                                    $stt_badge = '<span class="px-2 py-1 rounded-md bg-gray-100 text-gray-500 text-xs font-bold border border-gray-200">Đã kết thúc</span>';
                                } else {
                                    $stt_badge = '<span class="px-2 py-1 rounded-md bg-green-100 text-green-700 text-xs font-bold border border-green-200 animate-pulse">● Đang mở</span>';
                                }
                        ?>
                        <tr class="hover:bg-honey-50/20 transition group">
                            <td class="px-6 py-4 font-mono text-gray-400 font-bold">#<?php echo $e['id']; ?></td>
                            
                            <td class="px-6 py-4">
                                <div class="font-bold text-gray-800 text-base mb-0.5"><?php echo $e['exam_title']; ?></div>
                                <div class="text-xs text-gray-500 font-medium bg-gray-100 inline-block px-2 py-0.5 rounded">
                                    <?php echo $e['subject']; ?> • <?php echo ($e['exam_type']=='quiz')?'Trắc nghiệm':($e['exam_type']=='essay'?'Tự luận':'Hỗn hợp'); ?>
                                </div>
                            </td>
                            
                            <td class="px-6 py-4">
                                <a href="class_detail.php?id=<?php echo $e['class_id']; ?>" class="font-bold text-blue-600 hover:underline">
                                    <?php echo $e['class_name']; ?>
                                </a>
                            </td>
                            
                            <td class="px-6 py-4 text-gray-600">
                                <div class="flex items-center gap-1 font-medium"><i class="ph-bold ph-calendar"></i> <?php echo date('d/m/Y', $start); ?></div>
                                <div class="flex items-center gap-1 text-xs mt-1 text-gray-400"><i class="ph-bold ph-clock"></i> <?php echo date('H:i', $start); ?> (<?php echo $e['duration']; ?>p)</div>
                            </td>
                            
                            <td class="px-6 py-4 text-center">
                                <?php echo $stt_badge; ?>
                            </td>
                            
                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end items-center gap-2">
                                    <a href="grading.php?exam_id=<?php echo $e['id']; ?>" class="p-2 bg-white border border-gray-200 rounded-lg text-honey-600 hover:bg-honey-50 hover:border-honey-300 transition" title="Chấm điểm">
                                        <i class="ph-bold ph-star"></i>
                                    </a>
                                    <a href="view_exam.php?id=<?php echo $e['id']; ?>" target="_blank" class="p-2 bg-white border border-gray-200 rounded-lg text-gray-500 hover:text-blue-600 hover:border-blue-300 transition" title="Xem đề">
                                        <i class="ph-bold ph-eye"></i>
                                    </a>
                                    <div class="h-4 w-px bg-gray-300 mx-1"></div>
                                    <a href="edit_exam.php?id=<?php echo $e['id']; ?>" class="text-gray-400 hover:text-blue-600 p-1" title="Sửa">
                                        <i class="ph-bold ph-pencil-simple text-lg"></i>
                                    </a>
                                    <a href="?del=<?php echo $e['id']; ?>" onclick="return confirm('CẢNH BÁO: Xóa bài thi sẽ xóa toàn bộ điểm số của học sinh trong bài này. Bạn có chắc chắn?')" class="text-gray-400 hover:text-red-500 p-1" title="Xóa">
                                        <i class="ph-bold ph-trash text-lg"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; endif; ?>
                    </tbody>
                </table>
            </div>
            
            <?php if(mysqli_num_rows($exams) > 10): ?>
            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 text-center text-xs text-gray-500">
                Hiển thị tất cả kết quả
            </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>