<?php
// SỬA ĐƯỜNG DẪN: Thêm ../ để lấy file từ thư mục gốc
include "../connection.php"; 
include "../auth.php"; 
requireRole(['teacher']);
date_default_timezone_set('Asia/Ho_Chi_Minh');

$class_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$tid = $_SESSION['teacher_id'];

// 1. Kiểm tra quyền sở hữu lớp
$class = mysqli_fetch_assoc(mysqli_query($link, "SELECT * FROM classes WHERE id=$class_id AND teacher_id=$tid"));
if(!$class) header("Location: teacher_classes.php");

// 2. Xử lý: Xóa học sinh khỏi lớp (Hành động)
if(isset($_GET['remove_std'])){
    $sid = intval($_GET['remove_std']);
    // Set class_id = NULL để đuổi học sinh khỏi lớp này
    mysqli_query($link, "UPDATE students SET class_id=NULL WHERE id=$sid");
    header("Location: class_detail.php?id=$class_id"); exit;
}

// 3. Lấy dữ liệu
// Số lượng HS
$student_count = mysqli_fetch_assoc(mysqli_query($link, "SELECT COUNT(*) as c FROM students WHERE class_id=$class_id"))['c'];
// Danh sách HS
$students = mysqli_query($link, "SELECT s.*, u.full_name, u.username FROM students s JOIN users u ON s.user_id=u.id WHERE s.class_id=$class_id");
// Danh sách bài thi
$exams = mysqli_query($link, "SELECT e.*, (SELECT COUNT(DISTINCT student_id) FROM scores WHERE exam_id = e.id) as sub_count FROM exams e WHERE class_id=$class_id ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Lớp <?php echo $class['name']; ?> | TeacherBee</title>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['"Be Vietnam Pro"', 'sans-serif'] },
                    colors: {
                        honey: { 50:'#FFF8E1', 100:'#FFECB3', 500:'#FFB300', 600:'#FFA000' },
                        dark: { 900:'#2D3436', 800:'#636E72', 100:'#F9FAFB' }
                    }
                }
            }
        }
    </script>
    <style>
        .tab-active { @apply border-b-2 border-honey-500 text-honey-600 bg-honey-50/50; }
        .tab-inactive { @apply border-transparent text-gray-500 hover:text-dark-900 hover:bg-gray-50; }
    </style>
