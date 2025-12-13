<?php
include "connection.php";
include "auth.php";
requireRole(['student']);
$sid = $_SESSION['student_id'];
?>
<!DOCTYPE html>
<html lang="en">
<link rel="stylesheet" href="https://site-assets.fontawesome.com/releases/v6.4.2/css/all.css">
<head>
    <title>Trang Chủ | Student</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="dashboard_style.css">

</head>
<body>
    <?php include "includes/sidebar.php"; ?>
    <div class="main-wrapper">
        <?php include "includes/topbar.php"; ?>
        <div class="content-scroll">
            
            <div class="hero-banner">
                <i class="fa-solid fa-bee hero-icon"></i>
                <h1>Chào mừng, <?php echo $_SESSION['full_name']; ?>! 👋</h1>
                <p>Chúc bạn một ngày học tập thật hiệu quả và tràn đầy năng lượng.</p>
            </div>

            <!-- Search Bar -->
            <div class="search-bar">
                <input type="text" id="searchInput" placeholder="🔍 Tìm kiếm tin tức, lớp học, điểm số..." onkeyup="searchContent()">
            </div>

            <!-- Quick Stats -->
            <div class="quick-stats">
                <div class="stat-card" onclick="location.href='#news'">
                    <div class="stat-icon news"><i class="fa-solid fa-newspaper"></i></div>
                    <div class="stat-number"><?php echo mysqli_num_rows(mysqli_query($link, "SELECT * FROM news")); ?></div>
                    <div class="stat-label">Tin Tức</div>
                </div>
                <div class="stat-card" onclick="location.href='student_classes.php'">
                    <div class="stat-icon classes"><i class="fa-solid fa-chalkboard"></i></div>
                    <div class="stat-number"><?php echo mysqli_num_rows(mysqli_query($link, "SELECT * FROM classes")); ?></div>
                    <div class="stat-label">Lớp Học</div>
                </div>
                <div class="stat-card" onclick="location.href='student_scores.php'">
                    <div class="stat-icon scores"><i class="fa-solid fa-chart-line"></i></div>
                    <div class="stat-number"><?php echo mysqli_num_rows(mysqli_query($link, "SELECT * FROM scores WHERE student_id=$sid")); ?></div>
                    <div class="stat-label">Kết Quả</div>
                </div>
            </div>

            <div style="display:grid; grid-template-columns: 2fr 1fr; gap:24px;">
                <!-- News Section -->
                <div class="card" id="news">
                    <div class="card-header">
                        <h3 class="card-title">📢 Tin Tức Nhà Trường</h3>
                    </div>
                    <table class="dataTable">
                        <?php 
                        $res = mysqli_query($link, "SELECT * FROM news ORDER BY created_at DESC LIMIT 5");
                        while($r = mysqli_fetch_assoc($res)): ?>
                        <tr class="news-item">
                            <td width="120" style="color:#64748B; font-size:12px;"><?php echo date('d/m/Y', strtotime($r['created_at'])); ?></td>
                            <td>
                                <div style="font-weight:700; color:#B45309; margin-bottom:4px;"><?php echo $r['title']; ?></div>
                                <div style="color:#475569; font-size:13px;"><?php echo $r['content']; ?></div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </table>
                </div>

                <!-- Quick Links -->
                <div>
                    <div class="card" style="text-align:center; margin-bottom:20px;">
                        <div style="width:60px; height:60px; background:#FFF3E0; color:#F57C00; border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 15px; font-size:24px;">
                            <i class="fa-solid fa-chalkboard-user"></i>
                        </div>
                        <h3 style="margin:0 0 10px 0; font-size:16px;">Lớp Học</h3>
                        <p style="color:#64748B; font-size:13px; margin-bottom:15px;">Xem và đăng ký các lớp học.</p>
                        <a href="student_classes.php" class="btn-primary" style="width:100%">Xem Lớp Học</a>
                    </div>

                    <div class="card" style="text-align:center;">
                        <div style="width:60px; height:60px; background:#ECFDF5; color:#10B981; border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 15px; font-size:24px;">
                            <i class="fa-solid fa-chart-pie"></i>
                        </div>
                        <h3 style="margin:0 0 10px 0; font-size:16px;">Kết Quả Học Tập</h3>
                        <p style="color:#64748B; font-size:13px; margin-bottom:15px;">Xem điểm số các bài kiểm tra.</p>
                        <a href="student_scores.php" class="btn-primary" style="width:100%">Xem Điểm</a>
                    </div>
                </div>
            </div>

            <?php include "includes/footer.php"; ?>
        </div>
    </div>

    <script>
        function searchContent() {
            const input = document.getElementById('searchInput').value.toLowerCase();
            const newsItems = document.querySelectorAll('.news-item');
            
            newsItems.forEach(item => {
                const text = item.textContent.toLowerCase();
                item.style.display = text.includes(input) ? '' : 'none';
            });
        }
    </script>
</body>
</html>