<?php
// 1. CẤU HÌNH & KẾT NỐI
include "../connection.php"; 
include "../auth.php"; 
requireRole(['student']);
date_default_timezone_set('Asia/Ho_Chi_Minh');

$sid = $_SESSION['student_id'];
$cid = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Nếu không có ID lớp -> Về danh sách
if($cid == 0) {
    header("Location: student_classes.php"); exit;
}

// 2. CHECK QUYỀN TRUY CẬP
$check_access = mysqli_query($link, "SELECT id FROM students WHERE id=$sid AND class_id=$cid");
if(!$check_access) die("Lỗi SQL Check Access: " . mysqli_error($link));

if(mysqli_num_rows($check_access) == 0){
    echo "<script>alert('Bạn không thuộc lớp học này!'); window.location='student_classes.php';</script>";
    exit;
}

// 3. LẤY THÔNG TIN LỚP & GIÁO VIÊN
$class_sql = "SELECT c.*, u.full_name as teacher_name 
              FROM classes c 
              LEFT JOIN teachers t ON c.teacher_id = t.id 
              LEFT JOIN users u ON t.user_id = u.id 
              WHERE c.id = $cid";
$class_query = mysqli_query($link, $class_sql);
if(!$class_query) die("Lỗi SQL Lấy Lớp: " . mysqli_error($link));
$class = mysqli_fetch_assoc($class_query);

// 4. LẤY DANH SÁCH BÀI THI
$exams = mysqli_query($link, "SELECT * FROM exams WHERE class_id=$cid ORDER BY exam_date DESC");

