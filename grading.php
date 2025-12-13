<?php
include "connection.php"; include "auth.php"; requireRole(['teacher']);
date_default_timezone_set('Asia/Ho_Chi_Minh'); // QUAN TRỌNG

$exam_id = intval($_GET['exam_id']);
$exam = mysqli_fetch_assoc(mysqli_query($link, "SELECT e.*, c.name as class_name FROM exams e JOIN classes c ON e.class_id=c.id WHERE e.id=$exam_id"));
if(!$exam) header("Location: teacher_dashboard.php");

// --- LOGIC THỜI GIAN ---
$now = time();
$start_time = strtotime($exam['exam_date']);
$end_time = !empty($exam['end_date']) ? strtotime($exam['end_date']) : $start_time + ($exam['duration']*60);

// Tính trạng thái
if ($now < $start_time) {
    $status_label = '<span class="bg-yellow-100 text-yellow-700 px-2 py-1 rounded text-xs font-bold">Chưa bắt đầu</span>';
    $can_grade = false; // Chưa diễn ra -> Không cho nhập
    $msg = "Bài thi chưa bắt đầu.";
} elseif ($now > $end_time) {
    $status_label = '<span class="bg-gray-100 text-gray-600 px-2 py-1 rounded text-xs font-bold">Đã kết thúc</span>';
    $can_grade = true; // Đã xong -> ĐƯỢC PHÉP CHẤM
} else {
    $status_label = '<span class="bg-green-100 text-green-700 px-2 py-1 rounded text-xs font-bold animate-pulse">Đang diễn ra</span>';
    $can_grade = true; // Đang thi -> Vẫn cho chấm (để giáo viên chấm những em nộp sớm)
}

// Lấy danh sách HS
$students = mysqli_query($link, "SELECT s.id as sid, u.full_name, s.student_code, sc.score FROM students s JOIN users u ON s.user_id=u.id LEFT JOIN scores sc ON s.id=sc.student_id AND sc.exam_id=$exam_id WHERE s.class_id={$exam['class_id']}");

// XỬ LÝ LƯU ĐIỂM
if(isset($_POST['save_scores'])) {
    if(!$can_grade) {
        echo "<script>alert('Lỗi: Bài thi chưa bắt đầu!');</script>";
    } else {
        foreach($_POST['score'] as $sid => $val) {
            if($val === '') continue; // Nếu để trống thì bỏ qua
            $score = floatval($val);
            // Sử dụng INSERT ... ON DUPLICATE KEY UPDATE để vừa thêm mới vừa cập nhật
            $sql_score = "INSERT INTO scores (exam_id, student_id, score) VALUES ($exam_id, $sid, $score) 
                          ON DUPLICATE KEY UPDATE score=$score";
            mysqli_query($link, $sql_score);
        }
        echo "<script>alert('Đã lưu bảng điểm thành công!'); window.location.href='grading.php?exam_id=$exam_id';</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Chấm bài: <?php echo $exam['exam_title']; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { theme: { extend: { fontFamily: { sans: ['"Be Vietnam Pro"', 'sans-serif'] }, colors: { honey: { 50:'#FFF8E1', 100:'#FFECB3', 500:'#FFB300', 600:'#FFA000' }, dark: { 900:'#2D3436', 500:'#636E72', 100:'#F9FAFB' } } } } }
    </script>
</head>
<body class="bg-dark-100 min-h-screen font-sans text-dark-900">
    <form method="POST">
    <div class="bg-white border-b border-gray-200 px-8 py-4 flex items-center justify-between sticky top-0 z-10 shadow-sm">
        <div>
            <div class="flex items-center gap-2 text-sm text-gray-500 mb-1">
                <a href="class_detail.php?id=<?php echo $exam['class_id']; ?>" class="hover:underline flex items-center gap-1"><i class="ph-bold ph-arrow-left"></i> Quay lại lớp</a>
            </div>
            <h1 class="text-2xl font-bold flex items-center gap-3">
                <?php echo $exam['exam_title']; ?>
                <?php echo $status_label; ?>
            </h1>
            <p class="text-xs text-gray-500 mt-1">Hạn nộp: <?php echo date('H:i d/m/Y', $end_time); ?></p>
        </div>
        
        <div class="flex gap-2">
            <a href="view_exam.php?id=<?php echo $exam_id; ?>" target="_blank" class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-bold hover:bg-gray-50 flex items-center gap-2"><i class="ph-bold ph-eye"></i> Xem đề</a>

            <?php if($can_grade): ?>
                <button type="submit" name="save_scores" class="px-6 py-2 bg-honey-500 hover:bg-honey-600 text-white rounded-lg text-sm font-bold shadow-honey-glow transition flex items-center gap-2">
                    <i class="ph-bold ph-floppy-disk"></i> Lưu điểm
                </button>
            <?php else: ?>
                <button type="button" disabled class="px-6 py-2 bg-gray-300 text-gray-500 rounded-lg text-sm font-bold cursor-not-allowed">Chưa được chấm</button>
            <?php endif; ?>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-6 py-8">
        <?php if(!$can_grade): ?>
            <div class="bg-yellow-50 border border-yellow-200 text-yellow-800 p-4 rounded-xl mb-6 flex items-center gap-2">
                <i class="ph-bold ph-info"></i> <?php echo $msg; ?>
            </div>
        <?php endif; ?>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            <table class="w-full text-left">
                <thead class="bg-gray-50 border-b border-gray-200 text-xs text-gray-500 uppercase font-semibold">
                    <tr>
                        <th class="px-6 py-4">Học sinh</th>
                        <th class="px-6 py-4">Mã SV</th>
                        <th class="px-6 py-4 text-center">Điểm số (Thang 10)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                    <?php while($s = mysqli_fetch_assoc($students)): 
                        $has_score = ($s['score'] !== null);
                    ?>
                    <tr class="hover:bg-honey-50/30 transition">
                        <td class="px-6 py-4 font-bold text-dark-900"><?php echo $s['full_name']; ?></td>
                        <td class="px-6 py-4 text-gray-500 font-mono"><?php echo $s['student_code']; ?></td>
                        <td class="px-6 py-4 text-center">
                            <input type="number" step="0.1" min="0" max="10" 
                                   name="score[<?php echo $s['sid']; ?>]" 
                                   value="<?php echo $s['score']; ?>" 
                                   class="w-24 py-2 text-center border border-gray-300 rounded-lg focus:border-honey-500 outline-none font-bold text-lg disabled:bg-gray-100 disabled:text-gray-400 transition"
                                   <?php if(!$can_grade) echo 'disabled'; ?>>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
    </form>
</body>
</html>