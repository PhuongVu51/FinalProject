<?php
include "connection.php"; 
session_start();
if(!isset($_SESSION['username'])){ header('location:login.php'); }

// Lấy danh sách lớp
$classes = mysqli_query($link, "SELECT * FROM classes");

if(isset($_POST["insert"])) {
    $title = $_POST['exam_title'];
    $subject = $_POST['subject'];
    $date = $_POST['exam_date'];
    $class_id = $_POST['class_id']; // Lấy ID lớp

    mysqli_query($link,"INSERT INTO exams (exam_title, subject, exam_date, class_id) 
                        VALUES ('$title', '$subject', '$date', '$class_id')");
    header("Location: manage_exams.php");
    exit;
}
?>
<html lang="en">
<head>
    <title>Manage Exams</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="dashboard_style.css">
</head>
<body>
    <nav class="header-nav"><a href="home.php" class="logo">Teacher Bee 🐝</a><a href="logout.php" class="logout-btn">Logout</a></nav>
    <div class="main-container"><div class="content-box">
        <a href="home.php" class="btn btn-info">⬅️ Back</a>
        <h1 class="page-title">Manage Exams</h1>
        <div class="crud-container">
            <div class="form-container">
                <h2>Create Exam</h2>
                <form method="post">
                    <label>Title:</label> <input type="text" name="exam_title" class="form-control" required>
                    <label>Subject:</label> <input type="text" name="subject" class="form-control" required>
                    <label>Date:</label> <input type="date" name="exam_date" class="form-control">
                    <label>Class:</label>
                    <select name="class_id" class="form-control" required>
                        <?php while($c = mysqli_fetch_assoc($classes)): ?>
                            <option value="<?php echo $c['id']; ?>"><?php echo $c['name']; ?></option>
                        <?php endwhile; ?>
                    </select>
                    <br>
                    <button type="submit" name="insert" class="btn btn-primary">Create</button>
                </form>
            </div>
            <div class="table-container">
                <h2>Exam List</h2>
                <table class="table table-bordered">
                    <thead><tr><th>ID</th><th>Title</th><th>Class</th><th>Actions</th></tr></thead>
                    <tbody>
                    <?php
                    $res=mysqli_query($link,"SELECT e.*, c.name as class_name FROM exams e LEFT JOIN classes c ON e.class_id = c.id");
                    while($row=mysqli_fetch_array($res)) {
                        echo "<tr><td>{$row['id']}</td><td>{$row['exam_title']}</td><td>{$row['class_name']}</td>";
                        // Thêm nút Nhập điểm
                        echo "<td>
                            <a href='enter_scores.php?exam_id={$row['id']}' class='btn btn-warning btn-sm'>📊 Nhập điểm</a>
                            <a href='delete_exam.php?id={$row['id']}' class='btn btn-danger btn-sm'>Xóa</a>
                        </td></tr>";
                    }
                    ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div></div>
</body>
</html>