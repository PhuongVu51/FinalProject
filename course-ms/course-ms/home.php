<?php
session_start();
if(!isset($_SESSION['username'])){ header('location:login.php'); }
include "connection.php";
include "auth.php"; 

// --- 1. LOGIC LẤY SỐ LIỆU KPI ---
function getCount($link, $table) {
    $q = mysqli_query($link, "SELECT COUNT(*) as total FROM $table");
    return mysqli_fetch_assoc($q)['total'];
}

// Điểm trung bình toàn hệ thống (fallback 0 nếu bảng chưa tạo)
if (tableExists($link, 'scores')) {
    $avg_query = mysqli_query($link, "SELECT AVG(score) as avg_score FROM scores");
    $avg_data = mysqli_fetch_assoc($avg_query);
    $system_avg = number_format($avg_data['avg_score'], 1); // Làm tròn 1 số thập phân
} else {
    $system_avg = number_format(0, 1);
}

// --- 2. LOGIC BIỂU ĐỒ 1: XU HƯỚNG ĐIỂM SỐ (LINE CHART) ---
// Lấy điểm trung bình của 5 bài thi gần nhất để xem xu hướng
$trend_query = "SELECT e.exam_title, AVG(s.score) as avg_score 
                FROM exams e 
                JOIN scores s ON e.id = s.exam_id 
                GROUP BY e.id 
                ORDER BY e.exam_date DESC LIMIT 5";
$trend_res = mysqli_query($link, $trend_query);

$trend_labels = [];
$trend_data = [];
// Đảo ngược mảng để hiển thị theo thời gian từ cũ đến mới
$temp_rows = [];
while($row = mysqli_fetch_assoc($trend_res)) {
    $temp_rows[] = $row;
}
$temp_rows = array_reverse($temp_rows);

foreach($temp_rows as $row) {
    $trend_labels[] = $row['exam_title'];
    $trend_data[] = number_format($row['avg_score'], 1);
}

// --- 3. LOGIC BIỂU ĐỒ 2: PHÂN LOẠI HỌC LỰC (DOUGHNUT CHART) ---
$dist_query = "SELECT 
    SUM(CASE WHEN score >= 8 THEN 1 ELSE 0 END) as gio,
    SUM(CASE WHEN score >= 6.5 AND score < 8 THEN 1 ELSE 0 END) as kha,
    SUM(CASE WHEN score >= 5 AND score < 6.5 THEN 1 ELSE 0 END) as tb,
    SUM(CASE WHEN score < 5 THEN 1 ELSE 0 END) as yeu
FROM scores";
$dist_res = mysqli_fetch_assoc(mysqli_query($link, $dist_query));

// --- 4. TOP 5 HỌC SINH XUẤT SẮC ---
$top_query = "SELECT st.full_name, st.student_id_code, AVG(sc.score) as avg_score, c.name as class_name
              FROM students st
              JOIN scores sc ON st.id = sc.student_id
              LEFT JOIN classes c ON st.class_id = c.id
              GROUP BY st.id
              ORDER BY avg_score DESC
              LIMIT 5";
