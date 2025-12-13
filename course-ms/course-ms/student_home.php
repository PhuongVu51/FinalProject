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
    <style>
        .search-bar {
            background: white;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 24px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
        .search-bar input {
            width: 100%;
            padding: 12px 20px;
            border: 2px solid #E0E0E0;
            border-radius: 8px;
            font-size: 15px;
        }
        .search-bar input:focus {
            outline: none;
            border-color: #FFC107;
        }
        .quick-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 24px;
        }
        .stat-card {
            background: white;
            padding: 24px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            text-align: center;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
            text-decoration: none;
            color: inherit;
            display: block;
        }
        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            font-size: 28px;
        }
        .stat-icon.news { background: #E3F2FD; color: #1976D2; }
        .stat-icon.classes { background: #FFF3E0; color: #F57C00; }
        .stat-icon.scores { background: #E8F5E9; color: #388E3C; }
        .stat-number {
            font-size: 32px;
            font-weight: 700;
            color: #333;
            margin-bottom: 5px;
        }
        .stat-label {
            color: #666;
            font-size: 14px;
        }
        .news-list-item {
            background: white;
            border-radius: 8px;
            padding: 16px 20px;
            margin-bottom: 12px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.04);
            cursor: pointer;
            transition: all 0.3s;
            border-left: 3px solid #FFC107;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .news-list-item:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            transform: translateX(4px);
            border-left-color: #F57C00;
        }
        .news-list-left {
            display: flex;
            align-items: center;
            gap: 12px;
            flex: 1;
        }
        .news-list-icon {
            width: 40px;
            height: 40px;
            background: #FFF3E0;
            color: #F57C00;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            flex-shrink: 0;
        }
        .news-list-content {
            flex: 1;
        }
        .news-list-title {
            font-weight: 600;
            color: #333;
            margin-bottom: 4px;
            font-size: 15px;
        }
        .news-list-date {
            color: #64748B;
            font-size: 12px;
        }
        .news-list-arrow {
            color: #FFC107;
            font-size: 18px;
            flex-shrink: 0;
        }
        .view-all-btn {
            display: block;
            text-align: center;
            padding: 14px;
            background: linear-gradient(135deg, #FFC107 0%, #FFA000 100%);
            color: white;
            border-radius: 8px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s;
            margin-top: 16px;
            box-shadow: 0 2px 8px rgba(255, 193, 7, 0.3);
        }
        .view-all-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(255, 193, 7, 0.4);
        }
        .card-header-with-link {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .card-title {
            font-size: 18px;
            font-weight: 700;
            color: #333;
            margin: 0;
        }
        .header-link {
            color: #F57C00;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 5px;
            transition: color 0.3s;
        }
        .header-link:hover {
            color: #E65100;
        }
        .empty-news {
            text-align: center;
            padding: 40px 20px;
            color: #999;
        }
        .empty-news i {
            font-size: 48px;
            margin-bottom: 15px;
            opacity: 0.5;
        }
    </style>
</head>
<body>
    <?php include "includes/sidebar.php"; ?>
    <div class="main-wrapper">
        <?php include "includes/topbar.php"; ?>
        <div class="content-scroll">
            

            <!-- Search Bar -->
            <div class="search-bar">
                <input type="text" id="searchInput" placeholder="🔍 Tìm kiếm nhanh..." onkeyup="searchContent()">
            </div>
            
            <div class="hero-banner">
                <h1>Chào mừng, <?php echo $_SESSION['full_name']; ?>! 👋</h1>
                <p>Chúc bạn một ngày học tập thật hiệu quả và tràn đầy năng lượng.</p>
            </div>

            <!-- Quick Stats -->
            <div class="quick-stats">
                <a href="student_news.php" class="stat-card">
                    <div class="stat-icon news"><i class="fa-solid fa-newspaper"></i></div>
                    <div class="stat-number"><?php echo mysqli_num_rows(mysqli_query($link, "SELECT * FROM news")); ?></div>
                    <div class="stat-label">Tin Tức</div>
                </a>
                <a href="student_classes.php" class="stat-card">
                    <div class="stat-icon classes"><i class="fa-solid fa-chalkboard"></i></div>
                    <div class="stat-number"><?php echo mysqli_num_rows(mysqli_query($link, "SELECT * FROM classes")); ?></div>
                    <div class="stat-label">Lớp Học</div>
                </a>
                <a href="student_scores.php" class="stat-card">
                    <div class="stat-icon scores"><i class="fa-solid fa-chart-line"></i></div>
                    <div class="stat-number"><?php echo mysqli_num_rows(mysqli_query($link, "SELECT * FROM scores WHERE student_id=$sid")); ?></div>
                    <div class="stat-label">Kết Quả</div>
                </a>
            </div>

            <div style="display:grid; grid-template-columns: 2fr 1fr; gap:24px;">
                <!-- News Section -->
                <div class="card">
                    <div class="card-header-with-link">
                        <h3 class="card-title">📢 Tin Tức Nhà Trường</h3>
                        <a href="student_news.php" class="header-link">
                            Xem tất cả <i class="fa-solid fa-arrow-right"></i>
                        </a>
                    </div>
                    
                    <?php 
                    $res = mysqli_query($link, "SELECT * FROM news ORDER BY created_at DESC LIMIT 5");
                    if(mysqli_num_rows($res) > 0):
                        while($r = mysqli_fetch_assoc($res)): ?>
                        <div class="news-list-item news-item" onclick="window.location.href='student_news.php'">
                            <div class="news-list-left">
                                <div class="news-list-icon">
                                    <i class="fa-solid fa-bullhorn"></i>
                                </div>
                                <div class="news-list-content">
                                    <div class="news-list-title">
                                        <?php echo $r['title']; ?>
                                    </div>
                                    <div class="news-list-date">
                                        <i class="fa-regular fa-clock"></i>
                                        <?php echo date('d/m/Y H:i', strtotime($r['created_at'])); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="news-list-arrow">
                                <i class="fa-solid fa-chevron-right"></i>
                            </div>
                        </div>
                    <?php endwhile; ?>
                    
                    <a href="student_news.php" class="view-all-btn">
                        <i class="fa-solid fa-newspaper"></i> Xem Tất Cả Tin Tức
                    </a>
                    
                    <?php else: ?>
                        <div class="empty-news">
                            <i class="fa-regular fa-newspaper"></i>
                            <p>Chưa có tin tức nào</p>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Quick Links -->
                <div>
                    <div class="card" style="text-align:center; margin-bottom:20px;">
                        <div style="width:60px; height:60px; background:#FFF3E0; color:#F57C00; border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 15px; font-size:24px;">
                            <i class="fa-solid fa-chalkboard-user"></i>
                        </div>
                        <h3 style="margin:0 0 10px 0; font-size:16px;">Lớp Học</h3>
                        <p style="color:#64748B; font-size:13px; margin-bottom:15px;">Xem và đăng ký các lớp học.</p>
                        <a href="student_classes.php" class="btn-primary" style="width:100%; display:inline-block; text-decoration:none;">Xem Lớp Học</a>
                    </div>

                    <div class="card" style="text-align:center;">
                        <div style="width:60px; height:60px; background:#ECFDF5; color:#10B981; border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 15px; font-size:24px;">
                            <i class="fa-solid fa-chart-pie"></i>
                        </div>
                        <h3 style="margin:0 0 10px 0; font-size:16px;">Kết Quả Học Tập</h3>
                        <p style="color:#64748B; font-size:13px; margin-bottom:15px;">Xem điểm số các bài kiểm tra.</p>
                        <a href="student_scores.php" class="btn-primary" style="width:100%; display:inline-block; text-decoration:none;">Xem Điểm</a>
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