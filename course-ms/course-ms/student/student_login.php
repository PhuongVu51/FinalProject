<?php
session_start();
include "../config/connection.php";

if(isset($_POST['login'])) {
    // Người dùng có thể nhập ID hoặc Email vào ô này
    $input = mysqli_real_escape_string($link, $_POST['login_input']);
    $pass = md5($_POST['password']);
    
    // Kiểm tra: (Mã SV == input HOẶC Email == input) VÀ Mật khẩu đúng
    $sql = "SELECT * FROM students WHERE (student_id_code='$input' OR email='$input') AND password='$pass'";
    $res = mysqli_query($link, $sql);
    
    if(mysqli_num_rows($res) == 1) {
        $st = mysqli_fetch_assoc($res);
        $_SESSION['student_name'] = $st['full_name'];
        $_SESSION['student_id'] = $st['id'];
        header("Location: student_dashboard.php");
        exit;
    } else {
        $error = "Sai thông tin đăng nhập hoặc mật khẩu!";
    }
}
?>
<html lang="en">
<link rel="stylesheet" href="https://site-assets.fontawesome.com/releases/v6.4.2/css/all.css">
<head>
    <title>Student Login</title>
    <link rel="stylesheet" href="../shared/css/style.css">
</head>
<body>
    <div class="login-container"><div class="login-form-box">
        <div class="login-logo"><h3>Student Portal 🎓</h3></div>
        
        <?php if(isset($error)) echo "<p style='color:red; text-align:center;'>$error</p>"; ?>
        
        <form method="post">
            <div class="form-group">
                <label>Mã Sinh Viên / Email</label>
                <input type="text" name="login_input" class="form-control" placeholder="Nhập ID (vd: 1) hoặc Email" required>
            </div>
            <div class="form-group">
                <label>Mật khẩu</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" name="login" class="btn-submit">Đăng nhập</button>
        </form>
        
        <div style="text-align:center; margin-top:15px;">
            <a href="login.php" style="color:#E65100; text-decoration:none;">⬅️ Quay lại trang Giáo viên</a>
        </div>
    </div></div>
</body>
</html>