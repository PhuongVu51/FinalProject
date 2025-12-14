<?php
include "connection.php"; 
include "auth.php"; 
requireRole(['admin']);

if(isset($_POST['add'])){
    $t = mysqli_real_escape_string($link, $_POST['title']); 
    $c = mysqli_real_escape_string($link, $_POST['content']);
    mysqli_query($link, "INSERT INTO news (title, content) VALUES ('$t','$c')"); 
    header("Location: manage_news.php");
    exit;
}

if(isset($_GET['del'])){ 
    mysqli_query($link, "DELETE FROM news WHERE id=".intval($_GET['del'])); 
    header("Location: manage_news.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <title>Quản Lý Tin Tức | Teacher Bee</title>
    <link rel="stylesheet" href="https://site-assets.fontawesome.com/releases/v6.4.2/css/all.css">
    <link rel="stylesheet" href="dashboard_style.css">
    <link rel="stylesheet" href="admin_tables.css">
    <style>
        .two-column-layout {
            display: grid;
            grid-template-columns: 500px 1fr;
            gap: 24px;
            align-items: start;
        }
        
        .form-card {
            background: white;
            border-radius: 16px;
            border: 1px solid #E2E8F0;
            overflow: hidden;
            position: sticky;
            top: 20px;
        }
        
        .form-card-header {
            padding: 20px 24px;
            border-bottom: 1px solid #F1F5F9;
            background: #FAFBFC;
        }
        
        .form-card-header h3 {
            margin: 0;
            font-size: 18px;
            font-weight: 700;
            color: #1E293B;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .form-card-body {
            padding: 24px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #475569;
            margin-bottom: 8px;
        }
        
        .form-control {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid #E2E8F0;
            border-radius: 8px;
            background: #F8FAFC;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.2s;
            box-sizing: border-box;
            font-family: 'Be Vietnam Pro', sans-serif;
        }
        
        .form-control:focus {
            background: white;
            border-color: #F59E0B;
            outline: none;
        }
        
        textarea.form-control {
            resize: vertical;
            min-height: 150px;
            line-height: 1.6;
        }
        
        .btn-submit {
            width: 100%;
            padding: 12px;
            background: #F59E0B;
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        
        .btn-submit:hover {
            background: #D97706;
        }
        
        .news-list-card {
            background: white;
            border-radius: 16px;
            border: 1px solid #E2E8F0;
            overflow: hidden;
        }
        
        .news-item {
            padding: 24px;
            border-bottom: 1px solid #F1F5F9;
            display: flex;
            justify-content: space-between;
            gap: 20px;
            transition: all 0.2s;
        }
        
        .news-item:last-child {
            border-bottom: none;
        }
        
        .news-item:hover {
            background: #FAFBFC;
        }
        
        .news-content-wrapper {
            flex: 1;
        }
        
        .news-title {
            font-size: 16px;
            font-weight: 700;
            color: #1E293B;
            margin: 0 0 8px 0;
        }
        
        .news-meta {
            display: flex;
            align-items: center;
            gap: 6px;
            color: #94A3B8;
            font-size: 12px;
            margin-bottom: 12px;
        }
        
        .news-text {
            color: #64748B;
            font-size: 14px;
            line-height: 1.6;
            margin: 0;
        }
        
        .news-actions {
            display: flex;
            align-items: flex-start;
        }
    </style>
</head>
<body>
    <?php include "includes/sidebar.php"; ?>
    
    <div class="main-wrapper">
        <?php include "includes/topbar.php"; ?>
        
        <div class="content-scroll">
            <div class="page-header">
                <div>
                    <h2 class="page-title">Quản Lý Tin Tức</h2>
                    <p class="page-subtitle">
                        <a href="home.php" style="color: #94A3B8; text-decoration: none;">
                            &lt; Quay lại trang chủ
                        </a>
                    </p>
                </div>
            </div>
            
            <div class="two-column-layout">
                <!-- Add News Form -->
                <div class="form-card">
                    <div class="form-card-header">
                        <h3>
                            <i class="fa-solid fa-pen-to-square" style="color: #F59E0B;"></i>
                            Đăng Tin Mới
                        </h3>
                    </div>
                    <div class="form-card-body">
                        <form method="post">
                            <div class="form-group">
                                <label class="form-label">Tiêu đề bài viết</label>
                                <input type="text" name="title" class="form-control" 
                                       placeholder="Ví dụ: Thông báo nghỉ lễ..." required>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Nội dung chi tiết</label>
                                <textarea name="content" class="form-control" 
                                          placeholder="Nhập nội dung tin tức..." required></textarea>
                            </div>
                            
                            <button name="add" class="btn-submit">
                                <i class="fa-solid fa-paper-plane"></i> Đăng Bài Viết
                            </button>
                        </form>
                    </div>
                </div>
                
                <!-- News List -->
                <div class="news-list-card">
                    <div class="card-top-row">
                        <h3 class="card-title">
                            <i class="fa-solid fa-list-ul"></i>
                            Danh Sách Tin Tức
                        </h3>
                    </div>
                    
                    <div>
                        <?php 
                        $res = mysqli_query($link, "SELECT * FROM news ORDER BY created_at DESC");
                        
                        if(mysqli_num_rows($res) > 0):
                            while($r = mysqli_fetch_assoc($res)): 
                        ?>
                            <div class="news-item">
                                <div class="news-content-wrapper">
                                    <h4 class="news-title">
                                        <?php echo htmlspecialchars($r['title']); ?>
                                    </h4>
                                    <div class="news-meta">
                                        <i class="fa-regular fa-clock"></i>
                                        <?php echo date('d/m/Y H:i', strtotime($r['created_at'])); ?>
                                    </div>
                                    <p class="news-text">
                                        <?php echo nl2br(htmlspecialchars($r['content'])); ?>
                                    </p>
                                </div>
                                <div class="news-actions">
                                    <a href="?del=<?php echo $r['id']; ?>" 
                                       onclick="return confirm('Xóa tin này?')" 
                                       class="action-btn delete-btn" 
                                       title="Xóa">
                                        <i class="fa-solid fa-trash"></i>
                                    </a>
                                </div>
                            </div>
                        <?php 
                            endwhile;
                        else: 
                        ?>
                            <div class="empty-state" style="padding: 60px 20px;">
                                <i class="fa-solid fa-newspaper" style="font-size: 48px; color: #CBD5E1; margin-bottom: 12px; display: block;"></i>
                                <p style="color: #94A3B8; margin: 0;">Chưa có tin tức nào</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>