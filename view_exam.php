<?php
include "connection.php"; include "auth.php"; requireRole(['teacher']);
$id = intval($_GET['id']);
$exam = mysqli_fetch_assoc(mysqli_query($link, "SELECT * FROM exams WHERE id=$id"));
$questions = mysqli_query($link, "SELECT * FROM exam_questions WHERE exam_id=$id");
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Chi tiết đề thi</title>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 p-8 font-sans">
    <div class="max-w-3xl mx-auto bg-white p-8 rounded-2xl shadow-lg">
        <div class="border-b pb-4 mb-6">
            <h1 class="text-2xl font-bold text-gray-900"><?php echo $exam['exam_title']; ?></h1>
            <p class="text-gray-500 mt-1">Thời gian: <?php echo $exam['duration']; ?> phút | Loại: <?php echo ucfirst($exam['exam_type']); ?></p>
        </div>

        <div class="space-y-6">
            <?php 
            $i=1;
            while($q = mysqli_fetch_assoc($questions)): 
            ?>
            <div class="p-4 bg-gray-50 rounded-xl border border-gray-200">
                <h3 class="font-bold text-gray-800 mb-3">Câu <?php echo $i++; ?>: <?php echo $q['question_text']; ?></h3>
                
                <?php if($q['question_type'] == 'multiple_choice'): ?>
                    <ul class="space-y-2">
                        <?php 
                        $opts = mysqli_query($link, "SELECT * FROM exam_options WHERE question_id=".$q['id']);
                        while($opt = mysqli_fetch_assoc($opts)): 
                            $style = $opt['is_correct'] ? "text-green-600 font-bold bg-green-50 border-green-200" : "text-gray-600 bg-white border-gray-200";
                        ?>
                        <li class="px-3 py-2 border rounded-lg <?php echo $style; ?>">
                            <?php echo $opt['option_text']; ?>
                            <?php if($opt['is_correct']) echo " (Đúng)"; ?>
                        </li>
                        <?php endwhile; ?>
                    </ul>
                <?php else: ?>
                    <div class="p-3 bg-white border border-dashed border-gray-300 rounded text-gray-400 text-sm italic">
                        Câu hỏi tự luận
                    </div>
                <?php endif; ?>
            </div>
            <?php endwhile; ?>
        </div>
    </div>
</body>
</html>