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
<head>
    <title>Tin Tức</title>
    <link rel="stylesheet" href="https://site-assets.fontawesome.com/releases/v6.4.2/css/all.css">
    <link rel="stylesheet" href="dashboard_style.css">
    <style>
        body { background-color: #F8FAFC; font-family: 'Segoe UI', system-ui, sans-serif; }
        
        /* CHUẨN HÓA TIÊU ĐỀ */
        .page-title { font-size: 24px; font-weight: 800; color: #1E293B; margin-bottom: 30px; margin-top: 0; }
        
        .card { background: white; border-radius: 16px; border: 1px solid #E2E8F0; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); padding: 24px; margin-bottom: 24px; }
        .form-control { border: 1px solid #CBD5E1; border-radius: 8px; padding: 10px 14px; width: 100%; transition: border 0.2s; background: #F8FAFC; }
        .form-control:focus { border-color: #F59E0B; background: white; outline: none; }
        .btn-primary { background: #F59E0B; color: white; border: none; padding: 12px 24px; border-radius: 8px; font-weight: 600; cursor: pointer; transition: 0.2s; display: inline-flex; align-items: center; gap: 8px; }
        .btn-primary:hover { background: #D97706; transform: translateY(-1px); }
        .news-item { border-bottom: 1px solid #F1F5F9; padding-bottom: 20px; margin-bottom: 20px; display: flex; justify-content: space-between; align-items: flex-start; }
        .news-item:last-child { border-bottom: none; margin-bottom: 0; padding-bottom: 0; }
    </style>
</head>
<body>
<?php include "includes/sidebar.php"; ?>
<div class="main-wrapper">
    <?php include "includes/topbar.php"; ?>
    <div class="content-scroll">
    
    <h2 class="page-title">Hệ Thống Quản Lý</h2>
        
    <div class="card">
        <h3 style="margin:0 0 20px 0; color:#334155;">Đăng Tin Mới</h3>
        <form method="post">
            <div class="form-group" style="margin-bottom:15px;">
                <label style="font-weight:600; font-size:14px; color:#475569; display:block; margin-bottom:8px;">Tiêu đề bài viết</label>
                <input type="text" name="title" class="form-control" placeholder="Ví dụ: Thông báo nghỉ lễ..." required>
            </div>
            <div class="form-group" style="margin-bottom:20px;">
                <label style="font-weight:600; font-size:14px; color:#475569; display:block; margin-bottom:8px;">Nội dung chi tiết</label>
                <textarea name="content" class="form-control" rows="4" placeholder="Nhập nội dung..." required></textarea>
            </div>
            <button name="add" class="btn-primary"><i class="fa-solid fa-paper-plane"></i> Đăng Bài Viết</button>
        </form>
    </div>

    <div class="card">
        <h3 style="margin:0 0 25px 0; color:#334155; display:flex; align-items:center; gap:10px;"><i class="fa-solid fa-list-ul" style="color:#64748B;"></i> Danh Sách Tin Tức</h3>
        <?php $res=mysqli_query($link, "SELECT * FROM news ORDER BY created_at DESC");
        while($r=mysqli_fetch_assoc($res)): ?>
        <div class="news-item">
            <div style="padding-right: 20px;">
                <h4 style="margin:0 0 6px 0; color:#1E293B; font-size:16px; font-weight:700;"><?php echo $r['title']; ?></h4>
                <div style="font-size:12px; color:#94A3B8; margin-bottom:10px; display:flex; align-items:center; gap:6px;"><i class="fa-regular fa-clock"></i> <?php echo date('d/m/Y H:i', strtotime($r['created_at'])); ?></div>
                <p style="margin:0; color:#475569; font-size:14px; line-height:1.6;"><?php echo nl2br($r['content']); ?></p>
            </div>
            <a href="?del=<?php echo $r['id']; ?>" onclick="return confirm('Xóa tin này?')" class="action-btn btn-delete" style="background:#FEF2F2; color:#EF4444; width:36px; height:36px; display:flex; align-items:center; justify-content:center; border-radius:8px; transition:0.2s;" title="Xóa"><i class="fa-solid fa-trash"></i></a>
        </div>
        <?php endwhile; ?>
    </div>
</div>
</div>
</body></html>