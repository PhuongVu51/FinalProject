<?php
include "../connection.php"; 
include "../auth.php"; 
requireRole(['teacher']);

$tid = $_SESSION['teacher_id'];

// 1. Thống kê cơ bản
$class_count = mysqli_fetch_assoc(mysqli_query($link, "SELECT COUNT(*) as c FROM classes WHERE teacher_id=$tid"))['c'];
$exam_count = mysqli_fetch_assoc(mysqli_query($link, "SELECT COUNT(*) as c FROM exams WHERE teacher_id=$tid"))['c'];
$student_count = mysqli_fetch_assoc(mysqli_query($link, "SELECT COUNT(DISTINCT s.id) as c FROM students s JOIN classes c ON s.class_id = c.id WHERE c.teacher_id=$tid"))['c'];

// 2. Lấy 3 bài kiểm tra sắp tới (Upcoming Exams)
$upcoming_exams = mysqli_query($link, "
    SELECT e.*, c.name as class_name 
    FROM exams e 
    JOIN classes c ON e.class_id = c.id 
    WHERE e.teacher_id=$tid AND e.exam_date >= CURDATE() 
    ORDER BY e.exam_date ASC LIMIT 3
");

// 3. Lấy tin tức mới nhất
$news = mysqli_query($link, "SELECT * FROM news ORDER BY created_at DESC LIMIT 3");
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <title>Dashboard | Teacher Bee</title>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['"Be Vietnam Pro"', 'sans-serif'] },
                    colors: { honey: { 50:'#FFF8E1', 100:'#FFECB3', 400:'#FFCA28', 500:'#FFB300', 600:'#FFA000' } }
                }
            }
        }
    </script>
