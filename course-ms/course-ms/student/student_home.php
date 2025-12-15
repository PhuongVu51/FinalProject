<?php
// 1. KẾT NỐI & CẤU HÌNH
include "../connection.php"; 
include "../auth.php"; 
requireRole(['student']);
date_default_timezone_set('Asia/Ho_Chi_Minh');

$sid = $_SESSION['student_id'];

// --- HÀM DEBUG QUERY ---
function runQuery($link, $sql) {
    $res = mysqli_query($link, $sql);
    if(!$res) die("SQL Error: " . mysqli_error($link));
    return $res;
}

// 2. THỐNG KÊ (BIG NUMBERS)
$stats = [
    'classes' => mysqli_fetch_assoc(runQuery($link, "SELECT COUNT(*) as c FROM students WHERE id=$sid AND class_id > 0"))['c'],
    'exams_done' => mysqli_fetch_assoc(runQuery($link, "SELECT COUNT(*) as c FROM scores WHERE student_id=$sid"))['c'],
    'pending' => mysqli_fetch_assoc(runQuery($link, "SELECT COUNT(*) as c FROM exams e JOIN students s ON e.class_id=s.class_id WHERE s.id=$sid AND e.exam_date > NOW()"))['c']
];

// 3. DỮ LIỆU BIỂU ĐỒ (Lấy 5 bài thi gần nhất)
$chart_labels = [];
$chart_data = [];
$chart_sql = "SELECT sc.score, e.exam_title 
              FROM scores sc JOIN exams e ON sc.exam_id=e.id 
              WHERE sc.student_id=$sid ORDER BY e.exam_date ASC LIMIT 5";
$chart_res = runQuery($link, $chart_sql);
while($row = mysqli_fetch_assoc($chart_res)){
    $chart_labels[] = $row['exam_title'];
    $chart_data[] = $row['score'];
}

