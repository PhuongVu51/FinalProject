<?php 
$cp = basename($_SERVER['PHP_SELF']); 
$role = $_SESSION['role'] ?? '';
?>
<aside class="sidebar">
    <div class="brand">
        <i class="fa-solid fa-bee"></i> <span>MathsAM</span>
    </div>
    
    <ul class="menu-list">
        <?php if($role == 'student'): ?>
            <li class="menu-label">LEARNING</li>
            <li><a href="student_home.php" class="menu-link <?php echo ($cp=='student_home.php')?'active':''; ?>"><i class="fa-solid fa-house"></i> Home</a></li>
            <li><a href="student_classes.php" class="menu-link <?php echo ($cp=='student_classes.php')?'active':''; ?>"><i class="fa-solid fa-layer-group"></i> My Classes</a></li>
            <li><a href="student_dashboard.php" class="menu-link <?php echo ($cp=='student_dashboard.php')?'active':''; ?>"><i class="fa-solid fa-chart-simple"></i> Grades</a></li>

        <?php elseif($role == 'teacher'): ?>
            <li class="menu-label">MAIN</li>
            <li><a href="home.php" class="menu-link <?php echo ($cp=='home.php')?'active':''; ?>"><i class="fa-solid fa-grid-2"></i> Dashboard</a></li>
            <li class="menu-label">ACADEMIC</li>
            <li><a href="manage_exams.php" class="menu-link <?php echo ($cp=='manage_exams.php'||$cp=='enter_scores.php')?'active':''; ?>"><i class="fa-solid fa-file-pen"></i> Exams & Scores</a></li>

        <?php elseif($role == 'admin'): ?>
            <li class="menu-label">OVERVIEW</li>
            <li><a href="home.php" class="menu-link <?php echo ($cp=='home.php')?'active':''; ?>"><i class="fa-solid fa-chart-pie"></i> Dashboard</a></li>
            
            <li class="menu-label">MANAGEMENT</li>
            <li><a href="manage_classes.php" class="menu-link <?php echo ($cp=='manage_classes.php')?'active':''; ?>"><i class="fa-solid fa-chalkboard"></i> Classes</a></li>
            <li><a href="manage_teachers.php" class="menu-link <?php echo ($cp=='manage_teachers.php')?'active':''; ?>"><i class="fa-solid fa-chalkboard-user"></i> Teachers</a></li>
            <li><a href="manage_students.php" class="menu-link <?php echo ($cp=='manage_students.php')?'active':''; ?>"><i class="fa-solid fa-user-graduate"></i> Students</a></li>
            
            <li class="menu-label">SYSTEM</li>
            <li><a href="manage_applications.php" class="menu-link <?php echo ($cp=='manage_applications.php')?'active':''; ?>"><i class="fa-solid fa-envelope-open-text"></i> Applications</a></li>
            <li><a href="manage_news.php" class="menu-link <?php echo ($cp=='manage_news.php')?'active':''; ?>"><i class="fa-regular fa-newspaper"></i> News</a></li>
        <?php endif; ?>
    </ul>
    
    <div class="sidebar-footer">
        <a href="logout.php" class="btn-logout">
            <i class="fa-solid fa-arrow-right-from-bracket"></i> Logout
        </a>
    </div>
</aside>