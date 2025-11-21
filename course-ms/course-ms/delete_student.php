<?php
include "connection.php";
session_start();
if(!isset($_SESSION['username'])){ header('location:login.php'); }

$id = $_GET["id"]; 
$res = mysqli_query($link," SELECT * FROM students WHERE id=$id");
$item = mysqli_fetch_array($res);
$student_name = $item["full_name"];

// Xử lý Xóa (Delete)
if (isset($_GET['confirm']) && $_GET['confirm'] === 'yes') {
    mysqli_query($link, "DELETE FROM students WHERE id=$id") or die(mysqli_error($link));
    header("location:manage_students.php"); 
    exit;
}
?>

<html lang="en">
<head>
    <title>Confirm Delete</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
</head>
<body style="padding: 50px; background-color: #FFF8E1;">
    <div class="container text-center">
        <div class="alert alert-danger">
            <h1>Bạn có chắc chắn muốn xóa học sinh "<?php echo $student_name; ?>"?</h1>
        </div>
        <a href="delete_student.php?id=<?php echo $id ?>&confirm=yes" class="btn btn-danger btn-lg">Có, Xóa</a>
        <a href="manage_students.php" class="btn btn-default btn-lg">Không, Quay lại</a>
    </div>
</body>
</html>