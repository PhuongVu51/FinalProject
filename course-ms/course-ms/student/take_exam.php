<?php
// 1. KẾT NỐI & AUTH
include "../connection.php"; 
include "../auth.php"; 
requireRole(['student']);
date_default_timezone_set('Asia/Ho_Chi_Minh');

$sid = $_SESSION['student_id'];
$eid = isset($_GET['id']) ? intval($_GET['id']) : 0;

// 2. CHECK BÀI THI
$exam_res = mysqli_query($link, "SELECT * FROM exams WHERE id=$eid");
if(!$exam_res || mysqli_num_rows($exam_res) == 0) {
    echo "<script>alert('Bài thi không tồn tại!'); window.location='student_classes.php';</script>"; exit;
}
$exam = mysqli_fetch_assoc($exam_res);

// 3. CHECK THỜI GIAN
$now = time();
$start = strtotime($exam['exam_date']);
$end = !empty($exam['end_date']) ? strtotime($exam['end_date']) : $start + ($exam['duration']*60);

if($now < $start) die("<h1>Chưa đến giờ làm bài!</h1>");
if($now > $end) die("<h1>Bài thi đã kết thúc!</h1>");

// 4. CHECK ĐÃ LÀM CHƯA
$check = mysqli_query($link, "SELECT id FROM scores WHERE exam_id=$eid AND student_id=$sid");
if(mysqli_num_rows($check) > 0){
    echo "<script>alert('Bạn đã nộp bài rồi!'); window.location='student_class_detail.php?id={$exam['class_id']}';</script>"; exit;
}

