<?php
session_start();
include "connection.php";

$full_name = mysqli_real_escape_string($link, $_POST['full_name']);
$email = mysqli_real_escape_string($link, $_POST['email']);
$pass = md5($_POST['password']);
// Các trường khác...

// Kiểm tra username tồn tại
$check = mysqli_query($link, "SELECT id FROM users WHERE username='$email'");
if(mysqli_num_rows($check) > 0){
    echo "<script>alert('Email đã tồn tại!'); window.location='register.php';</script>";
} else {
    // 1. Thêm vào bảng Users trước (Role 2 = Teacher)
    $sql_user = "INSERT INTO users (username, password, role_id, full_name) VALUES ('$email', '$pass', 2, '$full_name')";
    
    if (mysqli_query($link, $sql_user)) {
        $user_id = mysqli_insert_id($link); // Lấy ID vừa tạo
        
        // 2. Thêm vào bảng Teachers (Liên kết với User ID)
        $sql_teacher = "INSERT INTO teachers (full_name, email, password, role_id, user_id) 
                        VALUES ('$full_name', '$email', '$pass', 2, $user_id)";
        mysqli_query($link, $sql_teacher);
        
        echo "<script>alert('Đăng ký thành công!'); window.location='login.php';</script>";
    } else {
        echo "Lỗi: " . mysqli_error($link);
    }
}
?>