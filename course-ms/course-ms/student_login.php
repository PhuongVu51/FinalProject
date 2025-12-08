<?php
session_start();
include "connection.php";

if(isset($_POST['login'])) {
    $id_code = $_POST['student_id_code'];
    $pass = md5($_POST['password']);
    
    $res = mysqli_query($link, "SELECT * FROM students WHERE student_id_code='$id_code' AND password='$pass'");
    if(mysqli_num_rows($res) == 1) {
        $st = mysqli_fetch_assoc($res);
        $_SESSION['student_name'] = $st['full_name'];
        $_SESSION['student_id'] = $st['id'];
        header("Location: student_dashboard.php");
    } else {
        $error = "Sai mã số học sinh hoặc mật khẩu!";
    }
}
?>
<html lang="en">
<head>
    <title>Student Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="login-container"><div class="login-form-box">
        <h2>Học sinh Đăng nhập</h2>
        <?php if(isset($error)) echo "<p style='color:red'>$error</p>"; ?>
        <form method="post">
            <div class="form-group"><label>Mã số HS</label><input type="text" name="student_id_code" class="form-control" required></div>
            <div class="form-group"><label>Mật khẩu</label><input type="password" name="password" class="form-control" required></div>
            <button type="submit" name="login" class="btn-submit">Vào xem điểm</button>
        </form>
        <div style="text-align:center; margin-top:10px;"><a href="login.php">Giáo viên đăng nhập</a></div>
    </div></div>
</body>
</html>