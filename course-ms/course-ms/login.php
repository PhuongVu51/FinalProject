<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Teacher Bee</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <div class="login-container">
        <div class="login-form-box">
            <div class="login-logo">
                <h3>Online Course Management System</h3>
            </div>
            
            <h2>Login</h2>
            
            <form action="validation.php" method="post">
                <div class="form-group">
                    <label>Email</label>
                    <input type="text" name="email" class="form-control" placeholder="Enter your email" required>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" class="form-control" placeholder="Enter your password" required>
                </div>
                
                <button type="submit" class="btn-submit">Continue</button>
                
                <div class="forgot-password">
                    <a href="#">Forgot password?</a>
                </div>
            </form>

            <div class="register-link">
                Don't have an account? <a href="register.php">Sign up</a>
            </div>
        </div>
    </div>
</body>
</html>