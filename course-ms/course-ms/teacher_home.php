<?php
include "connection.php";
include "auth.php";
requireRole(['teacher']);

$tid = intval($_SESSION['teacher_id']);

$classCount = 0;
$examTotal = 0;
$latestNews = null;

// Thống kê nhanh: số lớp đang dạy, tổng bài kiểm tra
$classCountRow = mysqli_fetch_assoc(mysqli_query($link, "SELECT COUNT(*) as c FROM classes WHERE teacher_id=$tid"));
$classCount = $classCountRow['c'] ?? 0;
$examTotalRow = mysqli_fetch_assoc(mysqli_query($link, "SELECT COUNT(*) as c FROM exams WHERE teacher_id=$tid"));
$examTotal = $examTotalRow['c'] ?? 0;
$latestNews = mysqli_fetch_assoc(mysqli_query($link, "SELECT title, content, created_at FROM news ORDER BY created_at DESC LIMIT 1"));
?>
<!DOCTYPE html>
<html lang="en">
<link rel="stylesheet" href="https://site-assets.fontawesome.com/releases/v6.4.2/css/all.css">
<head>
    <title>Dashboard | Teacher</title>
    <link rel="stylesheet" href="dashboard_style.css">
</head>
<body>
    <?php include "includes/sidebar.php"; ?>
    <div class="main-wrapper">
        <?php include "includes/topbar.php"; ?>
        <div class="content-scroll">
            <div class="hero-banner" style="margin-bottom:24px;">
                <i class="fa-solid fa-bee hero-icon"></i>
                <h1>Xin chào, <?php echo $_SESSION['full_name']; ?>! 👋</h1>
            </div>

            <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap:16px; margin-bottom:24px;">
                <a href="teacher_classes.php" class="card" style="margin:0; display:flex; align-items:center; justify-content:space-between; text-decoration:none; color:inherit;">
                    <div>
                        <div style="color:#94A3B8; font-size:12px; text-transform:uppercase; font-weight:800; letter-spacing:0.5px;">Lớp đang giảng dạy</div>
                        <div style="font-size:28px; font-weight:900; color:#0F172A;"><?php echo $classCount; ?></div>
                    </div>
                    <div class="stat-icon" style="background:#EFF6FF; color:#3B82F6; width:56px; height:56px; border-radius:16px; display:grid; place-items:center; font-size:22px;">
                        <i class="fa-solid fa-chalkboard"></i>
                    </div>
                </a>
                <a href="manage_exams.php" class="card" style="margin:0; display:flex; align-items:center; justify-content:space-between; text-decoration:none; color:inherit;">
                    <div>
                        <div style="color:#94A3B8; font-size:12px; text-transform:uppercase; font-weight:800; letter-spacing:0.5px;">Tổng bài kiểm tra</div>
                        <div style="font-size:28px; font-weight:900; color:#0F172A;"><?php echo $examTotal; ?></div>
                    </div>
                    <div class="stat-icon" style="background:#FFF7ED; color:#F59E0B; width:56px; height:56px; border-radius:16px; display:grid; place-items:center; font-size:22px;">
                        <i class="fa-solid fa-file-pen"></i>
                    </div>
                </a>
            </div>

            <a href="news.php" class="card" style="margin-bottom:24px; display:block; text-decoration:none; color:inherit;">
                <div class="card-header" style="align-items:center;">
                    <h3 style="margin:0;">Tin tức mới nhất</h3>
                    <div style="color:#94A3B8; font-size:13px;">
                        <?php echo $latestNews ? date('d/m/Y H:i', strtotime($latestNews['created_at'])) : ''; ?>
                    </div>
                </div>
                <?php if($latestNews): ?>
                    <div style="font-size:18px; font-weight:800; color:#0F172A; margin-bottom:8px;">
                        <?php echo $latestNews['title']; ?>
                    </div>
                    <div style="color:#475569; line-height:1.6;">
                        <?php echo nl2br(htmlspecialchars($latestNews['content'])); ?>
                    </div>
                <?php else: ?>
                    <div style="color:#94A3B8;">Chưa có tin tức.</div>
                <?php endif; ?>
            </a>

        </div>
    </div>
</body>
</html>
