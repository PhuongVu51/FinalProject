<?php
session_start();
if(!isset($_SESSION['username'])){ header('location:login.php'); }

// Gọi file auth để dùng hàm checkAccess
include "auth.php"; 
?>

<html lang='en'>
<head>
    <title>Teacher Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="dashboard_style.css">
</head>
<body>

    <nav class="header-nav">
        <a href="home.php" class="logo">CourseMS🐝 
            <span style="font-size:12px; background:#fff; color:#333; padding:2px 6px; border-radius:4px; vertical-align:middle;">
                <?php echo ($_SESSION['role_id'] == 1) ? 'ADMIN' : 'TEACHER'; ?>
            </span>
        </a>
        <a href="logout.php" class="logout-btn">Log out</a>
    </nav>

    <div class="main-container">
        <div class="content-box">
            
            <h1 class="page-title">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
            <p>This is your dashboard. Please choose an action:</p>
            <hr>
            
            <div class="text-center dashboard-buttons">
                
                <?php if(checkAccess('manage_students')): ?>
                <a href="manage_students.php" class="btn btn-dashboard">
                    🎓 Manage Students
                </a>
                <?php endif; ?>

                <?php if(checkAccess('manage_exams')): ?>
                <a href="manage_exams.php" class="btn btn-dashboard">
                    📝 Manage Exams
                </a>
                <?php endif; ?>

                <a href="manage_classes.php" class="btn btn-dashboard">
                    🏫 Manage Classes
                </a>
                
                <?php if(checkAccess('delete_data')): ?>
                <div style="margin-top:20px; border-top:1px dashed #ccc; padding-top:20px;">
                    <p class="text-danger">⚠️ <strong>Admin Zone</strong></p>
                    <button class="btn btn-danger">System Settings (Demo)</button>
                </div>
                <?php endif; ?>

            </div>

        </div>
    </div>

</body>
</html>