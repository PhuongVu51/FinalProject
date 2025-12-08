<?php
include "connection.php";
session_start();
if(!isset($_SESSION['username'])){ header('location:login.php'); }

$exam_id = $_GET['exam_id'];

// Lấy thông tin bài thi
$exam = mysqli_fetch_assoc(mysqli_query($link, "SELECT * FROM exams WHERE id=$exam_id"));
$class_id = $exam['class_id'];

// Xử lý lưu điểm
if(isset($_POST['save_scores'])) {
    foreach($_POST['scores'] as $student_id => $score) {
        $comment = $_POST['comments'][$student_id];
        // Insert hoặc Update nếu đã có điểm
        $sql = "INSERT INTO scores (exam_id, student_id, score, comments) 
                VALUES ($exam_id, $student_id, '$score', '$comment')
                ON DUPLICATE KEY UPDATE score='$score', comments='$comment'";
        mysqli_query($link, $sql);
    }
    $msg = "Đã lưu điểm thành công!";
}

// Lấy danh sách học sinh trong lớp của bài thi này, kèm theo điểm (nếu có)
$sql_students = "SELECT s.id, s.full_name, s.student_id_code, sc.score, sc.comments 
                 FROM students s 
                 LEFT JOIN scores sc ON s.id = sc.student_id AND sc.exam_id = $exam_id
                 WHERE s.class_id = $class_id";
$students = mysqli_query($link, $sql_students);
?>

<html lang="en">
<head>
    <title>Enter Scores</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="dashboard_style.css">
</head>
<body>
    <nav class="header-nav"><a href="home.php" class="logo">CourseMS🐝</a><a href="logout.php" class="logout-btn">Logout</a></nav>
    <div class="main-container"><div class="content-box">
        <a href="manage_exams.php" class="btn btn-info">⬅️ Back to Exams</a>
        <h1 class="page-title">Nhập điểm: <?php echo $exam['exam_title']; ?></h1>
        
        <?php if(isset($msg)) echo "<div class='alert alert-success'>$msg</div>"; ?>

        <form method="post">
            <table class="table table-bordered table-striped">
                <thead><tr><th>Mã HS</th><th>Tên Học sinh</th><th>Điểm số (0-10)</th><th>Nhận xét</th></tr></thead>
                <tbody>
                <?php while($st = mysqli_fetch_assoc($students)): ?>
                    <tr>
                        <td><?php echo $st['student_id_code']; ?></td>
                        <td><?php echo $st['full_name']; ?></td>
                        <td>
                            <input type="number" step="0.1" min="0" max="10" class="form-control" 
                                   name="scores[<?php echo $st['id']; ?>]" 
                                   value="<?php echo $st['score']; ?>" required>
                        </td>
                        <td>
                            <input type="text" class="form-control" 
                                   name="comments[<?php echo $st['id']; ?>]" 
                                   value="<?php echo $st['comments']; ?>">
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
            <button type="submit" name="save_scores" class="btn btn-primary btn-lg">Lưu Bảng Điểm</button>
        </form>
    </div></div>
</body>
</html>