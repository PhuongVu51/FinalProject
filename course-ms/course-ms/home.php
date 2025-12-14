<?php
include "connection.php";
include "auth.php";
requireRole(['admin', 'teacher']);

$role = $_SESSION['role'];
if($role === 'teacher') { header("Location: teacher_home.php"); exit; }

function getCount($link, $sql){ 
    $r = mysqli_fetch_assoc(mysqli_query($link, $sql)); 
    return $r['c']; 
}

if($role === 'admin'){
    $s_count = getCount($link, "SELECT COUNT(*) as c FROM users WHERE role='student'");
    $t_count = getCount($link, "SELECT COUNT(*) as c FROM users WHERE role='teacher'");
    $c_count = getCount($link, "SELECT COUNT(*) as c FROM classes");
    $e_count = getCount($link, "SELECT COUNT(*) as c FROM exams");
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <title>Hệ Thống Quản Lý | Teacher Bee</title>
    <link rel="stylesheet" href="https://site-assets.fontawesome.com/releases/v6.4.2/css/all.css">
    <link rel="stylesheet" href="dashboard_style.css">
    <style>
        body { background-color: #FFFDF7; }
        
        .dashboard-header {
            margin-bottom: 30px;
        }
        
        .dashboard-header h1 {
            font-size: 28px;
            font-weight: 700;
            color: #1E293B;
            margin: 0;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: white;
            border-radius: 16px;
            padding: 24px;
            border: 1px solid #E2E8F0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            transition: all 0.2s;
        }
        
        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        }
        
        .stat-content p {
            margin: 0 0 4px 0;
            color: #64748B;
            font-size: 13px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .stat-content h3 {
            margin: 0;
            font-size: 32px;
            font-weight: 700;
            color: #1E293B;
        }
        
        .stat-icon {
            width: 56px;
            height: 56px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }
        
        .stat-icon.students {
            background: #EFF6FF;
            color: #3B82F6;
        }
        
        .stat-icon.teachers {
            background: #ECFDF5;
            color: #10B981;
        }
        
        .stat-icon.classes {
            background: #FEF3C7;
            color: #F59E0B;
        }
        
        .stat-icon.exams {
            background: #F3E8FF;
            color: #A855F7;
        }
        
        .news-card {
            background: white;
            border-radius: 16px;
            border: 1px solid #E2E8F0;
            overflow: hidden;
        }
        
        .news-card-header {
            padding: 20px 24px;
            border-bottom: 1px solid #F1F5F9;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .news-card-header h3 {
            margin: 0;
            font-size: 18px;
            font-weight: 700;
            color: #1E293B;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .view-all-link {
            color: #64748B;
            text-decoration: none;
            font-size: 13px;
            font-weight: 600;
            transition: color 0.2s;
        }
        
        .view-all-link:hover {
            color: #F59E0B;
        }
        
        .news-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .news-table thead th {
            text-align: left;
            padding: 16px 24px;
            background: #FAFBFC;
            color: #94A3B8;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 1px solid #E2E8F0;
        }
        
        .news-table tbody td {
            padding: 20px 24px;
            border-bottom: 1px solid #F1F5F9;
            color: #334155;
            font-size: 14px;
        }
        
        .news-table tbody tr:last-child td {
            border-bottom: none;
        }
        
        .news-table tbody tr:hover {
            background-color: #FAFBFC;
        }
        
        .news-date {
            color: #64748B;
            font-size: 13px;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        
        .news-title {
            font-weight: 700;
            color: #1E293B;
        }
        
        .news-content {
            color: #64748B;
            font-size: 13px;
            line-height: 1.6;
        }
    </style>
</head>
<body>
    <?php include "includes/sidebar.php"; ?>
    
    <div class="main-wrapper">
        <?php include "includes/topbar.php"; ?>
        
        <div class="content-scroll">
            <div class="dashboard-header">
            </div>

            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-content">
                        <p>TỔNG HỌC SINH</p>
                        <h3><?php echo $s_count; ?></h3>
                    </div>
                    <div class="stat-icon students">
                        <i class="fa-solid fa-user-graduate"></i>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-content">
                        <p>GIÁO VIÊN</p>
                        <h3><?php echo $t_count; ?></h3>
                    </div>
                    <div class="stat-icon teachers">
                        <i class="fa-solid fa-chalkboard-user"></i>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-content">
                        <p>LỚP HỌC</p>
                        <h3><?php echo $c_count; ?></h3>
                    </div>
                    <div class="stat-icon classes">
                        <i class="fa-solid fa-chalkboard"></i>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-content">
                        <p>BÀI THI</p>
                        <h3><?php echo $e_count; ?></h3>
                    </div>
                    <div class="stat-icon exams">
                        <i class="fa-solid fa-file-pen"></i>
                    </div>
                </div>
            </div>

            <div class="news-card">
                <div class="news-card-header">
                    <h3>
                        <i class="fa-solid fa-newspaper" style="color: #F59E0B;"></i>
                        Tin Tức & Thông Báo
                    </h3>
                    <a href="manage_news.php" class="view-all-link">Xem tất cả</a>
                </div>
                
                <table class="news-table">
                    <thead>
                        <tr>
                            <th width="150">NGÀY ĐĂNG</th>
                            <th width="280">TIÊU ĐỀ</th>
                            <th>NỘI DUNG TÓM TẮT</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $res = mysqli_query($link, "SELECT * FROM news ORDER BY created_at DESC LIMIT 5");
                        if(mysqli_num_rows($res) > 0):
                            while($r = mysqli_fetch_assoc($res)): ?>
                            <tr>
                                <td>
                                    <div class="news-date">
                                        <i class="fa-regular fa-calendar"></i>
                                        <?php echo date('d/m/Y', strtotime($r['created_at'])); ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="news-title">
                                        <?php echo htmlspecialchars($r['title']); ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="news-content">
                                        <?php echo htmlspecialchars(substr($r['content'], 0, 100)) . (strlen($r['content'])>100?'...':''); ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; 
                        else: ?>
                            <tr>
                                <td colspan="3" style="text-align: center; padding: 40px; color: #94A3B8;">
                                    Chưa có tin tức nào.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>