// 4. LỚP HỌC (Lấy 3 lớp mới nhất)
$my_classes = runQuery($link, "
    SELECT c.id, c.name, u.full_name as teacher 
    FROM classes c 
    JOIN students s ON c.id = s.class_id 
    JOIN teachers t ON c.teacher_id = t.id 
    JOIN users u ON t.user_id = u.id 
    WHERE s.id = $sid 
    ORDER BY s.id DESC LIMIT 3
");

// 5. LỊCH THI SẮP TỚI
$upcoming = runQuery($link, "
    SELECT e.id, e.exam_title, e.exam_date, c.name as class_name 
    FROM exams e 
    JOIN students s ON e.class_id = s.class_id 
    JOIN classes c ON e.class_id = c.id
    WHERE s.id = $sid AND e.exam_date > NOW() 
    ORDER BY e.exam_date ASC LIMIT 3
");
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <title>Dashboard | Student</title>
    <?php include "../includes/header_config.php"; ?>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-[#F8F9FD] flex font-sans text-gray-900">
    
    <?php include "../includes/sidebar.php"; ?>

    <div class="flex-1 p-8 ml-[260px]">
        
        <div class="flex justify-between items-end mb-10">
            <div>
                <h1 class="text-4xl font-black text-gray-800 tracking-tight mb-2">
                    Hi, <?php echo $_SESSION['full_name']; ?> 👋
                </h1>
                <p class="text-gray-500 font-medium text-lg">Chào mừng trở lại! Cùng xem qua tiến độ học tập nhé.</p>
            </div>
            <a href="student_classes.php?tab=market" class="bg-dark-900 hover:bg-black text-white px-6 py-3 rounded-xl font-bold shadow-lg shadow-gray-200 transition transform hover:-translate-y-1 flex items-center gap-2">
                <i class="ph-bold ph-plus"></i> Đăng ký lớp mới
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
            <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-gray-100 relative overflow-hidden group hover:shadow-md transition">
                <div class="absolute top-0 right-0 p-4 opacity-5 group-hover:opacity-10 transition">
                    <i class="ph-duotone ph-books text-8xl text-blue-600"></i>
                </div>
                <div class="flex flex-col h-full justify-between relative z-10">
                    <div class="w-14 h-14 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-3xl mb-4">
                        <i class="ph-fill ph-student"></i>
                    </div>
                    <div>
                        <div class="text-5xl font-black text-gray-900 tracking-tighter mb-1"><?php echo $stats['classes']; ?></div>
                        <div class="text-sm font-bold text-gray-400 uppercase tracking-widest">Lớp đang học</div>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-gray-100 relative overflow-hidden group hover:shadow-md transition">
                <div class="absolute top-0 right-0 p-4 opacity-5 group-hover:opacity-10 transition">
                    <i class="ph-duotone ph-check-circle text-8xl text-green-600"></i>
                </div>
                <div class="flex flex-col h-full justify-between relative z-10">
                    <div class="w-14 h-14 rounded-2xl bg-green-50 text-green-600 flex items-center justify-center text-3xl mb-4">
                        <i class="ph-fill ph-certificate"></i>
                    </div>
                    <div>
                        <div class="text-5xl font-black text-gray-900 tracking-tighter mb-1"><?php echo $stats['exams_done']; ?></div>
                        <div class="text-sm font-bold text-gray-400 uppercase tracking-widest">Bài hoàn thành</div>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-gray-100 relative overflow-hidden group hover:shadow-md transition">
                <div class="absolute top-0 right-0 p-4 opacity-5 group-hover:opacity-10 transition">
                    <i class="ph-duotone ph-clock text-8xl text-honey-500"></i>
                </div>
                <div class="flex flex-col h-full justify-between relative z-10">
                    <div class="w-14 h-14 rounded-2xl bg-honey-50 text-honey-600 flex items-center justify-center text-3xl mb-4">
                        <i class="ph-fill ph-lightning"></i>
                    </div>
                    <div>
                        <div class="text-5xl font-black text-gray-900 tracking-tighter mb-1"><?php echo $stats['pending']; ?></div>
                        <div class="text-sm font-bold text-gray-400 uppercase tracking-widest">Sắp diễn ra</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <div class="lg:col-span-2 space-y-8">
                
                <div class="bg-white p-8 rounded-[2rem] shadow-sm border border-gray-100">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-xl font-extrabold text-gray-800">Phong độ học tập</h2>
                        <a href="student_dashboard.php" class="text-honey-600 font-bold text-sm hover:underline">Chi tiết</a>
                    </div>
                    <div class="relative h-64 w-full">
                        <?php if(empty($chart_data)): ?>
                            <div class="absolute inset-0 flex items-center justify-center flex-col text-gray-400">
                                <i class="ph-duotone ph-chart-line-up text-5xl mb-2"></i>
                                <p>Chưa có dữ liệu điểm số</p>
                            </div>
                        <?php endif; ?>
                        <canvas id="performanceChart"></canvas>
                    </div>
                </div>

                <div>
                    <h2 class="text-xl font-extrabold text-gray-800 mb-6 px-2">Lớp học gần đây</h2>
                    <div class="space-y-4">
                        <?php while($c = mysqli_fetch_assoc($my_classes)): ?>
                        <a href="student_class_detail.php?id=<?php echo $c['id']; ?>" class="flex items-center gap-5 bg-white p-5 rounded-3xl border border-gray-100 shadow-sm hover:shadow-md hover:border-honey-300 transition group">
                            <div class="w-16 h-16 rounded-2xl bg-gray-50 flex items-center justify-center text-2xl font-black text-gray-300 group-hover:bg-honey-500 group-hover:text-white transition">
                                <?php echo substr($c['name'], 0, 1); ?>
                            </div>
                            <div class="flex-1">
                                <h3 class="font-bold text-lg text-gray-800 group-hover:text-honey-600 transition"><?php echo $c['name']; ?></h3>
                                <p class="text-sm text-gray-500 font-medium"><?php echo $c['teacher']; ?></p>
                            </div>
                            <div class="w-10 h-10 rounded-full border border-gray-200 flex items-center justify-center text-gray-400 group-hover:border-honey-500 group-hover:text-honey-600 transition">
                                <i class="ph-bold ph-arrow-right"></i>
                            </div>
                        </a>
                        <?php endwhile; ?>
                    </div>
                </div>

            </div>

            <div class="bg-white p-8 rounded-[2rem] shadow-sm border border-gray-100 h-fit">
                <h2 class="text-xl font-extrabold text-gray-800 mb-8 flex items-center gap-2">
                    <i class="ph-fill ph-calendar-check text-honey-500"></i> Lịch thi
                </h2>

                <div class="relative border-l-2 border-gray-100 ml-3 space-y-8 pb-4">
                    <?php if(mysqli_num_rows($upcoming) == 0): ?>
                        <div class="pl-8 text-gray-400 italic text-sm">Không có bài thi sắp tới.</div>
                    <?php else: while($e = mysqli_fetch_assoc($upcoming)): 
                        $t = strtotime($e['exam_date']);
                    ?>
                    <div class="relative pl-8">
                        <div class="absolute -left-[9px] top-0 w-4 h-4 rounded-full border-4 border-white bg-honey-500 shadow-sm"></div>
                        
                        <div class="text-xs font-bold text-honey-600 uppercase mb-1 tracking-wider">
                            <?php echo date('d/m/Y', $t); ?> • <?php echo date('H:i', $t); ?>
                        </div>
                        <h4 class="font-bold text-gray-800 text-lg leading-tight mb-1">
                            <?php echo $e['exam_title']; ?>
                        </h4>
                        <p class="text-sm text-gray-500 font-medium bg-gray-50 inline-block px-2 py-1 rounded-lg">
                            <?php echo $e['class_name']; ?>
                        </p>
                    </div>
                    <?php endwhile; endif; ?>
                </div>

                <div class="mt-8 bg-gradient-to-br from-gray-900 to-gray-800 rounded-2xl p-6 text-white text-center relative overflow-hidden">
                    <div class="relative z-10">
                        <p class="font-bold text-lg mb-2">Cần ôn tập?</p>
                        <p class="text-sm text-gray-400 mb-4">Xem lại các bài đã làm để rút kinh nghiệm.</p>
                        <a href="student_dashboard.php" class="inline-block bg-white text-gray-900 px-6 py-2 rounded-xl font-bold text-sm hover:bg-honey-500 hover:text-white transition">Xem lịch sử</a>
                    </div>
                    <i class="ph-duotone ph-student absolute -bottom-4 -right-4 text-8xl opacity-10"></i>
                </div>
            </div>

        </div>

    </div>

    <script>
        const ctx = document.getElementById('performanceChart').getContext('2d');
        
        // Gradient màu cam/vàng
        let gradient = ctx.createLinearGradient(0, 0, 0, 300);
        gradient.addColorStop(0, 'rgba(255, 179, 0, 0.2)');
        gradient.addColorStop(1, 'rgba(255, 179, 0, 0)');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode($chart_labels); ?>,
                datasets: [{
                    label: 'Điểm số',
                    data: <?php echo json_encode($chart_data); ?>,
                    borderColor: '#FFB300',
                    backgroundColor: gradient,
                    borderWidth: 3,
                    tension: 0.4, // Đường cong mềm mại
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#FFB300',
                    pointBorderWidth: 3,
                    pointRadius: 6,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, max: 10, grid: { borderDash: [5, 5] } },
                    x: { grid: { display: false } }
                }
            }
        });
    </script>
</body>
</html>