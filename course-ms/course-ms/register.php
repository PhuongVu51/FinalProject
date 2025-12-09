<?php
session_start();
include "connection.php";

$msg = "";
if (isset($_POST['register'])) {
    $name = mysqli_real_escape_string($link, $_POST['full_name']);
    $email = mysqli_real_escape_string($link, $_POST['email']);
    $pass = md5($_POST['password']);
    
    $check = mysqli_query($link, "SELECT id FROM users WHERE username='$email'");
    if(mysqli_num_rows($check) > 0) {
        $msg = "Email đã tồn tại!";
    } else {
        // QUAN TRỌNG: role='teacher' (không dùng số)
        $sql = "INSERT INTO users (username, password, role, full_name) VALUES ('$email', '$pass', 'teacher', '$name')";
        if(mysqli_query($link, $sql)){
            $uid = mysqli_insert_id($link);
            mysqli_query($link, "INSERT INTO teachers (user_id, email) VALUES ($uid, '$email')");
            echo "<script>alert('Đăng ký thành công!'); window.location='login.php';</script>";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<link rel="stylesheet" href="https://site-assets.fontawesome.com/releases/v6.4.2/css/all.css">
<head><title>Register</title><link rel="stylesheet" href="style.css"></head>
<body>
<div class="login-container"><div class="login-form-box">
    <h3>📝 Đăng Ký GV</h3>
    <?php if($msg) echo "<p style='color:red'>$msg</p>"; ?>
    <form method="post">
        <div class="form-group"><label>Họ Tên</label><input type="text" name="full_name" class="form-control" required></div>
        <div class="form-group"><label>Email</label><input type="email" name="email" class="form-control" required></div>
        <div class="form-group"><label>Mật Khẩu</label><input type="password" name="password" class="form-control" required></div>
        <button type="submit" name="register" class="btn-submit">Đăng Ký</button>
    </form>
    <div style="text-align:center; margin-top:10px"><a href="login.php">Quay lại đăng nhập</a></div>
</div></div>
</body>
</html>