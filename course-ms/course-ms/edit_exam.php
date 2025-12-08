<?php
include "connection.php";
session_start();
if(!isset($_SESSION['username'])){ header('location:login.php'); }

$id = $_GET["id"]; 
$exam_title = ""; $subject = ""; $exam_date = "";

// Lấy thông tin cũ
$res = mysqli_query($link,"select * from exams where id=$id");
while ($row = mysqli_fetch_array($res)) {
    $exam_title = $row["exam_title"];
    $subject = $row["subject"];
    $exam_date = $row["exam_date"];
}

// Xử lý Cập nhật (Update)
if(isset($_POST["update"]))
{
    mysqli_query($link,"UPDATE exams SET 
                        exam_title='$_POST[exam_title]', 
                        subject='$_POST[subject]', 
                        exam_date='$_POST[exam_date]'
                        WHERE id=$id")
    or die(mysqli_error($link));

    header("Location: manage_exams.php"); 
    exit;
}
?>

<html lang="en">
<head>
    <title>Edit Exam</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <style> body { background-color: #FFF8E1; padding-top: 50px; } </style>
</head>
<body>
<div class="container">
    <div class="col-lg-4 col-lg-offset-4" style="background-color: #fff; padding: 20px; border-radius: 8px;">
        <h2>Cập nhật Bài kiểm tra</h2>
        <form action="" name="form_edit_exam" method="post">
             <div class="form-group">
                <label>Tên bài kiểm tra:</label>
                <input type="text" class="form-control" name="exam_title" value="<?php echo $exam_title; ?>">
            </div>
            <div class="form-group">
                <label>Môn học:</label>
                <input type="text" class="form-control" name="subject" value="<?php echo $subject; ?>">
            </div>
            <div class="form-group">
                <label>Ngày thi:</label>
                <input type="date" class="form-control" name="exam_date" value="<?php echo $exam_date; ?>">
            </div>
            <button type="submit" name="update" class="btn btn-primary">Cập nhật</button>
            <a href="manage_exams.php" class="btn btn-default">Hủy</a>
        </form>
    </div>
</div>
</body>
</html>