<?php
session_start();
include "connection.php";
include "auth.php";

$email = mysqli_real_escape_string($link, $_POST['email']);
$pass = md5($_POST['password']); 

$s = "SELECT * FROM teachers WHERE email='$email' AND password='$pass'";
$res = mysqli_query($link, $s);

if(mysqli_num_rows($res) == 1){
    $user = mysqli_fetch_assoc($res);
    $_SESSION['username'] = $user['full_name'];
    $_SESSION['teacher_id'] = $user['id'];
    $_SESSION['role_id'] = $user['role_id'];
    
    // Lấy quyền từ DB (giả sử bạn đã chạy SQL tạo bảng roles ở bước trước)
    // Nếu chưa có bảng role_permissions, ta fix cứng quyền cho Teacher để test
    $_SESSION['permissions'] = ($user['role_id'] == 1) ? ['delete_data'] : ['manage_exams', 'manage_students'];

    // XỬ LÝ COOKIE
    if(isset($_POST['remember'])) {
        setcookie('user_email', $email, time() + (86400 * 30), "/"); // 30 ngày
    } else {
        if(isset($_COOKIE['user_email'])) setcookie('user_email', "", time() - 3600, "/");
    }
    
    header('location:home.php'); 
}else{
    header('location:login.php?error=1'); 
}
?>