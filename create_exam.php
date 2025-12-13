<?php
include "connection.php"; include "auth.php"; requireRole(['teacher']);
$tid = $_SESSION['teacher_id'];
date_default_timezone_set('Asia/Ho_Chi_Minh'); // Set chuẩn múi giờ VN

// Lấy danh sách lớp
$classes = [];
$q = mysqli_query($link, "SELECT * FROM classes WHERE teacher_id = $tid");
while($c = mysqli_fetch_assoc($q)) { $classes[] = $c; }

// --- XỬ LÝ LƯU ---
if(isset($_POST['publish'])) {
    $title = mysqli_real_escape_string($link, $_POST['exam_title']);
    $start = $_POST['start_time']; 
    $end = $_POST['end_time'];
    $duration = intval($_POST['duration']);
    $cid = intval($_POST['class_id']);
    $exam_type_global = $_POST['type']; // quiz, essay, mixed

    // 1. Lưu Exam
    $sql = "INSERT INTO exams (exam_title, exam_date, end_date, duration, class_id, teacher_id, exam_type) 
            VALUES ('$title', '$start', '$end', $duration, $cid, $tid, '$exam_type_global')";
    
    if(mysqli_query($link, $sql)) {
        $eid = mysqli_insert_id($link);
        
        // 2. Lưu Câu hỏi
        if(isset($_POST['questions'])) {
            foreach($_POST['questions'] as $q) {
                $q_text = mysqli_real_escape_string($link, $q['text']);
                $q_type = $q['type']; // Lấy loại câu hỏi từng câu
                
                mysqli_query($link, "INSERT INTO exam_questions (exam_id, question_text, question_type) VALUES ($eid, '$q_text', '$q_type')");
                $qid = mysqli_insert_id($link);

                // Chỉ lưu đáp án nếu là trắc nghiệm
                if($q_type == 'multiple_choice' && isset($q['options'])) {
                    foreach($q['options'] as $idx => $opt) {
                        $is_c = ($idx == $q['correct_option']) ? 1 : 0;
                        $opt_text = mysqli_real_escape_string($link, $opt);
                        mysqli_query($link, "INSERT INTO exam_options (question_id, option_text, is_correct) VALUES ($qid, '$opt_text', $is_c)");
                    }
                }
            }
        }
        echo "<script>alert('Đăng bài thành công!'); window.location='class_detail.php?id=$cid';</script>";
    } else {
        echo "<script>alert('Lỗi: ".mysqli_error($link)."');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Tạo bài kiểm tra | Teacher Bee</title>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { theme: { extend: { fontFamily: { sans: ['"Be Vietnam Pro"', 'sans-serif'] }, colors: { honey: { 50:'#FFF8E1', 100:'#FFECB3', 500:'#FFB300', 600:'#FFA000' }, dark: { 900:'#2D3436', 500:'#636E72', 100:'#F9FAFB' } } } } }
    </script>
    <style>
        .step-active { @apply border-honey-500 text-honey-600 bg-honey-50; }
        .step-circle-active { background-color: #FFB300; color: white; border-color: #FFB300; }
        .step-circle-inactive { background-color: white; color: #9CA3AF; border-color: #E5E7EB; }
        input[type="radio"]:checked + div { border-color: #FFB300; background-color: #FFF8E1; ring: 2px solid #FFB300; }
    </style>
</head>
<body class="bg-dark-100 min-h-screen font-sans text-dark-900 pb-20">
    <form method="POST" id="examForm">
    
    <div class="bg-white shadow-sm h-16 flex items-center justify-between px-6 fixed top-0 w-full z-20">
        <div class="flex items-center gap-3">
            <a href="teacher_dashboard.php" class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center hover:bg-gray-200"><i class="ph-bold ph-arrow-left"></i></a>
            <h1 class="text-lg font-bold">Tạo bài kiểm tra mới</h1>
        </div>
        <button type="submit" name="publish" class="px-6 py-2 bg-honey-500 hover:bg-honey-600 text-white text-sm font-bold rounded-lg shadow-sm">Đăng bài</button>
    </div>

    <div class="max-w-4xl mx-auto mt-24 px-4">
        <div class="flex items-center justify-center mb-8">
            <div class="flex items-center w-full max-w-2xl relative">
                <div class="absolute top-5 left-0 w-full h-1 bg-gray-200 -z-10"></div>
                <div class="flex-1 flex flex-col items-center"><div id="ind-1" class="w-10 h-10 step-circle-active border-2 rounded-full flex items-center justify-center font-bold z-10 transition-colors">1</div><span class="text-xs font-bold mt-2 text-honey-600">Thông tin</span></div>
                <div class="flex-1 flex flex-col items-center"><div id="ind-2" class="w-10 h-10 step-circle-inactive border-2 rounded-full flex items-center justify-center font-bold z-10 bg-white transition-colors">2</div><span class="text-xs font-medium mt-2 text-gray-500">Cấu hình</span></div>
                <div class="flex-1 flex flex-col items-center"><div id="ind-3" class="w-10 h-10 step-circle-inactive border-2 rounded-full flex items-center justify-center font-bold z-10 bg-white transition-colors">3</div><span class="text-xs font-medium mt-2 text-gray-500">Nội dung</span></div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden min-h-[500px]">
            
            <div id="step-content-1" class="p-8 space-y-6">
                <div class="space-y-2"><label class="block text-sm font-bold text-dark-900">Tên bài kiểm tra *</label><input type="text" name="exam_title" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:border-honey-500 outline-none" required placeholder="VD: Kiểm tra 1 tiết"></div>
                <div class="space-y-2"><label class="block text-sm font-bold text-dark-900">Lớp áp dụng *</label>
                    <select name="class_id" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:border-honey-500 outline-none">
                        <?php foreach($classes as $c): ?><option value="<?php echo $c['id']; ?>"><?php echo $c['name']; ?></option><?php endforeach; ?>
                    </select>
                </div>
                <div class="mt-8 text-right"><button type="button" onclick="nextStep()" class="px-6 py-3 bg-dark-900 text-white font-bold rounded-xl">Tiếp tục <i class="ph-bold ph-arrow-right"></i></button></div>
            </div>

            <div id="step-content-2" class="p-8 space-y-6 hidden">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2"><label class="block text-sm font-bold text-dark-900">Bắt đầu *</label><input type="datetime-local" name="start_time" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl outline-none" required></div>
                    <div class="space-y-2"><label class="block text-sm font-bold text-dark-900">Kết thúc *</label><input type="datetime-local" name="end_time" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl outline-none" required></div>
                </div>
                <div class="space-y-2"><label class="block text-sm font-bold text-dark-900">Thời gian (Phút)</label><input type="number" name="duration" value="45" class="w-32 px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl font-bold outline-none"></div>
                
                <div class="space-y-2">
                    <label class="block text-sm font-bold text-dark-900">Hình thức thi</label>
                    <div class="grid grid-cols-3 gap-4">
                        <label class="cursor-pointer group"><input type="radio" name="type" value="quiz" class="sr-only" checked onclick="setExamType('quiz')"><div class="p-4 border border-gray-200 rounded-xl flex flex-col items-center gap-2 transition text-center"><i class="ph-duotone ph-list-checks text-3xl text-gray-400"></i><span class="text-sm font-medium">Trắc nghiệm</span></div></label>
                        <label class="cursor-pointer group"><input type="radio" name="type" value="essay" class="sr-only" onclick="setExamType('essay')"><div class="p-4 border border-gray-200 rounded-xl flex flex-col items-center gap-2 transition text-center"><i class="ph-duotone ph-text-t text-3xl text-gray-400"></i><span class="text-sm font-medium">Tự luận</span></div></label>
                        <label class="cursor-pointer group"><input type="radio" name="type" value="mixed" class="sr-only" onclick="setExamType('mixed')"><div class="p-4 border border-gray-200 rounded-xl flex flex-col items-center gap-2 transition text-center"><i class="ph-duotone ph-files text-3xl text-gray-400"></i><span class="text-sm font-medium">Kết hợp</span></div></label>
                    </div>
                </div>
                <div class="flex justify-between pt-6 border-t mt-4"><button type="button" onclick="prevStep()" class="px-6 py-3 bg-gray-100 text-gray-600 font-bold rounded-xl">Quay lại</button><button type="button" onclick="nextStep()" class="px-6 py-3 bg-dark-900 text-white font-bold rounded-xl">Tiếp tục <i class="ph-bold ph-arrow-right"></i></button></div>
            </div>

            <div id="step-content-3" class="p-8 hidden">
                <div class="flex justify-between items-center border-b pb-4 mb-6">
                    <h3 class="text-xl font-bold text-dark-900">Soạn thảo đề thi</h3>
                    <button type="button" onclick="addQuestion()" class="px-4 py-2 border-2 border-honey-500 text-honey-600 font-bold rounded-lg hover:bg-honey-50 flex gap-2"><i class="ph-bold ph-plus"></i> Thêm câu hỏi</button>
                </div>
                <div id="questions-container" class="space-y-6 pb-10"></div>
                <div class="flex justify-between pt-6 border-t"><button type="button" onclick="prevStep()" class="px-6 py-3 bg-gray-100 text-gray-600 font-bold rounded-xl">Quay lại</button></div>
            </div>
        </div>
    </div>
    </form>

    <script>
        let currentStep = 1; 
        let qCount = 0;
        let globalType = 'quiz'; // Mặc định trắc nghiệm

        function setExamType(type) { globalType = type; }

        function updateUI() {
            // 1. Ẩn/Hiện nội dung các bước
            [1, 2, 3].forEach(i => {
                const content = document.getElementById(`step-content-${i}`);
                if (content) content.classList.toggle('hidden', i !== currentStep);
            });

            // 2. Cập nhật thanh tiến trình (Indicator)
            for (let i = 1; i <= 3; i++) {
                const circle = document.getElementById(`ind-${i}`);
                
                // Reset class cơ bản
                circle.className = "w-10 h-10 border-2 rounded-full flex items-center justify-center font-bold z-10 transition-colors cursor-pointer";

                if (i < currentStep) {
                    // Bước đã qua (Hoàn thành) -> Màu Xanh hoặc Vàng đậm, có dấu tích
                    circle.classList.add("bg-green-500", "text-white", "border-green-500");
                    circle.innerHTML = '<i class="ph-bold ph-check"></i>';
                    // Thêm sự kiện click để quay lại
                    circle.onclick = function() { goToStep(i); };
                } else if (i === currentStep) {
                    // Bước hiện tại -> Màu Vàng (Active)
                    circle.classList.add("bg-honey-500", "text-white", "border-honey-500", "shadow-md");
                    circle.innerHTML = i;
                    circle.onclick = null; // Đang ở đây thì không cần click
                } else {
                    // Bước chưa đến -> Màu Trắng (Inactive)
                    circle.classList.add("bg-white", "text-gray-400", "border-gray-200");
                    circle.innerHTML = i;
                    circle.onclick = null; // Chưa đến thì không cho click
                }
            }
        }

        // Hàm nhảy đến bước cụ thể (Dùng khi click vào số 1, 2)
        function goToStep(step) {
            if (step < currentStep) { // Chỉ cho phép quay lại bước đã qua
                currentStep = step;
                updateUI();
            }
        }

        function nextStep() {
            // Validation đơn giản (nếu cần)
            if (currentStep === 1) {
                const title = document.querySelector('input[name="exam_title"]').value;
                if (!title) { alert("Vui lòng nhập tên bài kiểm tra!"); return; }
            }
            
            if (currentStep < 3) {
                currentStep++;
                updateUI();
                // Nếu sang bước 3 và chưa có câu hỏi nào thì thêm 1 câu mặc định
                if (currentStep === 3 && qCount === 0) addQuestion();
            }
        }

        function prevStep() {
            if (currentStep > 1) {
                currentStep--;
                updateUI();
            }
        }

        function addQuestion() {
            const container = document.getElementById('questions-container');
            const index = qCount;
            
            // Xác định loại câu hỏi: Nếu mixed thì mặc định trắc nghiệm, còn lại theo globalType
            let qType = globalType === 'mixed' ? 'multiple_choice' : (globalType === 'quiz' ? 'multiple_choice' : 'essay');
            
            // HTML Template cho câu hỏi mới
            let html = `
            <div class="question-block bg-gray-50 p-6 rounded-xl border border-gray-200 relative group animate-fade-in-down mb-4">
                <button type="button" onclick="this.closest('.question-block').remove()" class="absolute top-4 right-4 text-gray-400 hover:text-red-500 transition"><i class="ph-bold ph-trash text-xl"></i></button>
                
                <div class="mb-4 pr-10">
                    <label class="block text-sm font-bold text-honey-600 mb-1 flex justify-between items-center">
                        <span>Câu hỏi #${index + 1}</span>
                        ${globalType === 'mixed' ? `
                        <select name="questions[${index}][type]" onchange="toggleQType(this, ${index})" class="text-xs border border-gray-300 rounded px-2 py-1 bg-white focus:border-honey-500 outline-none">
                            <option value="multiple_choice">Trắc nghiệm</option>
                            <option value="essay">Tự luận</option>
                        </select>` 
                        : `<input type="hidden" name="questions[${index}][type]" value="${qType}">`}
                    </label>
                    <input type="text" name="questions[${index}][text]" class="w-full px-4 py-3 bg-white border border-gray-200 rounded-lg outline-none focus:ring-2 focus:ring-honey-500/20 focus:border-honey-500 transition" placeholder="Nhập nội dung câu hỏi..." required>
                </div>`;

            // Phần Đáp án Trắc nghiệm (A, B, C, D)
            html += `<div id="opts-${index}" class="${qType === 'essay' ? 'hidden' : ''} grid grid-cols-1 md:grid-cols-2 gap-3">`;
            const labels = ['A', 'B', 'C', 'D'];
            for(let i=0; i<4; i++) {
                html += `<div class="flex items-center gap-3 bg-white p-3 rounded-lg border border-gray-200 focus-within:border-honey-500 transition">
                            <input type="radio" name="questions[${index}][correct_option]" value="${i}" class="w-5 h-5 text-honey-600 focus:ring-honey-500 cursor-pointer">
                            <input type="text" name="questions[${index}][options][${i}]" class="w-full outline-none text-sm bg-transparent" placeholder="Đáp án ${labels[i]}">
                         </div>`;
            }
            html += `</div>`;
            
            // Phần Tự luận (Textarea giả)
            html += `<div id="essay-${index}" class="${qType === 'multiple_choice' ? 'hidden' : ''}">
                        <div class="p-4 bg-white border-2 border-dashed border-gray-300 rounded-lg text-sm text-gray-400 text-center flex flex-col items-center justify-center gap-2 h-32">
                            <i class="ph-duotone ph-text-t text-2xl"></i>
                            <span>Học sinh sẽ nhập câu trả lời văn bản tại đây.</span>
                        </div>
                     </div>`;
            
            html += `</div>`;
            
            container.insertAdjacentHTML('beforeend', html);
            qCount++;
        }

        // Hàm chuyển đổi giao diện khi thay đổi dropdown (Chế độ Mixed)
        function toggleQType(select, index) {
            const val = select.value;
            const optsDiv = document.getElementById(`opts-${index}`);
            const essayDiv = document.getElementById(`essay-${index}`);
            
            if (val === 'essay') {
                optsDiv.classList.add('hidden');
                essayDiv.classList.remove('hidden');
            } else {
                optsDiv.classList.remove('hidden');
                essayDiv.classList.add('hidden');
            }
        }

        // Khởi chạy lần đầu
        updateUI();
    </script>
</body>
</html>