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
        .hero-banner-large {
            background: linear-gradient(135deg, #FFA000 0%, #FF8F00 100%);
            padding: 24px 32px;
            border-radius: 16px;
            margin-bottom: 40px;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 8px 24px rgba(255, 160, 0, 0.3);
        }
        .hero-content h1 {
            font-size: 28px;
            font-weight: 700;
            margin: 0 0 12px 0;
            line-height: 1.3;
        }
        .hero-content p {
            font-size: 16px;
            margin: 0;
            opacity: 0.95;
            line-height: 1.5;
        }
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
        }
        .section-title {
            font-size: 22px;
            font-weight: 700;
            color: #333;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .section-title i {
            color: #FFA000;
        }
        .see-all-link {
            color: #FFA000;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        .see-all-link:hover {
            color: #FF8F00;
        }
        .classes-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 24px;
            margin-bottom: 50px;
        }
        .class-card {
            background: white;
            border-radius: 16px;
            padding: 24px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.08);
            transition: all 0.3s;
            position: relative;
        }
        .class-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 24px rgba(0,0,0,0.12);
        }
        .class-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin-bottom: 16px;
        }
        .class-name {
            font-size: 20px;
            font-weight: 700;
            color: #333;
            margin-bottom: 8px;
        }
        .class-teacher {
            color: #666;
            font-size: 14px;
            margin-bottom: 16px;
        }
        .class-action-btn {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            text-align: center;
            text-decoration: none;
            display: block;
        }
        .btn-register {
            background: #FFF3E0;
            color: #F57C00;
            border: 2px solid #FFE0B2;
        }
        .btn-register:hover {
            background: #FFE0B2;
        }
        .discover-section {
            margin-top: 50px;
        }
        .discover-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 24px;
        }
        .discover-card {
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 2px 12px rgba(0,0,0,0.08);
            transition: all 0.3s;
        }
        .discover-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 24px rgba(0,0,0,0.12);
        }
        .discover-image {
            width: 100%;
            height: 180px;
            object-fit: cover;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 48px;
        }
        .discover-content {
            padding: 20px;
        }
        .discover-title {
            font-size: 18px;
            font-weight: 700;
            color: #333;
            margin-bottom: 8px;
        }
        .discover-desc {
            color: #666;
            font-size: 13px;
            line-height: 1.5;
            margin-bottom: 16px;
        }
        .slot-badge {
            position: absolute;
            top: 15px;
            left: 15px;
            background: rgba(0,0,0,0.7);
            color: white;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <?php include "includes/sidebar.php"; ?>
    <div class="main-wrapper">
        <?php include "includes/student_topbar.php"; ?>
        <div class="content-scroll">
            
            <!-- Hero Banner -->
            <div class="hero-banner-large">
                <div class="hero-content">
                    <h1>Chào mừng, <?php echo $_SESSION['full_name']; ?>! 👋</h1>
                    <p>Chúc bạn một ngày học tập thật hiệu quả và tràn đầy năng lượng.</p>
                </div>
            </div>

            <!-- Quick Stats Grid -->
            <div class="classes-grid" style="margin-bottom: 40px;">
                <a href="student_news.php" class="class-card" style="text-decoration: none; cursor: pointer;">
                    <div class="class-icon" style="background: #E3F2FD; color: #1976D2;">
                        <i class="fa-solid fa-newspaper"></i>
                    </div>
                    <div class="class-name"><?php echo mysqli_num_rows(mysqli_query($link, "SELECT * FROM news")); ?></div>
                    <div class="class-teacher">Tin Tức</div>
                </a>

                <a href="student_classes.php" class="class-card" style="text-decoration: none; cursor: pointer;">
                    <div class="class-icon" style="background: #FFF3E0; color: #F57C00;">
                        <i class="fa-solid fa-chalkboard-user"></i>
                    </div>
                    <div class="class-name"><?php echo mysqli_num_rows(mysqli_query($link, "SELECT * FROM student_classes WHERE student_id=$sid")); ?></div>
                    <div class="class-teacher">Lớp Học Của Tôi</div>
                </a>

                <a href="student_scores.php" class="class-card" style="text-decoration: none; cursor: pointer;">
                    <div class="class-icon" style="background: #FFF9C4; color: #F9A825;">
                        <i class="fa-solid fa-star"></i>
                    </div>
                    <div class="class-name"><?php echo mysqli_num_rows(mysqli_query($link, "SELECT * FROM scores WHERE student_id=$sid")); ?></div>
                    <div class="class-teacher">Kết Quả</div>
                </a>
            </div>

            <!-- News Section -->
            <div class="section-header">
                <div class="section-title">
                    <i class="fa-solid fa-bullhorn"></i>
                    Tin Tức Nhà Trường
                </div>
                <a href="student_news.php" class="see-all-link">
                    Xem tất cả <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>

            <div class="card" style="margin-bottom: 50px;">
                <?php 
                $news_query = "SELECT * FROM news ORDER BY created_at DESC LIMIT 5";
                $news_rs = mysqli_query($link, $news_query);
                
                if(mysqli_num_rows($news_rs) > 0):
                    while($news = mysqli_fetch_assoc($news_rs)):
                ?>
                    <div class="news-list-item" onclick="window.location.href='student_news.php'" 
                         style="background: white; border-radius: 8px; padding: 16px 20px; margin-bottom: 12px; 
                                box-shadow: 0 2px 6px rgba(0,0,0,0.04); cursor: pointer; transition: all 0.3s; 
                                border-left: 3px solid #FFC107; display: flex; align-items: center; justify-content: space-between;">
                        <div style="display: flex; align-items: center; gap: 12px; flex: 1;">
                            <div style="width: 40px; height: 40px; background: #FFF3E0; color: #F57C00; border-radius: 50%; 
                                        display: flex; align-items: center; justify-content: center; font-size: 18px; flex-shrink: 0;">
                                <i class="fa-solid fa-bullhorn"></i>
                            </div>
                            <div style="flex: 1;">
                                <div style="font-weight: 600; color: #333; margin-bottom: 4px; font-size: 15px;">
                                    <?php echo $news['title']; ?>
                                </div>
                                <div style="color: #64748B; font-size: 12px;">
                                    <i class="fa-regular fa-clock"></i>
                                    <?php echo date('d/m/Y H:i', strtotime($news['created_at'])); ?>
                                </div>
                            </div>
                        </div>
                        <div style="color: #FFC107; font-size: 18px; flex-shrink: 0;">
                            <i class="fa-solid fa-chevron-right"></i>
                        </div>
                    </div>
                <?php 
                    endwhile;
                else:
                ?>
                    <div style="text-align: center; padding: 40px 20px; color: #999;">
                        <i class="fa-regular fa-newspaper" style="font-size: 48px; margin-bottom: 15px; opacity: 0.5;"></i>
                        <p>Chưa có tin tức nào</p>
                    </div>
                <?php endif; ?>

                <a href="student_news.php" class="class-action-btn btn-register" 
                   style="margin-top: 16px; background: linear-gradient(135deg, #FFC107 0%, #FFA000 100%); 
                          color: white; border: none;">
                    <i class="fa-solid fa-newspaper"></i> Xem Tất Cả Tin Tức
                </a>
            </div>

            <!-- Discover New Classes -->
            <div class="discover-section">
                <div class="section-header">
                    <div class="section-title">
                        <i class="fa-solid fa-compass"></i>
                        Khám phá lớp mới
                    </div>
                    <a href="student_classes.php" class="see-all-link">
                        Xem tất cả <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </div>

                <div class="discover-grid">
                    <?php 
                    // FIXED: Get available classes (exclude enrolled via student_classes)
                    $available_query = "SELECT c.*, u.full_name as teacher_name,
                        (SELECT COUNT(*) FROM student_classes WHERE class_id=c.id) as student_count
                        FROM classes c 
                        LEFT JOIN teachers t ON c.teacher_id=t.id 
                        LEFT JOIN users u ON t.user_id=u.id
                        WHERE c.id NOT IN (SELECT class_id FROM student_classes WHERE student_id=$sid)
                        AND c.id NOT IN (SELECT class_id FROM applications WHERE student_id=$sid AND status='pending')
                        LIMIT 3";
                    $available_rs = mysqli_query($link, $available_query);
                    
                    $icons = ['fa-code', 'fa-user-graduate', 'fa-calculator'];
                    $gradients = [
                        'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
                        'linear-gradient(135deg, #f093fb 0%, #f5576c 100%)',
                        'linear-gradient(135deg, #4facfe 0%, #00f2fe 100%)'
                    ];
                    $index = 0;
                    
                    while($class = mysqli_fetch_assoc($available_rs)):
                    ?>
                        <div class="discover-card">
                            <div class="discover-image" style="background: <?php echo $gradients[$index % 3]; ?>; position: relative;">
                                <i class="fa-solid <?php echo $icons[$index % 3]; ?>"></i>
                            </div>
                            <div class="discover-content">
                                <div class="discover-title"><?php echo $class['name']; ?></div>
                                <div class="discover-desc">
                                    <?php echo $class['teacher_name'] ? 'Giảng viên: ' . $class['teacher_name'] : 'Đang cập nhật giảng viên'; ?>
                                </div>
                                <a href="student_classes.php?reg=<?php echo $class['id']; ?>" 
                                   class="class-action-btn btn-register"
                                   onclick="return confirm('Bạn có chắc muốn đăng ký lớp này?');">
                                    Đăng ký <i class="fa-solid fa-plus"></i>
                                </a>
                            </div>
                        </div>
                    <?php 
                        $index++;
                    endwhile; 
                    ?>
                </div>
            </div>

            <?php include "includes/footer.php"; ?>
        </div>
    </div>
</body>
</html>