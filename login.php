<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Login - Teacher Bee</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="login-container">
        <div class="login-form-box">
            <div class="login-logo"><h3>Teacher Login</h3></div>
            
            <?php if(isset($_GET['error'])): ?>
                <p style="color:red; text-align:center;">Sai email hoặc mật khẩu!</p>
            <?php endif; ?>
            
            <form action="validation.php" method="post">
                <div class="form-group">
                    <label>Email</label>
                    <input type="text" name="email" class="form-control" required
                           value="<?php echo isset($_COOKIE['user_email']) ? $_COOKIE['user_email'] : ''; ?>">
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <div class="form-group">
                    <input type="checkbox" name="remember" id="rem"> <label for="rem">Remember Me</label>
                </div>
                <button type="submit" class="btn-submit">Login</button>
            </form>
            <div class="register-link">
                <a href="student_login.php">👉 Học sinh đăng nhập tại đây</a>
            </div>
        </div>
    </div>
</body>
</html>