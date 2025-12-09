<?php
// THÊM 3 DÒNG NÀY ĐỂ BÁO LỖI (Cần thiết trong quá trình phát triển)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

// Sử dụng kết nối cục bộ (Laragon)
include 'connection.php'; // tạo biến $link
$con = $link; // dùng chung biến kết nối cho rõ ràng

/* Lấy dữ liệu */
$email = $_POST['email'];
$pass = md5($_POST['password']);

/* Kiểm tra 'email' và 'password' trong bảng 'teachers' */
$s = "select * from teachers where email='$email' AND password='$pass'";

$result = mysqli_query($con, $s);

// Kiểm tra lỗi truy vấn SQL (Ví dụ: tên bảng sai)
if (!$result) {
    die("LỖI TRUY VẤN SQL: " . mysqli_error($con));
}

$num = mysqli_num_rows($result);

if($num == 1){
    $user_data = mysqli_fetch_assoc($result);

    /* Lưu thông tin vào session */
    $_SESSION['username'] = $user_data['full_name'];
    $_SESSION['email'] = $user_data['email'];
    $_SESSION['teacher_id'] = $user_data['id'];

    // Chuyển hướng đến trang Home
    header('location:home.php');
} else {
    // Sai thông tin, quay lại trang login
    header('location:login.php?error=1');
}
?>