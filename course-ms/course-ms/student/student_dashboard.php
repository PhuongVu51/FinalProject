<?php
include "../connection.php"; 
include "../auth.php"; 
requireRole(['student']);
$sid = $_SESSION['student_id'];

// 1. LẤY DỮ LIỆU ĐIỂM
$scores = [];
$total_score = 0;
$count = 0;
$max_score = 0;
$min_score = 10;

// Lấy điểm sắp xếp theo ngày thi cũ -> mới để vẽ biểu đồ cho đúng chiều thời gian
$query = "SELECT sc.score, e.exam_title, e.subject, e.exam_date 
          FROM scores sc 
          JOIN exams e ON sc.exam_id = e.id 
          WHERE sc.student_id = $sid 
          ORDER BY e.exam_date ASC";
$res = mysqli_query($link, $query);

while($r = mysqli_fetch_assoc($res)){
    $scores[] = $r;
    $total_score += $r['score'];
    $count++;
    if($r['score'] > $max_score) $max_score = $r['score'];
    if($r['score'] < $min_score) $min_score = $r['score'];
}

// Tính toán chỉ số
$avg_score = ($count > 0) ? round($total_score / $count, 2) : 0;

// Xếp loại
if($count == 0) { $rank = "Chưa xếp loại"; $rank_color = "text-gray-400"; $rank_bg = "bg-gray-100"; }
elseif($avg_score >= 8.5) { $rank = "Xuất sắc"; $rank_color = "text-green-600"; $rank_bg = "bg-green-100"; }
elseif($avg_score >= 7.0) { $rank = "Giỏi"; $rank_color = "text-blue-600"; $rank_bg = "bg-blue-100"; }
elseif($avg_score >= 5.0) { $rank = "Khá"; $rank_color = "text-yellow-600"; $rank_bg = "bg-yellow-100"; }
else { $rank = "Cần cố gắng"; $rank_color = "text-red-600"; $rank_bg = "bg-red-100"; }

