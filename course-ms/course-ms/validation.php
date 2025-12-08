<?php
session_start();
include "connection.php"; 
include "auth.php"; // Gọi file auth mới tạo

$email = mysqli_real_escape_string($link, $_POST['email']);
$pass = md5($_POST['password']); 

// Lấy thông tin User
$s = "SELECT * FROM teachers WHERE email='$email' AND password='$pass'";
$result = mysqli_query($link, $s);

if(mysqli_num_rows($result) == 1){
    $user_data = mysqli_fetch_assoc($result);

    // --- 1. LƯU SESSION CƠ BẢN ---
    $_SESSION['username'] = $user_data['full_name'];
    $_SESSION['email'] = $user_data['email'];
    $_SESSION['teacher_id'] = $user_data['id'];
    $_SESSION['role_id'] = $user_data['role_id']; // Lưu Role ID (1=Admin, 2=Teacher)

    // --- 2. LƯU QUYỀN HẠN VÀO SESSION ---
    // Gọi hàm từ auth.php để lấy mảng quyền (ví dụ: ['manage_students', 'manage_exams'])
    $_SESSION['permissions'] = getUserPermissions($link, $user_data['id']);

    // --- 3. XỬ LÝ COOKIE (REMEMBER ME) ---
    if(isset($_POST['remember'])) {
        // Đặt cookie tồn tại trong 30 ngày
        setcookie('user_email', $email, time() + (86400 * 30), "/");
    } else {
        // Nếu bỏ tích, xóa cookie cũ (nếu có)
        if(isset($_COOKIE['user_email'])) {
            setcookie('user_email', "", time() - 3600, "/");
        }
    }
    
    header('location:home.php'); 
}else{
    header('location:login.php?error=1'); 
}
?>