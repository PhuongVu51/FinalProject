<?php
include "connection.php";
include "auth.php";
requireRole(['teacher','student']);

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$news = null;
if($id > 0){
    $res = mysqli_query($link, "SELECT * FROM news WHERE id=$id");
    $news = mysqli_fetch_assoc($res);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title><?php echo $news ? htmlspecialchars($news['title']) : 'Tin tức'; ?></title>
    <link rel="stylesheet" href="https://site-assets.fontawesome.com/releases/v6.4.2/css/all.css">
    <link rel="stylesheet" href="dashboard_style.css">
</head>
<body>
    <?php include "includes/sidebar.php"; ?>
    <div class="main-wrapper">
        <?php include "includes/topbar.php"; ?>
        <div class="content-scroll">
            <div style="margin-bottom:12px; display:flex; align-items:center; gap:8px;">
                <a href="news.php" style="display:inline-flex; align-items:center; gap:6px; color:#0F172A; text-decoration:none; font-weight:700;">
                    <i class="fa-solid fa-arrow-left"></i> Quay lại
                </a>
            </div>
            <?php if(!$news): ?>
                <div class="card" style="padding:16px; color:#B91C1C; background:#FEF2F2; border:1px solid #FECACA;">Bài viết không tồn tại.</div>
            <?php else: ?>
                <div class="card" style="padding:24px;">
                    <div style="font-size:26px; font-weight:900; color:#0F172A; margin-bottom:6px;">
                        <?php echo htmlspecialchars($news['title']); ?>
                    </div>
                    <div style="color:#94A3B8; font-size:13px; display:flex; align-items:center; gap:6px; margin-bottom:18px;">
                        <i class="fa-regular fa-clock"></i> <?php echo date('d/m/Y H:i', strtotime($news['created_at'])); ?>
                    </div>
                    <div style="color:#0F172A; line-height:1.7; font-size:15px; white-space:pre-line;">
                        <?php echo htmlspecialchars($news['content']); ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
