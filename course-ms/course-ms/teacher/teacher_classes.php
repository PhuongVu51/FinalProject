<?php
// SỬA ĐƯỜNG DẪN: Thêm ../
include "../connection.php"; 
include "../auth.php"; 
requireRole(['teacher']);

$tid = $_SESSION['teacher_id'];

$sql = "SELECT c.*, 
        (SELECT COUNT(*) FROM students s WHERE s.class_id = c.id) as student_count,
        (SELECT COUNT(*) FROM exams e WHERE e.class_id = c.id) as exam_count
        FROM classes c WHERE teacher_id = $tid ORDER BY id DESC";
$classes = mysqli_query($link, $sql);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <title>Lớp học | Teacher Bee</title>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script> tailwind.config = { theme: { extend: { fontFamily: { sans: ['"Be Vietnam Pro"', 'sans-serif'] }, colors: { honey: { 500:'#FFB300', 600:'#FFA000' } } } } } </script>
</head>
<body class="bg-gray-50 flex font-sans text-gray-900">
    <?php include "../includes/sidebar.php"; ?>
    
    <div class="flex-1 p-8 ml-[260px]">
        <h1 class="text-2xl font-bold mb-8">Lớp Học Phụ Trách</h1>

        <?php if(mysqli_num_rows($classes) == 0): ?>
            <div class="text-center py-12 text-gray-400 bg-white rounded-2xl border border-dashed border-gray-300">
                <i class="ph-duotone ph-chalkboard-teacher text-4xl mb-2"></i>
                <p>Bạn chưa được phân công lớp nào.</p>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php while($c = mysqli_fetch_assoc($classes)): ?>
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden hover:shadow-md transition group">
                    <div class="bg-gradient-to-r from-honey-500 to-yellow-400 p-5">
                        <h3 class="text-white text-xl font-bold truncate"><?php echo $c['name']; ?></h3>
                        <span class="text-white/80 text-xs font-bold uppercase tracking-wider">Học kỳ 1 - 2025</span>
                    </div>
                    
                    <div class="p-6">
                        <div class="flex items-center gap-4 text-sm text-gray-600 mb-6">
                            <span class="flex items-center gap-1 font-bold"><i class="ph-bold ph-users"></i> <?php echo $c['student_count']; ?> HS</span>
                            <span class="w-1 h-1 bg-gray-300 rounded-full"></span>
                            <span class="flex items-center gap-1"><i class="ph-bold ph-file-text"></i> <?php echo $c['exam_count']; ?> bài kiểm tra</span>
                        </div>
                        
                        <a href="class_detail.php?id=<?php echo $c['id']; ?>" class="block w-full py-2.5 bg-honey-500 hover:bg-honey-600 text-white font-bold text-center rounded-xl transition shadow-lg shadow-honey-500/20">
                            Vào lớp
                        </a>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>