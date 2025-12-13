<?php
include "connection.php";
include "auth.php";
requireRole(['teacher']);

$tid = intval($_SESSION['teacher_id']);

$classCount = 0;
$examTotal = 0;
$latestNews = [];

// Rút gọn nội dung tin để chỉ hiển thị 1-2 dòng
function newsPreview($text, $limit = 160){
    $plain = strip_tags($text);
    $plain = trim(preg_replace('/\s+/', ' ', $plain));
    if(strlen($plain) <= $limit) return $plain;
    return substr($plain, 0, $limit) . '...';
}

// Thống kê nhanh: số lớp đang dạy, tổng bài kiểm tra
$classCountRow = mysqli_fetch_assoc(mysqli_query($link, "SELECT COUNT(*) as c FROM classes WHERE teacher_id=$tid"));
$classCount = $classCountRow['c'] ?? 0;
$examTotalRow = mysqli_fetch_assoc(mysqli_query($link, "SELECT COUNT(*) as c FROM exams WHERE teacher_id=$tid"));
$examTotal = $examTotalRow['c'] ?? 0;
$newsRes = mysqli_query($link, "SELECT id, title, content, created_at FROM news ORDER BY created_at DESC LIMIT 3");
while($n = mysqli_fetch_assoc($newsRes)) $latestNews[] = $n;
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

            <div class="card" style="margin-bottom:24px;">
                <div class="card-header" style="display:flex; align-items:center; gap:12px;">
                    <h3 style="margin:0;">Tin tức mới nhất</h3>
                    <a href="news.php" class="btn-primary" style="margin-left:auto; padding:8px 12px; font-size:13px; box-shadow:none; background:#E2E8F0; color:#0F172A; border:1px solid #CBD5E1;">Xem tất cả</a>
                </div>
                <?php if(!empty($latestNews)): ?>
                    <div style="margin-top:12px; display:grid; gap:14px;">
                    <?php foreach($latestNews as $item): ?>
                        <a href="news_detail.php?id=<?php echo $item['id']; ?>" style="text-decoration:none; color:inherit; display:block;">
                            <div class="card" style="margin:0; padding:20px; box-shadow:none; border:1px solid #E2E8F0;">
                                <div style="font-size:18px; font-weight:800; color:#0F172A; margin-bottom:6px;">
                                    <?php echo htmlspecialchars($item['title']); ?>
                                </div>
                                <div style="color:#94A3B8; font-size:14px; line-height:1.6; margin-bottom:10px;">
                                    <?php echo nl2br(htmlspecialchars(newsPreview($item['content'], 180))); ?>
                                </div>
                                <div style="font-size:12px; color:#94A3B8; display:flex; align-items:center; gap:6px;">
                                    <i class="fa-regular fa-clock"></i> <?php echo date('d/m/Y', strtotime($item['created_at'])); ?>
                                </div>
                            </div>
                        </a>
                    <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div style="padding:14px; color:#94A3B8;">Chưa có tin tức.</div>
                <?php endif; ?>
            </div>

        </div>
    </div>
</body>
</html>
