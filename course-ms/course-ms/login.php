<?php
session_start();
if(isset($_SESSION['user_id'])) {
    if($_SESSION['role'] == 'student') header("Location: student_home.php");
    else header("Location: home.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<link rel="stylesheet" href="https://site-assets.fontawesome.com/releases/v6.4.2/css/all.css">
<head><title>Login</title><link rel="stylesheet" href="style.css"></head>
<body>
<div class="login-container"><div class="login-form-box">
    <h3>🐝 Đăng Nhập</h3>
    <?php if(isset($_GET['error'])): ?>
        <p style="color:red; text-align:center;">
            <?php echo ($_GET['error']=='fail') ? "Sai tài khoản/mật khẩu!" : "Vui lòng nhập đủ thông tin."; ?>
        </p>
    <?php endif; ?>
    <form action="validation.php" method="post">
        <div class="form-group"><label>Tài khoản</label><input type="text" name="username" class="form-control"></div>
        <div class="form-group"><label>Mật khẩu</label><input type="password" name="password" class="form-control"></div>
        <div style="margin-bottom:10px"><input type="checkbox" name="remember"> Ghi nhớ tôi</div>
        <button type="submit" name="login" class="btn-submit">Đăng Nhập</button>
    </form>
    <div style="text-align:center; margin-top:15px"><a href="register.php">Đăng ký Giáo Viên</a></div>
</div></div>
</body>
</html>