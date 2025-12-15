<?php
// SỬA ĐƯỜNG DẪN: Thêm ../
include "../connection.php"; 
include "../auth.php"; 
requireRole(['teacher']);

$id = intval($_GET['id']);
$tid = $_SESSION['teacher_id'];

// Check quyền
$exam = mysqli_fetch_assoc(mysqli_query($link, "SELECT * FROM exams WHERE id=$id AND teacher_id=$tid"));
if(!$exam) {
    header("Location: manage_exams.php"); // Chuyển về trang quản lý nếu không thấy bài thi
    exit;
}

// Lấy danh sách lớp
$classes = mysqli_query($link, "SELECT * FROM classes WHERE teacher_id=$tid");

if(isset($_POST['save'])) {
    $title = mysqli_real_escape_string($link, $_POST['exam_title']);
    $date = $_POST['exam_date'];
    $dur = intval($_POST['duration']);
    $cid = intval($_POST['class_id']);
    
    // Cập nhật
    if(mysqli_query($link, "UPDATE exams SET exam_title='$title', exam_date='$date', duration=$dur, class_id=$cid WHERE id=$id")){
        echo "<script>alert('Cập nhật thành công!'); window.location='manage_exams.php';</script>";
    } else {
        echo "<script>alert('Lỗi: ".mysqli_error($link)."');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <title>Sửa bài thi | Teacher Bee</title>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script> tailwind.config = { theme: { extend: { fontFamily: { sans: ['"Be Vietnam Pro"', 'sans-serif'] }, colors: { honey: { 500:'#FFB300', 600:'#FFA000' } } } } } </script>
</head>
<body class="bg-gray-50 flex items-center justify-center min-h-screen font-sans p-4">
    
    <div class="bg-white p-8 rounded-3xl shadow-xl w-full max-w-lg border border-gray-100 relative">
        <a href="manage_exams.php" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 transition">
            <i class="ph-bold ph-x text-xl"></i>
        </a>

        <div class="text-center mb-8">
            <div class="w-16 h-16 bg-honey-50 text-honey-600 rounded-full flex items-center justify-center text-3xl mx-auto mb-4">
                <i class="ph-duotone ph-pencil-simple"></i>
            </div>
            <h2 class="text-2xl font-bold text-gray-800">Cập nhật bài thi</h2>
            <p class="text-gray-500 text-sm">Chỉnh sửa thông tin cơ bản của bài kiểm tra</p>
        </div>

        <form method="POST" class="space-y-5">
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1 ml-1">Tên bài kiểm tra</label>
                <div class="relative">
                    <i class="ph-bold ph-text-t absolute left-4 top-3 text-gray-400"></i>
                    <input type="text" name="exam_title" value="<?php echo $exam['exam_title']; ?>" class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-honey-500 focus:bg-white outline-none transition font-medium" required>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1 ml-1">Ngày thi</label>
                    <input type="datetime-local" name="exam_date" value="<?php echo date('Y-m-d\TH:i', strtotime($exam['exam_date'])); ?>" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-honey-500 focus:bg-white outline-none transition text-sm font-medium" required>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1 ml-1">Thời gian (phút)</label>
                    <input type="number" name="duration" value="<?php echo $exam['duration']; ?>" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-honey-500 focus:bg-white outline-none transition font-medium" required>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1 ml-1">Lớp áp dụng</label>
                <div class="relative">
                    <i class="ph-bold ph-chalkboard-teacher absolute left-4 top-3 text-gray-400"></i>
                    <select name="class_id" class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-honey-500 focus:bg-white outline-none transition font-medium appearance-none">
                        <?php while($c = mysqli_fetch_assoc($classes)): ?>
                            <option value="<?php echo $c['id']; ?>" <?php if($c['id']==$exam['class_id']) echo 'selected'; ?>>
                                <?php echo $c['name']; ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                    <i class="ph-bold ph-caret-down absolute right-4 top-3 text-gray-400 pointer-events-none"></i>
                </div>
            </div>

            <div class="pt-4 flex gap-3">
                <a href="manage_exams.php" class="flex-1 py-3 bg-gray-100 text-gray-600 font-bold rounded-xl hover:bg-gray-200 transition text-center">Hủy bỏ</a>
                <button type="submit" name="save" class="flex-1 py-3 bg-honey-500 text-white font-bold rounded-xl hover:bg-honey-600 transition shadow-lg shadow-honey-500/30">Lưu thay đổi</button>
            </div>
        </form>
    </div>

</body>
</html>