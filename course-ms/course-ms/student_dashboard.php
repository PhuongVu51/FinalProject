<?php
include "connection.php"; include "auth.php"; requireRole(['student']);
$sid = $_SESSION['student_id'];
?>
<!DOCTYPE html>
<html>
<link rel="stylesheet" href="https://site-assets.fontawesome.com/releases/v6.4.2/css/all.css">
<head>
    <title>Kết Quả Học Tập</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="dashboard_style.css">
</head>
<body>
<?php include "includes/sidebar.php"; ?>
<div class="main-wrapper">
    <div class="topbar"><h2 class="page-title">Kết Quả Học Tập</h2></div>
    <div class="content-scroll">
        
        <div class="hero-box">
            <i class="fa-solid fa-trophy hero-bg-icon"></i>
            <h1>Bảng Thành Tích</h1>
            <p>Cố gắng hết sức mình nhé, <?php echo $_SESSION['full_name']; ?>!</p>
        </div>

        <div class="card">
            <table class="pretty-table">
                <thead><tr><th>Môn Học</th><th>Bài Kiểm Tra</th><th>Ngày Thi</th><th>Điểm Số</th></tr></thead>
                <tbody>
                <?php 
                $rs=mysqli_query($link, "SELECT sc.score, e.exam_title, e.subject, e.exam_date FROM scores sc JOIN exams e ON sc.exam_id=e.id WHERE sc.student_id=$sid ORDER BY e.exam_date DESC");
                while($r=mysqli_fetch_assoc($rs)): 
                    $scoreColor = ($r['score'] >= 8) ? '#10B981' : (($r['score'] >= 5) ? '#F59E0B' : '#EF4444');
                ?>
                    <tr>
                        <td style="font-weight:700"><?php echo $r['subject']; ?></td>
                        <td><?php echo $r['exam_title']; ?></td>
                        <td style="color:#64748B"><?php echo date('d/m/Y', strtotime($r['exam_date'])); ?></td>
                        <td>
                            <span style="font-size:18px; font-weight:900; color:<?php echo $scoreColor; ?>">
                                <?php echo $r['score']; ?>
                            </span>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>

    </div>
</div>
</body></html>