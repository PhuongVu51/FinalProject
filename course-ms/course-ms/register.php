<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - Teacher Bee</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="login-container"> <div class="login-form-box" style="margin-top: 50px; margin-bottom: 50px;">
            
            <h2>Teacher Sign Up</h2>
            <p>Please fill in the details below.</p>
            
            <form action="registration.php" method="post">
                
                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" name="full_name" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control" required>
                </div>

                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label>Date of Birth</label>
                    <input type="date" name="dob" class="form-control">
                </div>
                
                <div class="form-group">
                    <label>Gender</label>
                    <select name="gender" class="form-control">
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                        <option value="other">Other</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Subject(s)</label>
                    <input type="text" name="subjects" class="form-control" placeholder="e.g., Math, English...">
                </div>
                
                <button type="submit" class="btn-submit">Sign Up</button>
            </form>

            <div class="register-link">
                Already have an account? <a href="login.php">Login</a>
            </div>
        </div>
    </div>
</body>
</html>