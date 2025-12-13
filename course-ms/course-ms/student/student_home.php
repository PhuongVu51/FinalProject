<?php
include "../config/connection.php";
include "../config/auth.php";
requireRole(['student']);
?>
<!DOCTYPE html>
<html lang="en">
<link rel="stylesheet" href="https://site-assets.fontawesome.com/releases/v6.4.2/css/all.css">
<head>
    <title>Trang Chủ | Student</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../shared/css/dashboard_style.css">
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
                <div style="margin-top:20px;">
                    <a href="student_classes.php" class="btn-secondary" style="background:white; color:#B45309; border:none;">
                        <i class="fa-solid fa-magnifying-glass"></i> Tìm Lớp Học Mới
                    </a>
                </div>
            </div>

            <div style="display:grid; grid-template-columns: 2fr 1fr; gap:24px;">
                <div class="card">
                    <div class="card-header"><h3 class="card-title">📢 Bảng Tin Nhà Trường</h3></div>
                    <table class="dataTable">
                        <?php 
                        $res = mysqli_query($link, "SELECT * FROM news ORDER BY created_at DESC LIMIT 5");
                        while($r = mysqli_fetch_assoc($res)): ?>
                        <tr>
                            <td width="120" style="color:#64748B; font-size:12px;"><?php echo date('d/m/Y', strtotime($r['created_at'])); ?></td>
                            <td>
                                <div style="font-weight:700; color:#B45309; margin-bottom:4px;"><?php echo $r['title']; ?></div>
                                <div style="color:#475569; font-size:13px;"><?php echo $r['content']; ?></div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </table>
                </div>

                <div>
                    <div class="card" style="text-align:center;">
                        <div style="width:60px; height:60px; background:#ECFDF5; color:#10B981; border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 15px; font-size:24px;">
                            <i class="fa-solid fa-chart-pie"></i>
                        </div>
                        <h3 style="margin:0 0 10px 0; font-size:16px;">Kết Quả Học Tập</h3>
                        <p style="color:#64748B; font-size:13px; margin-bottom:15px;">Xem điểm số các bài kiểm tra gần đây của bạn.</p>
                        <a href="student_dashboard.php" class="btn-primary" style="width:100%">Xem Điểm</a>
                    </div>
                </div>
            </div>

            <?php include "includes/footer.php"; ?>
        </div>
    </div>
</body>
</html>