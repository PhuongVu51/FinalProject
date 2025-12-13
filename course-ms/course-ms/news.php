<?php
include "connection.php";
include "auth.php";
requireRole(['teacher','student']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Tin tức</title>
    <link rel="stylesheet" href="https://site-assets.fontawesome.com/releases/v6.4.2/css/all.css">
    <link rel="stylesheet" href="dashboard_style.css">
</head>
<body>
    <?php include "includes/sidebar.php"; ?>
    <div class="main-wrapper">
        <?php include "includes/topbar.php"; ?>
        <div class="content-scroll">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">📰 Tin Tức & Thông Báo</h3>
                </div>
                <?php 
                $res = mysqli_query($link, "SELECT * FROM news ORDER BY created_at DESC");
                if(mysqli_num_rows($res)==0): ?>
                    <div style="padding:14px; color:#94A3B8;">Chưa có tin tức.</div>
                <?php else: 
                    while($r = mysqli_fetch_assoc($res)):
                        $excerpt = mb_strimwidth(strip_tags($r['content']), 0, 180, '...');
                ?>
                    <a href="news_detail.php?id=<?php echo $r['id']; ?>" style="text-decoration:none; color:inherit; display:block;">
                        <div class="card" style="margin:0 0 14px 0; padding:20px; box-shadow:none; border:1px solid #E2E8F0;">
                            <div style="font-size:18px; font-weight:800; color:#0F172A; margin-bottom:6px;">
                                <?php echo htmlspecialchars($r['title']); ?>
                            </div>
                            <div style="color:#94A3B8; font-size:14px; line-height:1.6; margin-bottom:10px;">
                                <?php echo nl2br(htmlspecialchars($excerpt)); ?>
                            </div>
                            <div style="font-size:12px; color:#94A3B8; display:flex; align-items:center; gap:6px;">
                                <i class="fa-regular fa-clock"></i> <?php echo date('d/m/Y', strtotime($r['created_at'])); ?>
                            </div>
                        </div>
                    </a>
                <?php endwhile; endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