// 5. XỬ LÝ NỘP BÀI
if(isset($_POST['submit_exam'])){
    $total = 0; $correct = 0;
    $qs = mysqli_query($link, "SELECT id, question_type FROM exam_questions WHERE exam_id=$eid");
    while($q = mysqli_fetch_assoc($qs)){
        if($q['question_type'] == 'multiple_choice'){
            $total++;
            $right = mysqli_fetch_assoc(mysqli_query($link, "SELECT id FROM exam_options WHERE question_id={$q['id']} AND is_correct=1"));
            if($right && isset($_POST['q_'.$q['id']]) && $_POST['q_'.$q['id']] == $right['id']) $correct++;
        }
    }
    $score = ($total > 0) ? round(($correct/$total)*10, 2) : 0;
    mysqli_query($link, "INSERT INTO scores (exam_id, student_id, score) VALUES ($eid, $sid, $score)");
    echo "<script>alert('Nộp bài thành công! Điểm số: $score'); window.location='student_class_detail.php?id={$exam['class_id']}';</script>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <title>Làm bài: <?php echo $exam['exam_title']; ?></title>
    <?php include "../includes/header_config.php"; ?>
    <script>
        function startTimer(duration, display) {
            var timer = duration, m, s;
            var interval = setInterval(function () {
                m = parseInt(timer / 60, 10); s = parseInt(timer % 60, 10);
                m = m < 10 ? "0" + m : m; s = s < 10 ? "0" + s : s;
                display.textContent = m + ":" + s;
                if (--timer < 0) {
                    clearInterval(interval);
                    alert("Hết giờ! Hệ thống đang tự nộp bài...");
                    document.getElementById("examForm").submit();
                }
            }, 1000);
        }
        window.onload = function () {
            var remaining = <?php echo $end - time(); ?>;
            var display = document.querySelector('#time');
            if(remaining > 0) startTimer(remaining, display);
            else { alert("Hết giờ!"); window.location.href="student_classes.php"; }
        };
    </script>
</head>
<body class="bg-gray-50 font-sans text-gray-900 pb-24">
    
    <div class="fixed top-0 w-full bg-white shadow-sm z-50 h-16 flex items-center justify-between px-4 md:px-8 border-b border-gray-200">
        <div class="font-bold text-lg truncate"><?php echo $exam['exam_title']; ?></div>
        
        <?php 
        // Đếm số câu hỏi trước để quyết định hiển thị nút nộp
        $q_check = mysqli_query($link, "SELECT count(*) as total FROM exam_questions WHERE exam_id=$eid");
        $q_count = mysqli_fetch_assoc($q_check)['total'];
        if($q_count > 0): 
        ?>
        <div class="flex items-center gap-4">
            <div class="flex items-center gap-2 bg-red-50 text-red-600 px-3 py-1.5 rounded-full font-mono font-bold border border-red-100">
                <i class="ph-bold ph-clock"></i> <span id="time">--:--</span>
            </div>
            <button onclick="if(confirm('Nộp bài ngay?')) document.getElementById('examForm').submit()" class="bg-honey-500 hover:bg-honey-600 text-white px-4 py-2 rounded-lg font-bold shadow-sm transition">Nộp bài</button>
        </div>
        <?php endif; ?>
    </div>

    <div class="max-w-3xl mx-auto mt-24 px-4">
        <form method="POST" id="examForm">
            <?php 
            $qs = mysqli_query($link, "SELECT * FROM exam_questions WHERE exam_id=$eid ORDER BY id ASC");
            if(mysqli_num_rows($qs) == 0): 
            ?>
                <div class="text-center py-20">
                    <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4 text-gray-400 text-4xl">
                        <i class="ph-duotone ph-file-x"></i>
                    </div>
                    <h2 class="text-xl font-bold text-gray-700">Đề thi chưa có câu hỏi</h2>
                    <p class="text-gray-500 mt-2 mb-6">Giáo viên chưa cập nhật câu hỏi cho bài kiểm tra này.</p>
                    <a href="student_class_detail.php?id=<?php echo $exam['class_id']; ?>" class="px-6 py-2 bg-gray-200 text-gray-700 font-bold rounded-lg hover:bg-gray-300">Quay lại</a>
                </div>
            <?php else: 
                $i = 1;
                while($q = mysqli_fetch_assoc($qs)):
            ?>
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 mb-6">
                    <div class="font-bold text-gray-800 mb-4 flex gap-2">
                        <span class="text-honey-600 whitespace-nowrap">Câu <?php echo $i++; ?>:</span> 
                        <span><?php echo nl2br($q['question_text']); ?></span>
                    </div>
                    
                    <?php if($q['question_type'] == 'multiple_choice'): 
                        $opts = mysqli_query($link, "SELECT * FROM exam_options WHERE question_id={$q['id']} ORDER BY id ASC");
                    ?>
                        <div class="space-y-3">
                            <?php while($opt = mysqli_fetch_assoc($opts)): ?>
                            <label class="flex items-center gap-3 p-3 border border-gray-200 rounded-xl cursor-pointer hover:bg-honey-50 hover:border-honey-300 transition group">
                                <input type="radio" name="q_<?php echo $q['id']; ?>" value="<?php echo $opt['id']; ?>" class="w-5 h-5 text-honey-600 focus:ring-honey-500">
                                <span class="text-sm group-hover:text-gray-900 text-gray-600"><?php echo $opt['option_text']; ?></span>
                            </label>
                            <?php endwhile; ?>
                        </div>
                    <?php else: ?>
                        <textarea name="essay_<?php echo $q['id']; ?>" rows="4" class="w-full p-3 border border-gray-200 rounded-xl focus:border-honey-500 outline-none" placeholder="Nhập câu trả lời..."></textarea>
                    <?php endif; ?>
                </div>
            <?php endwhile; ?>
            
            <input type="hidden" name="submit_exam" value="1">
            <div class="text-center pt-8 border-t border-gray-200">
                <button type="submit" onclick="return confirm('Xác nhận nộp bài?')" class="bg-honey-500 hover:bg-honey-600 text-white px-12 py-3 rounded-xl font-bold text-lg shadow-lg shadow-honey-500/30 transition transform hover:-translate-y-1">
                    Nộp bài thi
                </button>
            </div>
            <?php endif; ?>
        </form>
    </div>

</body>
</html>