<?php
include "connection.php";
include "auth.php";
requireRole(['teacher']);
date_default_timezone_set('Asia/Ho_Chi_Minh');

$class_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$tid = $_SESSION['teacher_id'];

// Check quyền
$class = mysqli_fetch_assoc(mysqli_query($link, "SELECT * FROM classes WHERE id=$class_id AND teacher_id=$tid"));
if(!$class) header("Location: teacher_dashboard.php");

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
    <title>Chi tiết lớp <?php echo $class['name']; ?></title>
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
<body class="bg-dark-100 min-h-screen font-sans text-dark-900">

    <div class="bg-white border-b border-gray-200 sticky top-0 z-20">
        <div class="max-w-7xl mx-auto px-6 pt-6 pb-0">
            <div class="flex items-center gap-2 text-sm text-gray-500 mb-4">
                <a href="teacher_dashboard.php" class="hover:underline">Dashboard</a>
                <i class="ph-bold ph-caret-right text-xs"></i>
                <span class="text-dark-900 font-medium"><?php echo $class['name']; ?></span>
            </div>

            <div class="flex justify-between items-start mb-6">
                <div class="flex gap-4">
                    <div class="w-16 h-16 bg-gradient-to-br from-honey-500 to-yellow-600 rounded-xl flex items-center justify-center text-white text-2xl font-bold shadow-lg shadow-honey-500/30">
                        <?php echo substr($class['name'], 0, 2); ?>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-dark-900"><?php echo $class['name']; ?></h1>
                        <p class="text-gray-500 mt-1 flex items-center gap-2">
                            <i class="ph-fill ph-chalkboard-teacher text-honey-500"></i> GV: <?php echo $_SESSION['full_name']; ?> 
                            <span class="mx-1">•</span> 
                            <span class="bg-green-100 text-green-700 text-xs px-2 py-0.5 rounded-full font-bold">Đang hoạt động</span>
                        </p>
                    </div>
                </div>
                <div class="flex gap-2">
                    <a href="create_exam.php?class_id=<?php echo $class_id; ?>" class="px-4 py-2 bg-honey-500 text-white rounded-lg font-bold text-sm hover:bg-honey-600 shadow-md transition flex items-center gap-2">
                        <i class="ph-bold ph-plus"></i> Tạo bài kiểm tra
                    </a>
                </div>
            </div>

            <div class="flex gap-1">
                <button onclick="switchTab('students')" id="tab-btn-students" class="tab-active px-6 py-3 text-sm font-bold transition-all rounded-t-lg flex items-center gap-2">
                    <i class="ph-bold ph-users"></i> Học sinh <span class="bg-honey-100 text-honey-700 px-1.5 rounded text-xs ml-1"><?php echo $student_count; ?></span>
                </button>
                <button onclick="switchTab('tests')" id="tab-btn-tests" class="tab-inactive px-6 py-3 text-sm font-bold transition-all rounded-t-lg flex items-center gap-2">
                    <i class="ph-bold ph-exam"></i> Bài kiểm tra
                </button>
                <button onclick="switchTab('docs')" id="tab-btn-docs" class="tab-inactive px-6 py-3 text-sm font-bold transition-all rounded-t-lg flex items-center gap-2">
                    <i class="ph-bold ph-files"></i> Tài liệu
                </button>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-6 py-8">
        
        <div id="tab-content-students" class="space-y-4">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <table class="w-full text-left">
                    <thead class="bg-gray-50 border-b border-gray-200 text-xs text-gray-500 uppercase font-semibold">
                        <tr>
                            <th class="px-6 py-4">Họ tên</th>
                            <th class="px-6 py-4">Mã SV / Email</th>
                            <th class="px-6 py-4">Tiến độ bài tập</th>
                            <th class="px-6 py-4 text-right">Hành động</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-sm">
                        <?php while($s = mysqli_fetch_assoc($students)): ?>
                        <tr class="hover:bg-honey-50/30 transition">
                            <td class="px-6 py-4 font-bold text-dark-900 flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold"><?php echo substr($s['full_name'],0,1); ?></div>
                                <?php echo $s['full_name']; ?>
                            </td>
                            <td class="px-6 py-4 text-gray-500"><?php echo $s['username']; ?></td>
                            <td class="px-6 py-4">
                                <div class="w-full bg-gray-200 rounded-full h-2 w-32">
                                    <div class="bg-green-500 h-2 rounded-full" style="width: <?php echo rand(20,90); ?>%"></div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <button class="text-gray-400 hover:text-dark-900"><i class="ph-bold ph-dots-three text-xl"></i></button>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div id="tab-content-tests" class="hidden space-y-6">
            <div class="grid grid-cols-1 gap-4">
                <?php while($e = mysqli_fetch_assoc($exams)): 
                    $now = time();
                    $start = strtotime($e['exam_date']);
                    $end = !empty($e['end_date']) ? strtotime($e['end_date']) : $start + ($e['duration']*60);
                    
                    if($now < $start) {
                        $status_html = '<span class="bg-yellow-100 text-yellow-700 px-2 py-0.5 rounded text-xs font-bold">Sắp diễn ra</span>';
                    } elseif($now > $end) {
                        $status_html = '<span class="bg-gray-100 text-gray-600 px-2 py-0.5 rounded text-xs font-bold">Đã kết thúc</span>';
                    } else {
                        $status_html = '<span class="bg-green-100 text-green-700 px-2 py-0.5 rounded text-xs font-bold animate-pulse">Đang mở</span>';
                    }
                ?>
                <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm hover:border-honey-500 transition flex items-center justify-between group">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-honey-50 text-honey-600 rounded-lg flex items-center justify-center text-2xl">
                            <i class="ph-duotone ph-list-checks"></i>
                        </div>
                        <div>
                            <a href="view_exam.php?id=<?php echo $e['id']; ?>" target="_blank" class="hover:underline">
                                <h3 class="font-bold text-lg text-dark-900 group-hover:text-honey-600 transition"><?php echo $e['exam_title']; ?></h3>
                            </a>
                            <div class="flex items-center gap-3 text-sm text-gray-500 mt-1">
                                <span class="flex items-center gap-1"><i class="ph-bold ph-calendar"></i> <?php echo date('d/m/Y', $start); ?></span>
                                <span class="flex items-center gap-1"><i class="ph-bold ph-clock"></i> <?php echo $e['duration']; ?>p</span>
                                <?php echo $status_html; ?>
                            </div>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="text-2xl font-bold text-dark-900"><?php echo $e['sub_count']; ?> <span class="text-sm text-gray-400 font-normal">/ <?php echo $student_count; ?></span></div>
                        <p class="text-xs text-gray-500 mb-2">Đã nộp bài</p>
                        
                        <div class="flex gap-2 justify-end">
                            <a href="view_exam.php?id=<?php echo $e['id']; ?>" target="_blank" class="px-3 py-1.5 border border-gray-200 rounded-lg text-xs font-bold text-gray-600 hover:bg-gray-50 hover:text-dark-900 flex items-center gap-1 transition">
                                <i class="ph-bold ph-eye"></i> Xem đề
                            </a>
                            <a href="grading.php?exam_id=<?php echo $e['id']; ?>" class="px-3 py-1.5 bg-honey-50 text-honey-600 border border-honey-200 rounded-lg text-xs font-bold hover:bg-honey-500 hover:text-white transition">
                                Chấm bài
                            </a>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
                
                <?php if(mysqli_num_rows($exams) == 0): ?>
                    <div class="text-center py-10 border-2 border-dashed border-gray-300 rounded-xl">
                        <p class="text-gray-500">Chưa có bài kiểm tra nào.</p>
                        <a href="create_exam.php?class_id=<?php echo $class_id; ?>" class="text-honey-600 font-bold hover:underline">Tạo ngay</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div id="tab-content-docs" class="hidden space-y-6">
            <div class="border-2 border-dashed border-gray-300 rounded-xl p-8 flex flex-col items-center justify-center text-center hover:bg-gray-50 hover:border-honey-400 transition cursor-pointer">
                <i class="ph-duotone ph-cloud-arrow-up text-4xl text-gray-400 mb-2"></i>
                <p class="font-bold text-dark-900">Tải lên tài liệu mới</p>
                <p class="text-sm text-gray-500">Kéo thả file hoặc click để chọn (PDF, DOCX)</p>
            </div>
        </div>

    </div>

    <script>
        function switchTab(t) {
            ['students','tests','docs'].forEach(k => {
                document.getElementById(`tab-content-${k}`).classList.add('hidden');
                document.getElementById(`tab-btn-${k}`).className = "tab-inactive px-6 py-3 text-sm font-bold transition-all rounded-t-lg flex items-center gap-2";
            });
            document.getElementById(`tab-content-${t}`).classList.remove('hidden');
            document.getElementById(`tab-btn-${t}`).className = "tab-active px-6 py-3 text-sm font-bold transition-all rounded-t-lg flex items-center gap-2";
        }
    </script>
</body>
</html>