<?php
include "connection.php";
include "auth.php";
requireRole(['admin', 'teacher']);

// Hàm đếm nhanh - FIXED: Use role_id instead of role
function getC($link, $sql){ $r=mysqli_fetch_assoc(mysqli_query($link, $sql)); return $r['c']; }

// SỬA LẠI: Dùng cột 'role' và tìm theo tên 'student'/'teacher'
$s_count = getC($link, "SELECT COUNT(*) as c FROM users WHERE role='student'"); 
$t_count = getC($link, "SELECT COUNT(*) as c FROM users WHERE role='teacher'");
$c_count = getC($link, "SELECT COUNT(*) as c FROM classes");
$e_count = getC($link, "SELECT COUNT(*) as c FROM exams");
?>
<!DOCTYPE html>
<html lang="en">
<link rel="stylesheet" href="https://site-assets.fontawesome.com/releases/v6.4.2/css/all.css">
<head>
    <title>Dashboard | Teacher Bee</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="dashboard_style.css">
</head>
<body>

    <?php include "includes/sidebar.php"; ?>

    <div class="main-wrapper">
        <?php include "includes/topbar.php"; ?>

        <div class="content-scroll">
            
            <div class="stats-grid">
                <div class="stat-item">
                    <div class="stat-info">
                        <p>Tổng Học Sinh</p>
                        <h3><?php echo $s_count; ?></h3>
                    </div>
                    <div class="stat-icon" style="background:#EFF6FF; color:#3B82F6;">
                        <i class="fa-solid fa-user-graduate"></i>
                    </div>
                </div>
                
                <div class="stat-item">
                    <div class="stat-info">
                        <p>Giáo Viên</p>
                        <h3><?php echo $t_count; ?></h3>
                    </div>
                    <div class="stat-icon" style="background:#ECFDF5; color:#10B981;">
                        <i class="fa-solid fa-chalkboard-user"></i>
                    </div>
                </div>

                <div class="stat-item">
                    <div class="stat-info">
                        <p>Lớp Học</p>
                        <h3><?php echo $c_count; ?></h3>
                    </div>
                    <div class="stat-icon" style="background:#FFFBEB; color:#F59E0B;">
                        <i class="fa-solid fa-chalkboard"></i>
                    </div>
                </div>

                <div class="stat-item">
                    <div class="stat-info">
                        <p>Bài Thi</p>
                        <h3><?php echo $e_count; ?></h3>
                    </div>
                    <div class="stat-icon" style="background:#F3E8FF; color:#9333EA;">
                        <i class="fa-solid fa-file-pen"></i>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">📰 Tin Tức & Thông Báo Mới</h3>
                    <a href="manage_news.php" class="btn-secondary" style="padding:6px 12px; font-size:12px;">Quản lý</a>
                </div>
                <table class="dataTable">
                    <thead>
                        <tr>
                            <th width="150">Ngày đăng</th>
                            <th>Tiêu đề</th>
                            <th>Nội dung tóm tắt</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $res = mysqli_query($link, "SELECT * FROM news ORDER BY created_at DESC LIMIT 5");
                        while($r = mysqli_fetch_assoc($res)): ?>
                        <tr>
                            <td style="color:#64748B;">
                                <i class="fa-regular fa-clock"></i> <?php echo date('d/m/Y', strtotime($r['created_at'])); ?>
                            </td>
                            <td style="font-weight:600; color:#B45309;"><?php echo htmlspecialchars($r['title']); ?></td>
                            <td style="color:#475569;"><?php echo htmlspecialchars(substr($r['content'], 0, 80)) . '...'; ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

            <?php include "includes/footer.php"; ?>
        </div>
    </div>
</body>
</html>