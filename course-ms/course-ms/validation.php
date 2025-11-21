<?php
session_start();
$con=mysqli_connect('127.0.0.1','root');
mysqli_select_db($con, 'teacher_bee_db');

/* Lấy dữ liệu (sử dụng 'email' thay vì 'user') */
$email = $_POST['email']; 
$pass = md5($_POST['password']);

/* Kiểm tra 'email' và 'password' trong bảng 'teachers' */
$s="select * from teachers where email='$email' && password='$pass'";

$result = mysqli_query($con, $s);
$num = mysqli_num_rows($result);

if($num == 1){
    $user_data = mysqli_fetch_assoc($result);

    /* Lưu thông tin vào session */
    $_SESSION['username'] = $user_data['full_name']; // Chào theo tên đầy đủ
    $_SESSION['email'] = $user_data['email'];
    $_SESSION['teacher_id'] = $user_data['id'];
    
    // Chuyển hướng đến trang Home (Màn 3 - Dashboard)
    header('location:home.php'); 
}else{
    // Sai thông tin, quay lại trang login
    header('location:login.php?error=1'); // Thêm param lỗi (tùy chọn)
}
?>