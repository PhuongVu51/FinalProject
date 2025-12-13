<?php
include "connection.php"; include "auth.php"; requireRole(['admin', 'teacher']);

function getC($link, $sql){ $r=mysqli_fetch_assoc(mysqli_query($link, $sql)); return $r['c']; }
$s_count = getC($link, "SELECT COUNT(*) as c FROM users WHERE role='student'");
$t_count = getC($link, "SELECT COUNT(*) as c FROM users WHERE role='teacher'");
$c_count = getC($link, "SELECT COUNT(*) as c FROM classes");
$e_count = getC($link, "SELECT COUNT(*) as c FROM exams");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Dashboard | TeacherBee</title>
    <link rel="stylesheet" href="https://site-assets.fontawesome.com/releases/v6.4.2/css/all.css">
    <link rel="stylesheet" href="dashboard_style.css">
</head>
<body>
<?php include "includes/sidebar.php"; ?>
<div class="main-wrapper">
    <div class="topbar">
        <h2 class="page-title">Overview</h2>
        <div class="user-profile">
            <span style="font-weight:700"><?php echo $_SESSION['full_name']; ?></span>
            <div class="user-avatar"><?php echo substr($_SESSION['full_name'],0,1); ?></div>
        </div>
    </div>

    <div class="content-scroll">
        <div class="hero-card">
            <div class="hero-text">
                <h1>Hello, <?php echo $_SESSION['full_name']; ?>! ☀️</h1>
                <p>Welcome back to TeacherBee. You have <b><?php echo $s_count; ?></b> students and <b><?php echo $c_count; ?></b> active classes today.</p>
                <a href="manage_classes.php" class="btn-primary">Manage Classes <i class="fa-solid fa-arrow-right"></i></a>
            </div>
            <img src="hero-illustration.png" alt="Dashboard" class="hero-img" onerror="this.style.display='none'">
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon" style="background:#E0F2FE; color:#0284C7"><i class="fa-solid fa-user-graduate"></i></div>
                <div class="stat-info"><h3><?php echo $s_count; ?></h3><p>Students</p></div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background:#DCFCE7; color:#16A34A"><i class="fa-solid fa-chalkboard-user"></i></div>
                <div class="stat-info"><h3><?php echo $t_count; ?></h3><p>Teachers</p></div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background:#FEF3C7; color:#D97706"><i class="fa-solid fa-book-open"></i></div>
                <div class="stat-info"><h3><?php echo $c_count; ?></h3><p>Classes</p></div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background:#F3E8FF; color:#9333EA"><i class="fa-solid fa-file-contract"></i></div>
                <div class="stat-info"><h3><?php echo $e_count; ?></h3><p>Exams</p></div>
            </div>
        </div>

        <div class="section-header">
            <h3>📢 Latest News</h3>
            <a href="manage_news.php" class="btn-secondary">View All</a>
        </div>
        
        <div class="card-white">
            <table class="table-custom">
                <thead><tr><th>Date</th><th>Title</th><th>Preview</th></tr></thead>
                <tbody>
                <?php $res=mysqli_query($link, "SELECT * FROM news ORDER BY created_at DESC LIMIT 4");
                while($r=mysqli_fetch_assoc($res)): ?>
                    <tr>
                        <td style="color:#9CA3AF"><?php echo date('M d, Y', strtotime($r['created_at'])); ?></td>
                        <td style="color:#F59E0B"><?php echo $r['title']; ?></td>
                        <td style="font-weight:400; color:#6B7280"><?php echo substr($r['content'],0,60); ?>...</td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>

    </div>
</div>
</body></html>