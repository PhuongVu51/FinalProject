<?php
include "connection.php"; include "auth.php"; requireRole(['admin']);

if(isset($_POST['add'])){
    $t=mysqli_real_escape_string($link, $_POST['title']); $c=mysqli_real_escape_string($link, $_POST['content']);
    mysqli_query($link, "INSERT INTO news (title, content) VALUES ('$t','$c')"); header("Location: manage_news.php");
}
if(isset($_GET['del'])){ mysqli_query($link, "DELETE FROM news WHERE id=".intval($_GET['del'])); header("Location: manage_news.php"); }
?>
<!DOCTYPE html>
<html>
<link rel="stylesheet" href="https://site-assets.fontawesome.com/releases/v6.4.2/css/all.css">
<head>
    <title>Tin Tức</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="dashboard_style.css">
</head>
<body>
<?php include "includes/sidebar.php"; ?>
<div class="main-wrapper">
    <div class="topbar"><h2 class="page-title">Bảng Tin Nhà Trường</h2></div>
    <div class="content-scroll">
        
    <div class="card">
        <form method="post">
            <div class="form-group">
                <label>Tiêu đề bài viết</label>
                <input type="text" name="title" class="form-control" placeholder="Ví dụ: Thông báo nghỉ lễ..." required>
            </div>
            <div class="form-group">
                <label>Nội dung chi tiết</label>
                <textarea name="content" class="form-control" rows="4" placeholder="Nhập nội dung..." required></textarea>
            </div>
            <button name="add" class="btn-primary"><i class="fa-solid fa-paper-plane"></i> Đăng Bài Viết</button>
        </form>
    </div>

    <div class="card">
        <h3 style="margin-bottom:20px;">Danh Sách Tin Tức</h3>
        <?php $res=mysqli_query($link, "SELECT * FROM news ORDER BY created_at DESC");
        while($r=mysqli_fetch_assoc($res)): ?>
        <div style="border-bottom:1px solid #F1F5F9; padding-bottom:15px; margin-bottom:15px; display:flex; justify-content:space-between; align-items:start;">
            <div>
                <h4 style="margin:0 0 5px 0; color:#1E293B; font-size:16px;"><?php echo $r['title']; ?></h4>
                <div style="font-size:12px; color:#94A3B8; margin-bottom:8px;">
                    <i class="fa-regular fa-clock"></i> <?php echo date('d/m/Y H:i', strtotime($r['created_at'])); ?>
                </div>
                <p style="margin:0; color:#475569; font-size:14px; line-height:1.5;"><?php echo nl2br($r['content']); ?></p>
            </div>
            <a href="?del=<?php echo $r['id']; ?>" onclick="return confirm('Xóa tin này?')" class="action-btn btn-delete" title="Xóa">
                <i class="fa-solid fa-trash"></i>
            </a>
        </div>
        <?php endwhile; ?>
    </div>
</div>
</body></html>