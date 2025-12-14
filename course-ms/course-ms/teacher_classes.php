<?php
include "connection.php";
include "auth.php";
requireRole(['teacher']);

$tid = intval($_SESSION['teacher_id']);
$q = "SELECT c.*, 
            (SELECT COUNT(*) FROM students s WHERE s.class_id=c.id) as student_count,
            (SELECT COUNT(*) FROM exams e WHERE e.class_id=c.id) as exam_count
      FROM classes c WHERE c.teacher_id=$tid ORDER BY c.id DESC";
$rs = mysqli_query($link, $q);
?>
<!DOCTYPE html>
<html lang="en">
<link rel="stylesheet" href="https://site-assets.fontawesome.com/releases/v6.4.2/css/all.css">
<head>
    <title>Lớp Học | Teacher</title>
    <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"> -->
    <link rel="stylesheet" href="dashboard_style.css">
</head>
<body>
<?php include "includes/sidebar.php"; ?>
<div class="main-wrapper">
    <?php include "includes/topbar.php"; ?>
    <div class="content-scroll">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Lớp Học Phụ Trách</h3>
            </div>
            <table class="dataTable">
                <thead><tr><th>Tên lớp</th><th>Học sinh</th><th>Bài kiểm tra</th><th width="150">Thao tác</th></tr></thead>
                <tbody>
                    <?php if(mysqli_num_rows($rs)==0): ?>
                        <tr><td colspan="4" style="padding:18px; text-align:center; color:#94A3B8;">Bạn chưa được gán lớp nào.</td></tr>
                    <?php else: while($c=mysqli_fetch_assoc($rs)): ?>
                        <tr>
                            <td style="font-weight:700; color:#0F172A;"><?php echo $c['name']; ?></td>
                            <td style="color:#64748B;"><?php echo $c['student_count']; ?> HS</td>
                            <td style="color:#64748B;"><?php echo $c['exam_count']; ?> bài</td>
                            <td>
                                <a href="teacher_home.php?class_id=<?php echo $c['id']; ?>" class="btn-secondary" style="padding:6px 10px; font-size:12px;">Xem lớp</a>
                                <a href="manage_exams.php?cid=<?php echo $c['id']; ?>" class="btn-primary" style="padding:6px 10px; font-size:12px;">Bài kiểm tra</a>
                            </td>
                        </tr>
                    <?php endwhile; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>