</head>
<body class="bg-[#F9FAFB] font-sans text-gray-900 flex">

    <?php include "../includes/sidebar.php"; ?>
    
    <div class="flex-1 p-8 ml-[260px]">
        
        <div class="relative bg-gradient-to-r from-honey-500 to-amber-400 rounded-3xl p-10 mb-10 overflow-hidden shadow-lg shadow-honey-500/20 text-white">
            <div class="relative z-10">
                <h1 class="text-3xl font-extrabold mb-2">Xin chào, Thầy/Cô <?php echo $_SESSION['full_name']; ?>! 👋</h1>
                <p class="text-white/90 text-lg font-medium">Chúc thầy/cô một ngày giảng dạy tràn đầy năng lượng và hiệu quả.</p>
                
                <div class="mt-6 flex gap-3">
                    <a href="create_exam.php" class="bg-white text-honey-600 px-5 py-2.5 rounded-xl font-bold text-sm shadow-md hover:bg-gray-50 transition flex items-center gap-2">
                        <i class="ph-bold ph-plus-circle"></i> Tạo bài thi mới
                    </a>
                    <a href="teacher_classes.php" class="bg-honey-600 text-white border border-honey-400 px-5 py-2.5 rounded-xl font-bold text-sm hover:bg-honey-700 transition">
                        Xem lớp học
                    </a>
                </div>
            </div>
            <i class="ph-duotone ph-student absolute -bottom-6 -right-6 text-[180px] text-white opacity-20 transform rotate-12"></i>
            <div class="absolute top-0 right-0 w-64 h-64 bg-white opacity-10 rounded-full blur-3xl -mr-16 -mt-16"></div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <div class="lg:col-span-2 space-y-8">
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm flex flex-col items-center justify-center text-center group hover:-translate-y-1 transition duration-300">
                        <div class="w-14 h-14 bg-blue-50 text-blue-500 rounded-2xl flex items-center justify-center text-3xl mb-3 group-hover:scale-110 transition">
                            <i class="ph-duotone ph-chalkboard-teacher"></i>
                        </div>
                        <h3 class="text-4xl font-bold text-gray-900"><?php echo $class_count; ?></h3>
                        <p class="text-gray-400 text-xs font-bold uppercase tracking-wider mt-1">Lớp phụ trách</p>
                    </div>
                    <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm flex flex-col items-center justify-center text-center group hover:-translate-y-1 transition duration-300">
                        <div class="w-14 h-14 bg-honey-50 text-honey-600 rounded-2xl flex items-center justify-center text-3xl mb-3 group-hover:scale-110 transition">
                            <i class="ph-duotone ph-users-three"></i>
                        </div>
                        <h3 class="text-4xl font-bold text-gray-900"><?php echo $student_count; ?></h3>
                        <p class="text-gray-400 text-xs font-bold uppercase tracking-wider mt-1">Tổng học sinh</p>
                    </div>
                    <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm flex flex-col items-center justify-center text-center group hover:-translate-y-1 transition duration-300">
                        <div class="w-14 h-14 bg-purple-50 text-purple-500 rounded-2xl flex items-center justify-center text-3xl mb-3 group-hover:scale-110 transition">
                            <i class="ph-duotone ph-files"></i>
                        </div>
                        <h3 class="text-4xl font-bold text-gray-900"><?php echo $exam_count; ?></h3>
                        <p class="text-gray-400 text-xs font-bold uppercase tracking-wider mt-1">Bài kiểm tra</p>
                    </div>
                </div>

                <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
                    <div class="px-8 py-6 border-b border-gray-50 flex justify-between items-center">
                        <h3 class="font-bold text-lg text-gray-800 flex items-center gap-2">
                            <i class="ph-duotone ph-clock-countdown text-honey-500"></i> Lịch thi sắp tới
                        </h3>
                        <a href="manage_exams.php" class="text-xs font-bold text-gray-400 hover:text-honey-600">Xem tất cả</a>
                    </div>
                    <div class="divide-y divide-gray-50">
                        <?php if(mysqli_num_rows($upcoming_exams) == 0): ?>
                            <div class="p-8 text-center text-gray-400 text-sm">Hiện không có lịch thi nào sắp tới.</div>
                        <?php else: while($ex = mysqli_fetch_assoc($upcoming_exams)): ?>
                            <div class="px-8 py-5 flex items-center justify-between hover:bg-gray-50 transition group">
                                <div class="flex items-center gap-4">
                                    <div class="bg-honey-100 text-honey-700 w-12 h-12 rounded-xl flex flex-col items-center justify-center font-bold leading-tight">
                                        <span class="text-sm"><?php echo date('M', strtotime($ex['exam_date'])); ?></span>
                                        <span class="text-lg"><?php echo date('d', strtotime($ex['exam_date'])); ?></span>
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-gray-800 group-hover:text-honey-600 transition"><?php echo $ex['exam_title']; ?></h4>
                                        <p class="text-xs text-gray-500 font-medium flex gap-2 mt-1">
                                            <span class="bg-blue-50 text-blue-600 px-2 py-0.5 rounded"><?php echo $ex['class_name']; ?></span>
                                            <span class="flex items-center gap-1"><i class="ph-bold ph-clock"></i> <?php echo date('H:i', strtotime($ex['exam_date'])); ?></span>
                                        </p>
                                    </div>
                                </div>
                                <a href="grading.php?exam_id=<?php echo $ex['id']; ?>" class="w-10 h-10 rounded-full border border-gray-200 flex items-center justify-center text-gray-400 hover:bg-honey-500 hover:text-white hover:border-honey-500 transition">
                                    <i class="ph-bold ph-caret-right"></i>
                                </a>
                            </div>
                        <?php endwhile; endif; ?>
                    </div>
                </div>

            </div>

            <div class="space-y-8">
                
                <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-6">
                    <h3 class="font-bold text-lg text-gray-800 mb-6 flex items-center gap-2">
                        <i class="ph-duotone ph-newspaper text-green-500"></i> Tin tức mới
                    </h3>
                    
                    <div class="space-y-6">
                        <?php while($n = mysqli_fetch_assoc($news)): ?>
                        <a href="../news_detail.php?id=<?php echo $n['id']; ?>" class="block group">
                            <div class="flex gap-4">
                                <div class="w-16 h-16 rounded-xl bg-gray-100 flex items-center justify-center text-2xl text-gray-400 group-hover:bg-honey-100 group-hover:text-honey-500 transition shrink-0">
                                    <i class="ph-duotone ph-article"></i>
                                </div>
                                <div>
                                    <h4 class="font-bold text-gray-800 text-sm leading-snug mb-1 group-hover:text-honey-600 transition line-clamp-2">
                                        <?php echo $n['title']; ?>
                                    </h4>
                                    <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wide">
                                        <?php echo date('d/m/Y', strtotime($n['created_at'])); ?>
                                    </span>
                                </div>
                            </div>
                        </a>
                        <?php endwhile; ?>
                    </div>

                    <a href="../news.php" class="block w-full text-center mt-6 py-3 border border-gray-200 rounded-xl text-xs font-bold text-gray-500 hover:bg-gray-50 hover:text-dark-900 transition">
                        Xem tất cả tin tức
                    </a>
                </div>

                <div class="bg-gradient-to-br from-gray-800 to-gray-900 rounded-3xl p-6 text-white text-center shadow-lg">
                    <i class="ph-duotone ph-rocket-launch text-4xl text-honey-400 mb-3"></i>
                    <h3 class="font-bold text-lg mb-2">Truy cập nhanh</h3>
                    <p class="text-gray-400 text-sm mb-6">Các công cụ quản lý lớp học.</p>
                    <div class="grid grid-cols-2 gap-3">
                        <a href="teacher_classes.php" class="py-2 bg-white/10 hover:bg-white/20 rounded-lg text-sm font-bold transition">Danh sách lớp</a>
                        <a href="manage_exams.php" class="py-2 bg-white/10 hover:bg-white/20 rounded-lg text-sm font-bold transition">Kho đề thi</a>
                    </div>
                </div>

            </div>
        </div>
    </div>
</body>
</html>