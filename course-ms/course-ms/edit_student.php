<?php
include "connection.php";
session_start();
if(!isset($_SESSION['username'])){ header('location:login.php'); }

$id = $_GET["id"]; 
$student_id_code = ""; $full_name = ""; $email = ""; $class_name = "";

// Lấy thông tin cũ
$res = mysqli_query($link,"select * from students where id=$id");
while ($row = mysqli_fetch_array($res)) {
    $student_id_code = $row["student_id_code"];
    $full_name = $row["full_name"];
    $email = $row["email"];
    $class_name = $row["class_name"];
}

// Xử lý Cập nhật (Update)
if(isset($_POST["update"]))
{
    mysqli_query($link,"UPDATE students SET 
                        student_id_code='$_POST[student_id_code]', 
                        full_name='$_POST[full_name]', 
                        email='$_POST[email]', 
                        class_name='$_POST[class_name]' 
                        WHERE id=$id")
    or die(mysqli_error($link));

    header("Location: manage_students.php"); // Cập nhật xong, về trang quản lý
    exit;
}
?>

<html lang="en">
<head>
    <title>Edit Student</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <style> body { background-color: #FFF8E1; padding-top: 50px; } </style>
</head>
<body>
<div class="container">
    <div class="col-lg-4 col-lg-offset-4" style="background-color: #fff; padding: 20px; border-radius: 8px;">
        <h2>Cập nhật Thông tin Học sinh</h2>
        <form action="" name="form_edit_student" method="post">
             <div class="form-group">
                <label>Mã số học sinh:</label>
                <input type="text" class="form-control" name="student_id_code" value="<?php echo $student_id_code; ?>">
            </div>
            <div class="form-group">
                <label>Họ và tên:</label>
                <input type="text" class="form-control" name="full_name" value="<?php echo $full_name; ?>">
            </div>
            <div class="form-group">
                <label>Email:</label>
                <input type="email" class="form-control" name="email" value="<?php echo $email; ?>">
            </div>
            <div class="form-group">
                <label>Lớp:</label>
                <input type="text" class="form-control" name="class_name" value="<?php echo $class_name; ?>">
            </div>
            <button type="submit" name="update" class="btn btn-primary">Cập nhật</button>
            <a href="manage_students.php" class="btn btn-default">Hủy</a>
        </form>
    </div>
</div>
</body>
</html>