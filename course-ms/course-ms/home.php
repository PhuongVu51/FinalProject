<?php
include "connection.php";
include "auth.php";
requireRole(['admin', 'teacher']);

$role = $_SESSION['role'];
if($role === 'teacher') { header("Location: teacher_home.php"); exit; }

// Hàm đếm nhanh cho admin
function getCount($link, $sql){ $r = mysqli_fetch_assoc(mysqli_query($link, $sql)); return $r['c']; }

if($role === 'admin'){
    $s_count = getCount($link, "SELECT COUNT(*) as c FROM users WHERE role='student'");
    $t_count = getCount($link, "SELECT COUNT(*) as c FROM users WHERE role='teacher'");
    $c_count = getCount($link, "SELECT COUNT(*) as c FROM classes");
    $e_count = getCount($link, "SELECT COUNT(*) as c FROM exams");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Dashboard | Teacher Bee</title>
    <link rel="stylesheet" href="https://site-assets.fontawesome.com/releases/v6.4.2/css/all.css">
    <link rel="stylesheet" href="dashboard_style.css">
    <style>
        /* Modern Clean Overrides */
        :root { --primary-bg: #F8FAFC; --card-bg: #FFFFFF; --text-main: #1E293B; --text-muted: #64748B; }
        body { background-color: var(--primary-bg); font-family: 'Segoe UI', system-ui, sans-serif; }
        .main-wrapper { padding: 30px; }
        
        /* Stats Cards */
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 24px; margin-bottom: 30px; }
        .stat-item { background: var(--card-bg); border-radius: 16px; padding: 24px; display: flex; align-items: center; justify-content: space-between; border: 1px solid #E2E8F0; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); transition: all 0.2s ease; }
        .stat-item:hover { transform: translateY(-4px); box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); border-color: transparent; }
        .stat-info p { margin: 0 0 5px; color: var(--text-muted); font-size: 14px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; }
        .stat-info h3 { margin: 0; font-size: 28px; font-weight: 700; color: var(--text-main); }
        .stat-icon { width: 56px; height: 56px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 24px; }

        /* Modern Table Card */
        .card { background: var(--card-bg); border-radius: 16px; border: 1px solid #E2E8F0; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); padding: 0; overflow: hidden; margin-bottom: 30px; }
        .card-header { padding: 20px 24px; border-bottom: 1px solid #F1F5F9; display: flex; justify-content: space-between; align-items: center; background: white; }
        .card-title { font-size: 18px; font-weight: 700; color: var(--text-main); margin: 0; display: flex; align-items: center; gap: 10px; }
        
        /* Clean Table */
        .dataTable { width: 100%; border-collapse: collapse; }
        .dataTable thead th { background: #F8FAFC; color: var(--text-muted); font-weight: 600; text-transform: uppercase; font-size: 12px; letter-spacing: 0.05em; padding: 16px 24px; text-align: left; border-bottom: 1px solid #E2E8F0; }
        .dataTable tbody td { padding: 16px 24px; border-bottom: 1px solid #F1F5F9; color: var(--text-main); font-size: 14px; vertical-align: middle; }
        .dataTable tbody tr:last-child td { border-bottom: none; }
        .dataTable tbody tr:hover { background-color: #F8FAFC; }
    </style>
</head>
<body>

    <?php include "includes/sidebar.php"; ?>

    <div class="main-wrapper">
        <?php include "includes/topbar.php"; ?>

        <div class="content-scroll">
                
                <div class="stats-grid">
                    <div class="stat-item">
                        <div class="stat-info">
                            <p>Tổng Học Sinh</p>
                            <h3><?php echo $s_count; ?></h3>
                        </div>
                        <div class="stat-icon" style="background:#EFF6FF; color:#3B82F6;">
                            <i class="fa-solid fa-user-graduate"></i>
                        </div>
                    </div>
                    
                    <div class="stat-item">
                        <div class="stat-info">
                            <p>Giáo Viên</p>
                            <h3><?php echo $t_count; ?></h3>
                        </div>
                        <div class="stat-icon" style="background:#ECFDF5; color:#10B981;">
                            <i class="fa-solid fa-chalkboard-user"></i>
                        </div>
                    </div>

                    <div class="stat-item">
                        <div class="stat-info">
                            <p>Lớp Học</p>
                            <h3><?php echo $c_count; ?></h3>
                        </div>
                        <div class="stat-icon" style="background:#FFFBEB; color:#F59E0B;">
                            <i class="fa-solid fa-chalkboard"></i>
                        </div>
                    </div>

                    <div class="stat-item">
                        <div class="stat-info">
                            <p>Bài Thi</p>
                            <h3><?php echo $e_count; ?></h3>
                        </div>
                        <div class="stat-icon" style="background:#F3E8FF; color:#9333EA;">
                            <i class="fa-solid fa-file-pen"></i>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fa-solid fa-newspaper" style="color:#F59E0B;"></i> 
                            Tin Tức & Thông Báo
                        </h3>
                        <a href="manage_news.php" class="btn-secondary" style="border-radius:20px; font-size:13px; padding:6px 16px; border:1px solid #E2E8F0; text-decoration:none; color:#64748B;">
                            Xem tất cả
                        </a>
                    </div>
                    <table class="dataTable">
                        <thead>
                            <tr>
                                <th width="150">Ngày đăng</th>
                                <th width="250">Tiêu đề</th>
                                <th>Nội dung tóm tắt</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $res = mysqli_query($link, "SELECT * FROM news ORDER BY created_at DESC LIMIT 5");
                            if(mysqli_num_rows($res) > 0):
                                while($r = mysqli_fetch_assoc($res)): ?>
                                <tr>
                                    <td style="color:#64748B;">
                                        <div style="display:flex; align-items:center; gap:8px;">
                                            <i class="fa-regular fa-calendar" style="font-size:12px;"></i>
                                            <?php echo date('d/m/Y', strtotime($r['created_at'])); ?>
                                        </div>
                                    </td>
                                    <td style="font-weight:600; color:#0F172A;"><?php echo htmlspecialchars($r['title']); ?></td>
                                    <td style="color:#475569; line-height:1.5;"><?php echo htmlspecialchars(substr($r['content'], 0, 90)) . (strlen($r['content'])>90?'...':''); ?></td>
                                </tr>
                                <?php endwhile; 
                            else: ?>
                                <tr><td colspan="3" align="center" style="padding:30px; color:#94A3B8;">Chưa có tin tức nào.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <?php include "includes/footer.php"; ?>
        </div>
    </div>
</body>
</html>