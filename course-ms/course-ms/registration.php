<?php
session_start();
$con=mysqli_connect('127.0.0.1','root');
mysqli_select_db($con, 'teacher_bee_db');

/* Lấy tất cả dữ liệu từ form mới */
$full_name = $_POST['full_name'];
$email = $_POST['email'];
$pass = md5($_POST['password']);
$dob = $_POST['dob'];
$gender = $_POST['gender'];
$subjects = $_POST['subjects'];


/* Kiểm tra email đã tồn tại chưa */
$s="select * from teachers where email='$email'";

$result = mysqli_query($con, $s);
$num = mysqli_num_rows($result);

if($num == 1){
    // Trả về trang đăng ký với thông báo lỗi
    echo "Email đã tồn tại. Vui lòng quay lại và sử dụng email khác.";
    header("refresh:3;url=register.php");
} else {
    /* Thêm giáo viên mới với đầy đủ thông tin */
    $reg ="INSERT INTO teachers (full_name, email, password, dob, gender, subjects) 
           VALUES ('$full_name', '$email', '$pass', '$dob', '$gender', '$subjects')";
    
    if (mysqli_query($con, $reg)) {
        echo "Đăng ký thành công! Đang chuyển đến trang đăng nhập...";
        // Chuyển hướng về trang login (Màn 1)
        header("refresh:2;url=login.php");
    } else {
        echo "Lỗi khi đăng ký: " . mysqli_error($con);
    }
}

mysqli_close($con);
?>