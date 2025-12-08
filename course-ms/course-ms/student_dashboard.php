<?php
include "connection.php";
session_start();
if(!isset($_SESSION['student_id'])){ header('location:student_login.php'); }

$student_id = $_SESSION['student_id'];
// Lấy danh sách điểm thi của học sinh này
$sql = "SELECT e.exam_title, e.subject, e.exam_date, sc.score, sc.comments 
        FROM scores sc 
        JOIN exams e ON sc.exam_id = e.id 
        WHERE sc.student_id = $student_id";
$scores = mysqli_query($link, $sql);
?>
<html lang="en">
<head>
    <title>Student Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="dashboard_style.css">
</head>
<body>
    <nav class="header-nav"><a href="#" class="logo">Xin chào, <?php echo $_SESSION['student_name']; ?> 🎓</a><a href="logout.php" class="logout-btn">Logout</a></nav>
    <div class="main-container"><div class="content-box">
        <h1 class="page-title">Kết quả học tập của bạn</h1>
        <table class="table table-bordered table-striped">
            <thead><tr><th>Môn học</th><th>Bài thi</th><th>Ngày thi</th><th>Điểm số</th><th>Lời phê</th></tr></thead>
            <tbody>
            <?php while($row = mysqli_fetch_assoc($scores)): ?>
                <tr>
                    <td><?php echo $row['subject']; ?></td>
                    <td><?php echo $row['exam_title']; ?></td>
                    <td><?php echo $row['exam_date']; ?></td>
                    <td style="font-weight:bold; color: #E65100;"><?php echo $row['score']; ?></td>
                    <td><?php echo $row['comments']; ?></td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div></div>
</body>
</html>