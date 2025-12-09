<?php
session_start();
if(!isset($_SESSION['username'])){
    header('location:login.php');
}
?>

<html lang='en'>
<head>
    <title>Teacher Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="dashboard_style.css">
</head>
<body>

    <nav class="header-nav">
        <a href="home.php" class="logo">CourseMS🐝</a>
        <a href="logout.php" class="logout-btn">Log out</a>
    </nav>

    <div class="main-container">
        <div class="content-box">
            
            <h1 class="page-title">Welcome, <?php echo $_SESSION['username']; ?>!</h1>
            <p>This is your teacher dashboard. Please choose an action:</p>
            <hr>
            
            <div class="text-center dashboard-buttons">
                <a href="manage_students.php" class="btn btn-dashboard">
                    🎓 Manage Students
                </a>
                <a href="manage_exams.php" class="btn btn-dashboard">
                    📝 Manage Exams
                </a>
                <a href="manage_classes.php" class="btn btn-dashboard">
                    🏫 Manage Classes
                </a>
            </div>

        </div>
    </div>

</body>
</html>