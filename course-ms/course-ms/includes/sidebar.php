<?php $current_page = basename($_SERVER['PHP_SELF']); ?>
<aside class="sidebar">
    <div class="brand">
        <i class="fa-solid fa-bee"></i>
        <span>CourseMS<span style="color:#D97706">.Pro</span></span>
    </div>
    
    <ul class="menu-list">
        <li class="menu-item">
            <a href="home.php" class="menu-link <?php echo ($current_page == 'home.php') ? 'active' : ''; ?>">
                <i class="fa-solid fa-grid-2"></i> Dashboard
            </a>
        </li>
        <li class="menu-label" style="padding: 15px 16px 5px; font-size: 11px; font-weight: 700; color: #94A3B8; text-transform: uppercase;">Management</li>
        
        <li class="menu-item">
            <a href="manage_students.php" class="menu-link <?php echo ($current_page == 'manage_students.php') ? 'active' : ''; ?>">
                <i class="fa-solid fa-user-graduate"></i> Students
            </a>
        </li>
        <li class="menu-item">
            <a href="manage_classes.php" class="menu-link <?php echo ($current_page == 'manage_classes.php') ? 'active' : ''; ?>">
                <i class="fa-solid fa-chalkboard-user"></i> Classes
            </a>
        </li>
        <li class="menu-item">
            <a href="manage_exams.php" class="menu-link <?php echo ($current_page == 'manage_exams.php') ? 'active' : ''; ?>">
                <i class="fa-solid fa-file-pen"></i> Exams
            </a>
        </li>
    </ul>

    <div class="sidebar-footer">
        <a href="logout.php" class="menu-link" style="color: #EF4444; background: #FEF2F2;">
            <i class="fa-solid fa-right-from-bracket"></i> Logout
        </a>
    </div>
</aside>