$top_res = mysqli_query($link, $top_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Dashboard | CourseMS Pro</title>
    <link rel="stylesheet" href="https://site-assets.fontawesome.com/releases/v6.4.0/css/all.css">
    <link rel="stylesheet" href="dashboard_style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

    <?php include "includes/sidebar.php"; ?>

    <div class="main-wrapper">
        <?php include "includes/topbar.php"; ?>

        <div class="content-scroll">
            
            <h2 class="section-title" style="border:none; margin-bottom: 20px;">Overview Statistics</h2>

            <div class="stats-grid">
                <div class="card stat-item">
                    <div>
                        <p class="stat-label">Total Students</p>
                        <h3 class="stat-value"><?php echo getCount($link, 'students'); ?></h3>
                    </div>
                    <div class="stat-icon" style="background:#EFF6FF; color:#3B82F6;">
                        <i class="fa-duotone fa-users"></i>
                    </div>
                </div>
                
                <div class="card stat-item">
                    <div>
                        <p class="stat-label">Total Classes</p>
                        <h3 class="stat-value"><?php echo getCount($link, 'classes'); ?></h3>
                    </div>
                    <div class="stat-icon" style="background:#ECFDF5; color:#10B981;">
                        <i class="fa-duotone fa-chalkboard"></i>
                    </div>
                </div>

                <div class="card stat-item">
                    <div>
                        <p class="stat-label">Avg. Score (System)</p>
                        <h3 class="stat-value" style="color: #F59E0B;"><?php echo $system_avg; ?></h3>
                    </div>
                    <div class="stat-icon" style="background:#FFFBEB; color:#F59E0B;">
                        <i class="fa-duotone fa-star"></i>
                    </div>
                </div>

                <div class="card stat-item">
                    <div>
                        <p class="stat-label">Total Exams</p>
                        <h3 class="stat-value"><?php echo getCount($link, 'exams'); ?></h3>
                    </div>
                    <div class="stat-icon" style="background:#F3E8FF; color:#9333EA;">
                        <i class="fa-duotone fa-file-certificate"></i>
                    </div>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px; margin-bottom: 24px;">
                
                <div class="card" style="margin-bottom:0;">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:15px;">
                        <h3 style="font-size:16px; font-weight:600; margin:0;">📈 Exam Performance Trend</h3>
                        <span style="font-size:12px; color:#64748B;">Last 5 Exams</span>
                    </div>
                    <div style="height: 300px;">
                        <canvas id="trendChart"></canvas>
                    </div>
                </div>

                <div class="card" style="margin-bottom:0;">
                    <h3 style="font-size:16px; font-weight:600; margin:0 0 15px 0;">📊 Grade Distribution</h3>
                    <div style="height: 250px; position: relative;">
                        <canvas id="distChart"></canvas>
                    </div>
                    <div style="text-align:center; margin-top:15px; font-size:13px; color:#64748B;">
                        Based on all graded exams
                    </div>
                </div>
            </div>

            <div class="card">
                <h3 style="font-size:16px; font-weight:600; margin:0 0 20px 0;">🏆 Top Performing Students</h3>
                <table class="dataTable" style="width:100%">
                    <thead>
                        <tr>
                            <th>Rank</th>
                            <th>Student Name</th>
                            <th>ID Code</th>
                            <th>Class</th>
                            <th>Avg Score</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $rank = 1;
                        while($std = mysqli_fetch_assoc($top_res)): 
                            // Icon cúp cho top 3
                            $icon = "";
                            if($rank == 1) $icon = "🥇";
                            elseif($rank == 2) $icon = "🥈";
                            elseif($rank == 3) $icon = "🥉";
                            else $icon = "#".$rank;
                        ?>
                        <tr>
                            <td style="font-weight:bold;"><?php echo $icon; ?></td>
                            <td style="font-weight:600; color:#1E293B;"><?php echo htmlspecialchars($std['full_name']); ?></td>
                            <td style="color:#64748B;"><?php echo $std['student_id_code']; ?></td>
                            <td><span style="background:#F1F5F9; padding:2px 8px; border-radius:4px; font-size:12px;"><?php echo $std['class_name']; ?></span></td>
                            <td>
                                <span style="color:#10B981; font-weight:700;"><?php echo number_format($std['avg_score'], 1); ?></span>
                            </td>
                        </tr>
                        <?php $rank++; endwhile; ?>
                    </tbody>
                </table>
            </div>

            <?php include "includes/footer.php"; ?>
        </div>
    </div>

    <script>
        // 1. Line Chart (Xu hướng)
        const ctxTrend = document.getElementById('trendChart').getContext('2d');
        new Chart(ctxTrend, {
            type: 'line',
            data: {
                labels: <?php echo json_encode($trend_labels); ?>,
                datasets: [{
                    label: 'Average Score',
                    data: <?php echo json_encode($trend_data); ?>,
                    borderColor: '#F59E0B', // Màu vàng chủ đạo
                    backgroundColor: 'rgba(245, 158, 11, 0.1)', // Màu nền mờ dưới đường
                    borderWidth: 3,
                    pointBackgroundColor: '#FFFFFF',
                    pointBorderColor: '#F59E0B',
                    pointRadius: 5,
                    fill: true,
                    tension: 0.4 // Đường cong mềm mại
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: { beginAtZero: true, max: 10, grid: { borderDash: [2, 4] } },
                    x: { grid: { display: false } }
                }
            }
        });

        // 2. Doughnut Chart (Phân loại)
        const ctxDist = document.getElementById('distChart').getContext('2d');
        new Chart(ctxDist, {
            type: 'doughnut',
            data: {
                labels: ['Excellent (>=8)', 'Good (6.5-8)', 'Average (5-6.5)', 'Weak (<5)'],
                datasets: [{
                    data: [
                        <?php echo $dist_res['gio'] ?? 0; ?>, 
                        <?php echo $dist_res['kha'] ?? 0; ?>, 
                        <?php echo $dist_res['tb'] ?? 0; ?>, 
                        <?php echo $dist_res['yeu'] ?? 0; ?>
                    ],
                    backgroundColor: [
                        '#10B981', // Xanh lá (Giỏi)
                        '#3B82F6', // Xanh dương (Khá)
                        '#F59E0B', // Vàng (TB)
                        '#EF4444'  // Đỏ (Yếu)
                    ],
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%', // Làm vòng tròn mỏng đi cho đẹp
                plugins: {
                    legend: { position: 'bottom', labels: { usePointStyle: true, boxWidth: 8 } }
                }
            }
        });
    </script>
</body>
</html>