</head>
<body class="bg-dark-100 min-h-screen font-sans text-dark-900 flex">

    <?php include "../includes/sidebar.php"; ?>

    <div class="flex-1 p-8 ml-[260px]">
        
        <div class="mb-6">
            <a href="teacher_classes.php" class="inline-flex items-center gap-2 text-gray-500 hover:text-honey-600 font-bold transition">
                <i class="ph-bold ph-arrow-left"></i> Quay lại danh sách lớp
            </a>
        </div>

        <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100 mb-8 relative overflow-hidden">
            <div class="absolute top-0 right-0 p-8 opacity-10 pointer-events-none">
                <i class="ph-duotone ph-chalkboard-teacher text-9xl text-honey-500"></i>
            </div>
            
            <div class="flex justify-between items-start relative z-10">
                <div class="flex gap-5">
                    <div class="w-20 h-20 bg-gradient-to-br from-honey-500 to-yellow-600 rounded-2xl flex items-center justify-center text-white text-3xl font-bold shadow-lg shadow-honey-500/30">
                        <?php echo substr($class['name'], 0, 2); ?>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 mb-2"><?php echo $class['name']; ?></h1>
                        <p class="text-gray-500 text-sm flex items-center gap-3">
                            <span class="flex items-center gap-1"><i class="ph-bold ph-users"></i> <?php echo $student_count; ?> Học sinh</span>
                            <span class="w-1 h-1 bg-gray-300 rounded-full"></span>
                            <span class="flex items-center gap-1"><i class="ph-bold ph-file-text"></i> <?php echo mysqli_num_rows($exams); ?> Bài kiểm tra</span>
                        </p>
                    </div>
                </div>
                <div>
                    <a href="create_exam.php?class_id=<?php echo $class_id; ?>" class="px-5 py-2.5 bg-honey-500 hover:bg-honey-600 text-white rounded-xl font-bold text-sm shadow-lg shadow-honey-500/20 transition flex items-center gap-2">
                        <i class="ph-bold ph-plus-circle"></i> Tạo bài kiểm tra
                    </a>
                </div>
            </div>

            <div class="flex gap-1 mt-8 border-b border-gray-100">
                <button onclick="switchTab('students')" id="tab-btn-students" class="tab-active px-6 py-3 text-sm font-bold transition-all rounded-t-lg flex items-center gap-2">
                    <i class="ph-bold ph-users"></i> Học sinh
                </button>
                <button onclick="switchTab('tests')" id="tab-btn-tests" class="tab-inactive px-6 py-3 text-sm font-bold transition-all rounded-t-lg flex items-center gap-2">
                    <i class="ph-bold ph-exam"></i> Bài kiểm tra
                </button>
            </div>
        </div>

        <div id="tab-content-students">
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                <?php if(mysqli_num_rows($students) == 0): ?>
                    <div class="text-center py-12">
                        <div class="bg-gray-50 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-3 text-gray-400"><i class="ph-duotone ph-user-minus text-3xl"></i></div>
                        <p class="text-gray-500 text-sm">Lớp chưa có học sinh nào.</p>
                    </div>
                <?php else: ?>
                    <table class="w-full text-left">
                        <thead class="bg-gray-50 border-b border-gray-100 text-xs text-gray-500 uppercase font-semibold">
                            <tr>
                                <th class="px-6 py-4">Mã SV</th>
                                <th class="px-6 py-4">Họ Tên</th>
                                <th class="px-6 py-4">Tài khoản</th>
                                <th class="px-6 py-4 text-right">Hành động</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 text-sm">
                            <?php while($s = mysqli_fetch_assoc($students)): ?>
                            <tr class="hover:bg-honey-50/20 transition group">
                                <td class="px-6 py-4 font-mono text-honey-600 font-bold">#<?php echo $s['student_code']; ?></td>
                                <td class="px-6 py-4 font-bold text-gray-800 flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold">
                                        <?php echo substr($s['full_name'],0,1); ?>
                                    </div>
                                    <?php echo $s['full_name']; ?>
                                </td>
                                <td class="px-6 py-4 text-gray-500"><?php echo $s['username']; ?></td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex justify-end gap-2">
                                        <button class="p-2 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition" title="Xem chi tiết">
                                            <i class="ph-bold ph-eye"></i>
                                        </button>
                                        <a href="?id=<?php echo $class_id; ?>&remove_std=<?php echo $s['id']; ?>" onclick="return confirm('Bạn chắc chắn muốn xóa học sinh <?php echo $s['full_name']; ?> khỏi lớp này?')" class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition" title="Xóa khỏi lớp">
                                            <i class="ph-bold ph-user-minus"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>

        <div id="tab-content-tests" class="hidden">
            <?php if(mysqli_num_rows($exams) == 0): ?>
                <div class="text-center py-12 bg-white rounded-3xl border border-dashed border-gray-300">
                    <i class="ph-duotone ph-file-dashed text-4xl text-gray-300 mb-2"></i>
                    <p class="text-gray-500 text-sm mb-4">Chưa có bài kiểm tra nào.</p>
                    <a href="create_exam.php?class_id=<?php echo $class_id; ?>" class="text-honey-600 font-bold hover:underline">Tạo ngay</a>
                </div>
            <?php else: ?>
                <div class="grid grid-cols-1 gap-4">
                    <?php while($e = mysqli_fetch_assoc($exams)): 
                        $now = time();
                        $start = strtotime($e['exam_date']);
                        $end = !empty($e['end_date']) ? strtotime($e['end_date']) : $start + ($e['duration']*60);
                        
                        // Badge trạng thái
                        if($now < $start) {
                            $stt = '<span class="bg-yellow-100 text-yellow-700 px-2 py-1 rounded text-xs font-bold">Sắp diễn ra</span>';
                        } elseif($now > $end) {
                            $stt = '<span class="bg-gray-100 text-gray-600 px-2 py-1 rounded text-xs font-bold">Đã kết thúc</span>';
                        } else {
                            $stt = '<span class="bg-green-100 text-green-700 px-2 py-1 rounded text-xs font-bold animate-pulse">Đang mở</span>';
                        }
                    ?>
                    <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm hover:border-honey-400 transition flex items-center justify-between group">
                        <div class="flex items-center gap-5">
                            <div class="w-14 h-14 bg-gray-50 text-gray-400 group-hover:bg-honey-50 group-hover:text-honey-600 rounded-xl flex items-center justify-center text-3xl transition">
                                <i class="ph-duotone ph-file-text"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-lg text-gray-900 group-hover:text-honey-600 transition mb-1"><?php echo $e['exam_title']; ?></h3>
                                <div class="flex items-center gap-3 text-xs text-gray-500 font-medium">
                                    <span class="flex items-center gap-1"><i class="ph-bold ph-calendar"></i> <?php echo date('d/m/Y H:i', $start); ?></span>
                                    <span class="flex items-center gap-1"><i class="ph-bold ph-clock"></i> <?php echo $e['duration']; ?> phút</span>
                                    <?php echo $stt; ?>
                                </div>
                            </div>
                        </div>
                        
                        <div class="text-right flex flex-col items-end gap-2">
                            <div class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Đã nộp: <span class="text-gray-900 text-base"><?php echo $e['sub_count']; ?></span>/<?php echo $student_count; ?></div>
                            <div class="flex gap-2">
                                <a href="view_exam.php?id=<?php echo $e['id']; ?>" class="px-4 py-2 border border-gray-200 rounded-lg text-xs font-bold text-gray-600 hover:bg-gray-50 hover:text-dark-900 transition">Xem đề</a>
                                <a href="grading.php?exam_id=<?php echo $e['id']; ?>" class="px-4 py-2 bg-honey-500 text-white rounded-lg text-xs font-bold hover:bg-honey-600 shadow-lg shadow-honey-500/20 transition">Chấm điểm</a>
                            </div>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>
            <?php endif; ?>
        </div>

    </div>

    <script>
        function switchTab(t) {
            ['students','tests'].forEach(k => {
                document.getElementById(`tab-content-${k}`).classList.add('hidden');
                document.getElementById(`tab-btn-${k}`).className = "tab-inactive px-6 py-3 text-sm font-bold transition-all rounded-t-lg flex items-center gap-2 border-b-2 border-transparent text-gray-500";
            });
            document.getElementById(`tab-content-${t}`).classList.remove('hidden');
            document.getElementById(`tab-btn-${t}`).className = "tab-active px-6 py-3 text-sm font-bold transition-all rounded-t-lg flex items-center gap-2 border-b-2 border-honey-500 text-honey-600 bg-honey-50/50";
        }
        // Mặc định hiện tab học sinh
        switchTab('students');
    </script>
</body>
</html>