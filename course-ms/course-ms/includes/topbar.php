<header class="topbar">
    <h2 class="page-title">
        <?php 
            $map = [
                'home.php' => 'Dashboard Overview',
                'manage_students.php' => 'Student Management',
                'manage_classes.php' => 'Class Management',
                'manage_exams.php' => 'Exam Management',
                'enter_scores.php' => 'Grading & Scores'
            ];
            echo $map[basename($_SERVER['PHP_SELF'])] ?? 'CourseMS';
        ?>
    </h2>
    <div class="user-profile">
        <div class="user-info" style="text-align:right;">
            <div><?php echo htmlspecialchars($_SESSION['username']); ?></div>
            <span><?php echo ($_SESSION['role_id']==1) ? 'Administrator' : 'Teacher'; ?></span>
        </div>
        <div class="user-avatar"><?php echo strtoupper(substr($_SESSION['username'],0,1)); ?></div>
    </div>
</header>