// 5. LẤY DANH SÁCH THÀNH VIÊN
$members = mysqli_query($link, "
    SELECT u.full_name 
    FROM students s 
    JOIN users u ON s.user_id = u.id 
    WHERE s.class_id = $cid 
    ORDER BY u.full_name ASC
");

$tab = isset($_GET['tab']) ? $_GET['tab'] : 'stream';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <title><?php echo $class['name']; ?> | TeacherBee</title>
    <?php include "../includes/header_config.php"; ?>
</head>
<body class="bg-gray-50 flex font-sans text-gray-900">
    
    <?php include "../includes/sidebar.php"; ?>

    <div class="flex-1 ml-[260px]">
        
        <div class="bg-gray-50 px-8 py-4">
            <a href="student_classes.php" class="inline-flex items-center gap-2 text-gray-500 hover:text-honey-600 font-bold transition group">
                <div class="w-8 h-8 rounded-full bg-white border border-gray-200 flex items-center justify-center shadow-sm group-hover:border-honey-500 group-hover:text-honey-600">
                    <i class="ph-bold ph-arrow-left"></i>
                </div>
                Quay lại danh sách lớp
            </a>
        </div>
        
        <div class="h-48 bg-gradient-to-r from-honey-500 to-orange-400 relative p-8 flex flex-col justify-end text-white">
            <div class="relative z-10">
                <h1 class="text-3xl font-bold mb-1"><?php echo $class['name']; ?></h1>
                <p class="opacity-90 flex items-center gap-2">
                    <i class="ph-bold ph-chalkboard-teacher"></i> GV: <?php echo $class['teacher_name']; ?>
                </p>
            </div>
            <i class="ph-duotone ph-books absolute top-4 right-8 text-[150px] opacity-20 rotate-12"></i>
        </div>

        <div class="bg-white border-b border-gray-200 sticky top-0 z-20 shadow-sm px-8 flex">
            <a href="?id=<?php echo $cid; ?>&tab=stream" 
               class="px-6 py-4 font-bold transition-all border-b-2 <?php echo $tab=='stream' ? 'text-honey-600 border-honey-500 bg-honey-50/50' : 'text-gray-500 border-transparent hover:text-gray-700 hover:bg-gray-50'; ?>">
                Bài tập & Kiểm tra
            </a>
            <a href="?id=<?php echo $cid; ?>&tab=people" 
               class="px-6 py-4 font-bold transition-all border-b-2 <?php echo $tab=='people' ? 'text-honey-600 border-honey-500 bg-honey-50/50' : 'text-gray-500 border-transparent hover:text-gray-700 hover:bg-gray-50'; ?>">
                Mọi người (<?php echo mysqli_num_rows($members); ?>)
            </a>
        </div>

        <div class="p-8 max-w-5xl mx-auto">

            <?php if($tab == 'stream'): ?>
                <div class="space-y-4">
                    <?php if(mysqli_num_rows($exams) == 0): ?>
                        <div class="text-center py-12 bg-white rounded-2xl border border-dashed border-gray-300">
                            <p class="text-gray-400 italic">Chưa có bài kiểm tra nào.</p>
                        </div>
                    <?php else: while($e = mysqli_fetch_assoc($exams)): 
                        $now = time();
                        $start = strtotime($e['exam_date']);
                        $end = !empty($e['end_date']) ? strtotime($e['end_date']) : $start + ($e['duration']*60);
                        
                        $score_q = mysqli_query($link, "SELECT score FROM scores WHERE exam_id={$e['id']} AND student_id=$sid");
                        $has_score = ($score_q) ? mysqli_fetch_assoc($score_q) : null;

                        $can_take = false;
                        if($has_score) {
                            $stt = "<span class='text-green-600 bg-green-50 px-3 py-1 rounded-lg text-xs font-bold'>Đã nộp: {$has_score['score']}đ</span>";
                        } elseif($now < $start) {
                            $stt = "<span class='text-gray-500 bg-gray-100 px-3 py-1 rounded-lg text-xs font-bold'>Chưa mở</span>";
                        } elseif($now > $end) {
                            $stt = "<span class='text-red-600 bg-red-50 px-3 py-1 rounded-lg text-xs font-bold'>Đã đóng</span>";
                        } else {
                            $can_take = true;
                            $stt = "<span class='text-blue-600 bg-blue-50 px-3 py-1 rounded-lg text-xs font-bold animate-pulse'>Đang diễn ra</span>";
                        }
                    ?>
                    <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm hover:border-honey-400 transition group flex items-center gap-4">
                        <div class="w-12 h-12 bg-honey-50 text-honey-600 rounded-full flex items-center justify-center text-xl shrink-0">
                            <i class="ph-fill ph-file-text"></i>
                        </div>
                        <div class="flex-1">
                            <h3 class="font-bold text-gray-800 text-lg group-hover:text-honey-600 transition"><?php echo $e['exam_title']; ?></h3>
                            <div class="flex gap-4 text-xs text-gray-500 mt-1">
                                <span><?php echo date('H:i d/m/Y', $start); ?></span>
                                <span>• <?php echo $e['duration']; ?> phút</span>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="mb-2"><?php echo $stt; ?></div>
                            <?php if($can_take): ?>
                                <a href="take_exam.php?id=<?php echo $e['id']; ?>" class="inline-block px-4 py-2 bg-honey-500 text-white text-xs font-bold rounded-lg hover:bg-honey-600 transition shadow-sm">Làm bài</a>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endwhile; endif; ?>
                </div>

            <?php elseif($tab == 'people'): ?>
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                    <div class="p-6 border-b border-gray-100">
                        <h2 class="text-honey-600 font-bold text-xl mb-4 flex items-center gap-2">
                            <i class="ph-duotone ph-chalkboard-teacher"></i> Giáo viên
                        </h2>
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-honey-100 text-honey-600 flex items-center justify-center font-bold">
                                <?php echo substr($class['teacher_name'] ?? 'T', 0, 1); ?>
                            </div>
                            <div class="font-bold text-gray-800"><?php echo $class['teacher_name']; ?></div>
                        </div>
                    </div>

                    <div class="p-6">
                        <h2 class="text-honey-600 font-bold text-xl mb-4 flex items-center gap-2">
                            <i class="ph-duotone ph-users"></i> Bạn cùng lớp
                            <span class="text-sm bg-honey-50 px-2 py-0.5 rounded-full text-honey-600"><?php echo mysqli_num_rows($members); ?></span>
                        </h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <?php while($mem = mysqli_fetch_assoc($members)): ?>
                            <div class="flex items-center gap-3 p-3 hover:bg-gray-50 rounded-xl transition cursor-default">
                                <div class="w-10 h-10 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center font-bold">
                                    <?php echo substr($mem['full_name'],0,1); ?>
                                </div>
                                <div class="font-bold text-gray-800"><?php echo $mem['full_name']; ?></div>
                            </div>
                            <?php endwhile; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

        </div>
    </div>
</body>
</html>