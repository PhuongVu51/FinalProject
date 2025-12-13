<?php 
$cp = basename($_SERVER['PHP_SELF']); 
$role = $_SESSION['role'] ?? '';
?>
<aside class="sidebar">
    <div class="brand">
        <i class="fa-solid fa-bee"></i> TeacherBee
    </div>
    <ul class="menu-list">
        <li class="menu-label">Học Tập</li>
        <li><a href="student_home.php" class="menu-link <?php echo ($cp=='student_home.php')?'active':''; ?>"><i class="fa-solid fa-house"></i> Trang Chủ</a></li>
        <li><a href="student_classes.php" class="menu-link <?php echo ($cp=='student_classes.php')?'active':''; ?>"><i class="fa-solid fa-chalkboard-user"></i> Lớp Học</a></li>
        <li><a href="student_dashboard.php" class="menu-link <?php echo ($cp=='student_dashboard.php')?'active':''; ?>"><i class="fa-solid fa-star"></i> Xem Điểm</a></li>
    </ul>
    <div class="sidebar-footer">
        <a href="../public/logout.php" class="menu-link" style="color:#EF4444; justify-content:center; background:#FEF2F2;">
            <i class="fa-solid fa-arrow-right-from-bracket"></i> Đăng Xuất
        </a>
    </div>
</aside>