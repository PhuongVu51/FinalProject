<?php
// 1. SỬA ĐƯỜNG DẪN (Thêm ../)
include "../connection.php"; 
include "../auth.php"; 
requireRole(['teacher']);

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Lấy thông tin đề thi
$exam = mysqli_fetch_assoc(mysqli_query($link, "SELECT * FROM exams WHERE id=$id"));

// Kiểm tra nếu đề không tồn tại
if(!$exam) {
    echo "<script>alert('Không tìm thấy đề thi!'); window.history.back();</script>";
    exit;
}

$questions = mysqli_query($link, "SELECT * FROM exam_questions WHERE exam_id=$id");
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title><?php echo $exam['exam_title']; ?> | Xem trước</title>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { theme: { extend: { fontFamily: { sans: ['"Be Vietnam Pro"', 'sans-serif'] }, colors: { honey: { 50:'#FFF8E1', 500:'#FFB300', 600:'#FFA000' }, dark: { 900:'#2D3436' } } } } }
    </script>
    <style>
        /* Ẩn sidebar và các nút khi in */
        @media print {
            aside, .no-print { display: none !important; }
            .main-content { margin-left: 0 !important; padding: 0 !important; }
            body { background: white; }
            .print-border { border: 1px solid #ddd; }
        }
    </style>
</head>
<body class="bg-gray-50 font-sans text-dark-900 flex">

    <?php include "../includes/sidebar.php"; ?>

    <div class="flex-1 p-8 ml-[260px] main-content">
        
        <div class="flex justify-between items-center mb-6 no-print">
            <a href="javascript:history.back()" class="flex items-center gap-2 text-gray-500 hover:text-honey-600 font-bold transition">
                <i class="ph-bold ph-arrow-left"></i> Quay lại
            </a>
            <button onclick="window.print()" class="px-4 py-2 bg-dark-900 text-white rounded-lg font-bold flex items-center gap-2 hover:bg-gray-800 transition">
                <i class="ph-bold ph-printer"></i> In đề thi
            </button>
        </div>

        <div class="max-w-4xl mx-auto bg-white p-10 rounded-3xl shadow-sm border border-gray-200 print-border">
            
            <div class="border-b-2 border-gray-100 pb-6 mb-8 text-center">
                <h1 class="text-3xl font-black text-gray-900 uppercase mb-2"><?php echo $exam['exam_title']; ?></h1>
                <div class="flex justify-center gap-6 text-sm text-gray-500 font-medium">
                    <span><i class="ph-bold ph-book-open"></i> Môn: <?php echo $exam['subject']; ?></span>
                    <span><i class="ph-bold ph-clock"></i> Thời gian: <?php echo $exam['duration']; ?> phút</span>
                    <span><i class="ph-bold ph-list-checks"></i> Hình thức: <?php echo ($exam['exam_type']=='quiz')?'Trắc nghiệm':(($exam['exam_type']=='essay')?'Tự luận':'Kết hợp'); ?></span>
                </div>
            </div>

            <div class="space-y-8">
                <?php 
                $i=1;
                if(mysqli_num_rows($questions) == 0) echo "<p class='text-center text-gray-400 italic'>Đề thi chưa có câu hỏi nào.</p>";
                
                while($q = mysqli_fetch_assoc($questions)): 
                ?>
                <div class="break-inside-avoid"> <h3 class="font-bold text-lg text-gray-800 mb-3 flex gap-2">
                        <span class="text-honey-600">Câu <?php echo $i++; ?>:</span> 
                        <span><?php echo nl2br($q['question_text']); ?></span>
                    </h3>
                    
                    <?php if($q['question_type'] == 'multiple_choice'): ?>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 pl-4">
                            <?php 
                            $opts = mysqli_query($link, "SELECT * FROM exam_options WHERE question_id=".$q['id']);
                            while($opt = mysqli_fetch_assoc($opts)): 
                                // Highlight đáp án đúng
                                $is_correct = $opt['is_correct'];
                                $border = $is_correct ? "border-green-500 bg-green-50" : "border-gray-200";
                                $text = $is_correct ? "text-green-700 font-bold" : "text-gray-600";
                                $icon = $is_correct ? "<i class='ph-bold ph-check-circle text-green-500 ml-auto'></i>" : "";
                            ?>
                            <div class="px-4 py-3 border rounded-xl <?php echo $border; ?> <?php echo $text; ?> flex items-center text-sm">
                                <?php echo $opt['option_text']; ?>
                                <?php echo $icon; ?>
                            </div>
                            <?php endwhile; ?>
                        </div>
                    <?php else: ?>
                        <div class="pl-4">
                            <div class="w-full h-32 border-2 border-dashed border-gray-200 rounded-xl bg-gray-50 flex items-center justify-center text-gray-400 text-sm">
                                (Phần trả lời tự luận của học sinh)
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                <?php endwhile; ?>
            </div>

            <div class="mt-12 pt-6 border-t border-gray-100 text-center text-xs text-gray-400 hidden print:block">
                Đề thi được tạo bởi hệ thống Teacher Bee - <?php echo date('d/m/Y'); ?>
            </div>

        </div>
    </div>
</body>
</html>