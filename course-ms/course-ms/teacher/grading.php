<?php
include "../connection.php"; include "../auth.php"; requireRole(['teacher']);
date_default_timezone_set('Asia/Ho_Chi_Minh');

$exam_id = intval($_GET['exam_id']);
$exam = mysqli_fetch_assoc(mysqli_query($link, "SELECT e.*, c.name as class_name FROM exams e JOIN classes c ON e.class_id=c.id WHERE e.id=$exam_id"));
if(!$exam) header("Location: teacher_dashboard.php");

// Logic thời gian
$now = time();
$start = strtotime($exam['exam_date']);
$end = !empty($exam['end_date']) ? strtotime($exam['end_date']) : $start + ($exam['duration']*60);

// Quyền chấm điểm: Chỉ cho phép khi bài thi đã BẮT ĐẦU
$can_grade = ($now >= $start);

// Lấy danh sách HS và điểm
$students = mysqli_query($link, "
    SELECT s.id as sid, u.full_name, s.student_code, sc.score 
    FROM students s 
    JOIN users u ON s.user_id=u.id 
    LEFT JOIN scores sc ON s.id=sc.student_id AND sc.exam_id=$exam_id 
    WHERE s.class_id={$exam['class_id']} 
    ORDER BY u.full_name ASC
");

// Xử lý lưu điểm
if(isset($_POST['save_scores'])) {
    if(!$can_grade) {
        echo "<script>alert('Chưa đến giờ thi, không thể nhập điểm!');</script>";
    } else {
        foreach($_POST['score'] as $sid => $val) {
            if($val === '') continue;
            $score = floatval($val);
            if($score < 0 || $score > 10) continue; // Validate điểm 0-10
            
            $sql = "INSERT INTO scores (exam_id, student_id, score) VALUES ($exam_id, $sid, $score) 
                    ON DUPLICATE KEY UPDATE score=$score";
            mysqli_query($link, $sql);
        }
        echo "<script>alert('Đã lưu bảng điểm!'); window.location.href='grading.php?exam_id=$exam_id';</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <title>Chấm bài: <?php echo $exam['exam_title']; ?> | Teacher Bee</title>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script> tailwind.config = { theme: { extend: { fontFamily: { sans: ['"Be Vietnam Pro"', 'sans-serif'] }, colors: { honey: { 50:'#FFF8E1', 500:'#FFB300', 600:'#FFA000' } } } } } </script>
</head>
<body class="bg-gray-50 flex font-sans text-gray-900">
    
    <?php include "../includes/sidebar.php"; ?>

    <div class="flex-1 p-8 ml-[260px]">
        
        <form method="POST">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4 bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <a href="class_detail.php?id=<?php echo $exam['class_id']; ?>" class="text-xs font-bold text-gray-400 hover:text-honey-600 transition flex items-center gap-1 uppercase tracking-wider">
                        <i class="ph-bold ph-arrow-left"></i> <?php echo $exam['class_name']; ?>
                    </a>
                </div>
                <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-3">
                    <?php echo $exam['exam_title']; ?>
                    <?php if($now < $start): ?>
                        <span class="bg-yellow-100 text-yellow-700 px-2 py-1 rounded text-xs font-bold">Chưa bắt đầu</span>
                    <?php elseif($now > $end): ?>
                        <span class="bg-gray-100 text-gray-600 px-2 py-1 rounded text-xs font-bold">Đã kết thúc</span>
                    <?php else: ?>
                        <span class="bg-green-100 text-green-700 px-2 py-1 rounded text-xs font-bold animate-pulse">● Đang thi</span>
                    <?php endif; ?>
                </h1>
                <p class="text-sm text-gray-500 mt-1 flex items-center gap-4">
                    <span><i class="ph-bold ph-book-open"></i> <?php echo $exam['subject']; ?></span>
                    <span><i class="ph-bold ph-clock"></i> Hạn nộp: <?php echo date('H:i d/m', $end); ?></span>
                </p>
            </div>
            
            <div class="flex gap-2">
                <a href="view_exam.php?id=<?php echo $exam_id; ?>" target="_blank" class="px-4 py-2 border border-gray-200 bg-gray-50 text-gray-700 rounded-xl font-bold text-sm hover:bg-white hover:border-gray-300 transition flex items-center gap-2">
                    <i class="ph-bold ph-eye"></i> Xem đề
                </a>
                <?php if($can_grade): ?>
                    <button type="submit" name="save_scores" class="px-5 py-2 bg-honey-500 text-white rounded-xl font-bold text-sm hover:bg-honey-600 shadow-lg shadow-honey-500/20 transition flex items-center gap-2">
                        <i class="ph-bold ph-floppy-disk"></i> Lưu điểm
                    </button>
                <?php else: ?>
                    <button type="button" disabled class="px-5 py-2 bg-gray-200 text-gray-400 rounded-xl font-bold text-sm cursor-not-allowed">Chưa được chấm</button>
                <?php endif; ?>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <table class="w-full text-left">
                <thead class="bg-gray-50 border-b border-gray-100 text-xs text-gray-500 uppercase font-bold">
                    <tr>
                        <th class="px-6 py-4">Học sinh</th>
                        <th class="px-6 py-4">Mã SV</th>
                        <th class="px-6 py-4 text-center">Trạng thái</th>
                        <th class="px-6 py-4 text-center w-40">Điểm số (0-10)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 text-sm">
                    <?php if(mysqli_num_rows($students) == 0): ?>
                        <tr><td colspan="4" class="text-center py-8 text-gray-400 italic">Lớp này chưa có học sinh nào.</td></tr>
                    <?php else: while($s = mysqli_fetch_assoc($students)): 
                        $has_score = ($s['score'] !== null);
                        // Highlight điểm
                        $score_color = "";
                        if($has_score) {
                            if($s['score'] >= 8) $score_color = "text-green-600 bg-green-50 border-green-200";
                            elseif($s['score'] >= 5) $score_color = "text-yellow-600 bg-yellow-50 border-yellow-200";
                            else $score_color = "text-red-600 bg-red-50 border-red-200";
                        }
                    ?>
                    <tr class="hover:bg-honey-50/20 transition group">
                        <td class="px-6 py-4 font-bold text-gray-800 flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-xs">
                                <?php echo substr($s['full_name'],0,1); ?>
                            </div>
                            <?php echo $s['full_name']; ?>
                        </td>
                        <td class="px-6 py-4 font-mono text-gray-500 text-xs font-bold">#<?php echo $s['student_code']; ?></td>
                        <td class="px-6 py-4 text-center">
                            <?php if($has_score): ?>
                                <span class="text-[10px] font-bold text-green-600 bg-green-100 px-2 py-1 rounded uppercase tracking-wide">Đã chấm</span>
                            <?php else: ?>
                                <span class="text-[10px] font-bold text-gray-400 bg-gray-100 px-2 py-1 rounded uppercase tracking-wide">Chưa chấm</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <input type="number" step="0.1" min="0" max="10" 
                                   name="score[<?php echo $s['sid']; ?>]" 
                                   value="<?php echo $s['score']; ?>" 
                                   placeholder="--"
                                   class="w-20 py-2 text-center border rounded-lg outline-none font-bold text-lg transition focus:ring-2 focus:ring-honey-500 focus:border-honey-500 <?php echo $has_score ? $score_color : 'border-gray-200 text-gray-800'; ?>"
                                   <?php if(!$can_grade) echo 'disabled style="background:#F3F4F6;"'; ?>>
                        </td>
                    </tr>
                    <?php endwhile; endif; ?>
                </tbody>
            </table>
        </div>
        </form>

    </div>
</body>
</html>