// Chuẩn bị dữ liệu cho Biểu đồ (JSON)
$chart_labels = [];
$chart_data = [];
foreach($scores as $s) {
    $chart_labels[] = $s['exam_title']; // Tên bài thi làm nhãn
    $chart_data[] = $s['score'];        // Điểm số
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <title>Bảng thành tích | Student</title>
    <?php include "../includes/header_config.php"; ?>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-50 flex font-sans text-gray-900">
    
    <?php include "../includes/sidebar.php"; ?>

    <div class="flex-1 p-8 ml-[260px]">
        
        <h1 class="text-3xl font-extrabold mb-8 flex items-center gap-3 text-gray-800">
            <i class="ph-duotone ph-chart-polar text-honey-500"></i> Bảng Thành Tích
        </h1>

        <?php if($count == 0): ?>
            <div class="flex flex-col items-center justify-center py-20 bg-white rounded-3xl border border-dashed border-gray-300">
                <div class="w-32 h-32 bg-gray-50 rounded-full flex items-center justify-center mb-6 text-gray-300 text-6xl">
                    <i class="ph-duotone ph-chart-bar"></i>
                </div>
                <h2 class="text-2xl font-bold text-gray-700">Chưa có dữ liệu điểm số</h2>
                <p class="text-gray-500 mt-2">Hãy hoàn thành bài kiểm tra đầu tiên để xem thống kê nhé!</p>
                <a href="student_classes.php" class="mt-6 px-8 py-3 bg-honey-500 text-white font-bold rounded-xl hover:bg-honey-600 transition shadow-lg shadow-honey-500/30">
                    Tìm bài kiểm tra
                </a>
            </div>
        <?php else: ?>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 flex flex-col items-center justify-center relative overflow-hidden group hover:shadow-md transition">
                    <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition">
                        <i class="ph-duotone ph-star text-6xl text-honey-500"></i>
                    </div>
                    <div class="text-5xl font-black text-gray-800 mb-2"><?php echo $avg_score; ?></div>
                    <div class="text-sm font-bold text-gray-400 uppercase tracking-widest">Điểm trung bình</div>
                </div>

                <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 flex flex-col items-center justify-center hover:shadow-md transition">
                    <div class="px-4 py-2 rounded-full font-bold text-sm mb-3 <?php echo $rank_bg . ' ' . $rank_color; ?>">
                        <?php echo $rank; ?>
                    </div>
                    <div class="text-sm font-bold text-gray-400 uppercase tracking-widest">Xếp loại học lực</div>
                </div>

                <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 flex items-center gap-5 hover:shadow-md transition">
                    <div class="w-16 h-16 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-3xl">
                        <i class="ph-duotone ph-files"></i>
                    </div>
                    <div>
                        <div class="text-3xl font-black text-gray-800"><?php echo $count; ?></div>
                        <div class="text-xs font-bold text-gray-400 uppercase">Bài đã thi</div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 flex flex-col justify-center gap-3 hover:shadow-md transition">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-400 text-xs font-bold uppercase">Cao nhất</span>
                        <span class="text-xl font-black text-green-600"><?php echo $max_score; ?></span>
                    </div>
                    <div class="h-px bg-gray-100 w-full"></div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-400 text-xs font-bold uppercase">Thấp nhất</span>
                        <span class="text-xl font-black text-red-500"><?php echo ($min_score == 10) ? 0 : $min_score; ?></span>
                    </div>
                </div>
            </div>

            <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100 mb-8">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-bold text-gray-800">Biểu đồ tiến độ học tập</h2>
                    <span class="text-xs font-bold bg-gray-100 text-gray-500 px-3 py-1 rounded-full">Gần đây nhất</span>
                </div>
                <div class="h-80 w-full">
                    <canvas id="scoreChart"></canvas>
                </div>
            </div>

            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-50 flex items-center gap-2">
                    <i class="ph-duotone ph-list-dashes text-xl text-honey-500"></i>
                    <h2 class="font-bold text-gray-800">Lịch sử điểm số chi tiết</h2>
                </div>
                <table class="w-full text-left">
                    <thead class="bg-gray-50/50 border-b border-gray-100 text-gray-400 uppercase font-bold text-xs">
                        <tr>
                            <th class="px-8 py-5">Bài kiểm tra</th>
                            <th class="px-8 py-5">Môn học</th>
                            <th class="px-8 py-5">Ngày thi</th>
                            <th class="px-8 py-5 text-right">Kết quả</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <?php 
                        // Đảo ngược mảng để hiện bài mới nhất lên đầu bảng
                        $reversed_scores = array_reverse($scores);
                        foreach($reversed_scores as $s): 
                            $sc = $s['score'];
                            // Màu sắc điểm
                            if($sc >= 8.0) $sc_class = "text-green-600 bg-green-50 border-green-200";
                            elseif($sc >= 5.0) $sc_class = "text-blue-600 bg-blue-50 border-blue-200";
                            else $sc_class = "text-red-600 bg-red-50 border-red-200";
                        ?>
                        <tr class="hover:bg-honey-50/10 transition group">
                            <td class="px-8 py-5 font-bold text-gray-800 text-base">
                                <?php echo $s['exam_title']; ?>
                            </td>
                            <td class="px-8 py-5 font-medium text-gray-500">
                                <?php echo $s['subject']; ?>
                            </td>
                            <td class="px-8 py-5 text-sm text-gray-400 font-medium">
                                <?php echo date('d/m/Y', strtotime($s['exam_date'])); ?>
                            </td>
                            <td class="px-8 py-5 text-right">
                                <span class="inline-flex items-center justify-center w-12 h-10 rounded-xl font-black text-lg border <?php echo $sc_class; ?>">
                                    <?php echo $sc; ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

        <?php endif; ?>
    </div>

    <script>
        <?php if($count > 0): ?>
        const ctx = document.getElementById('scoreChart').getContext('2d');
        
        // Tạo gradient màu vàng mật ong cho đẹp
        let gradient = ctx.createLinearGradient(0, 0, 0, 400);
        gradient.addColorStop(0, 'rgba(255, 179, 0, 0.5)'); // Honey color
        gradient.addColorStop(1, 'rgba(255, 179, 0, 0.0)');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode($chart_labels); ?>,
                datasets: [{
                    label: 'Điểm số',
                    data: <?php echo json_encode($chart_data); ?>,
                    borderColor: '#FFB300', // Honey-500
                    backgroundColor: gradient,
                    borderWidth: 3,
                    pointBackgroundColor: '#FFF',
                    pointBorderColor: '#FFB300',
                    pointBorderWidth: 3,
                    pointRadius: 6,
                    pointHoverRadius: 8,
                    fill: true,
                    tension: 0.4 // Làm mềm đường cong (Wow factor!)
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#2D3436',
                        padding: 12,
                        titleFont: { size: 14, family: "'Be Vietnam Pro', sans-serif" },
                        bodyFont: { size: 14, family: "'Be Vietnam Pro', sans-serif", weight: 'bold' }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 10,
                        grid: { borderDash: [5, 5], color: '#F3F4F6' },
                        ticks: { font: { family: "'Be Vietnam Pro', sans-serif" } }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { family: "'Be Vietnam Pro', sans-serif" } }
                    }
                }
            }
        });
        <?php endif; ?>
    </script>
</body>
</html>