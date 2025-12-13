<?php 
$cp = basename($_SERVER['PHP_SELF']); 
$role = $_SESSION['role'] ?? '';
?>
<aside class="sidebar">
    <div class="brand">
        <i class="fa-solid fa-bee"></i> TeacherBee
    </div>
    <ul class="menu-list">
        
        <?php if($role == 'student'): ?>
            <li class="menu-label">Học Tập</li>
            <li><a href="student_home.php" class="menu-link <?php echo ($cp=='student_home.php')?'active':''; ?>"><i class="fa-solid fa-house"></i> Trang Chủ</a></li>
            <li><a href="student_classes.php" class="menu-link <?php echo ($cp=='student_classes.php')?'active':''; ?>"><i class="fa-solid fa-chalkboard-user"></i> Lớp Học</a></li>
            <li><a href="student_dashboard.php" class="menu-link <?php echo ($cp=='student_dashboard.php')?'active':''; ?>"><i class="fa-solid fa-star"></i> Xem Điểm</a></li>
            <li class="menu-label">Thông Tin</li>
            <li><a href="news.php" class="menu-link <?php echo ($cp=='news.php')?'active':''; ?>"><i class="fa-regular fa-newspaper"></i> Tin Tức</a></li>

        <?php elseif($role == 'teacher'): ?>
            <li class="menu-label">Quản Lý</li>
            <li><a href="teacher_home.php" class="menu-link <?php echo ($cp=='teacher_home.php')?'active':''; ?>"><i class="fa-solid fa-chart-simple"></i> Trang chủ</a></li>
            <li class="menu-label">Giảng Dạy</li>
            <li><a href="teacher_classes.php" class="menu-link <?php echo ($cp=='teacher_classes.php')?'active':''; ?>"><i class="fa-solid fa-chalkboard"></i> Lớp Học</a></li>
            <li><a href="manage_exams.php" class="menu-link <?php echo ($cp=='manage_exams.php'||$cp=='enter_scores.php')?'active':''; ?>"><i class="fa-solid fa-file-pen"></i> Bài Thi & Điểm</a></li>
            <li class="menu-label">Thông Tin</li>
            <li><a href="news.php" class="menu-link <?php echo ($cp=='news.php')?'active':''; ?>"><i class="fa-regular fa-newspaper"></i> Tin Tức</a></li>

        <?php elseif($role == 'admin'): ?>
            <li class="menu-label">Tổng Quan</li>
            <li><a href="home.php" class="menu-link <?php echo ($cp=='home.php')?'active':''; ?>"><i class="fa-solid fa-chart-simple"></i> Dashboard</a></li>
            
            <li class="menu-label">Quản Trị</li>
            <li><a href="manage_classes.php" class="menu-link <?php echo ($cp=='manage_classes.php'||$cp=='edit_class.php')?'active':''; ?>"><i class="fa-solid fa-chalkboard"></i> Lớp Học</a></li>
            <li><a href="manage_teachers.php" class="menu-link <?php echo ($cp=='manage_teachers.php')?'active':''; ?>"><i class="fa-solid fa-person-chalkboard"></i> Giáo Viên</a></li>
            <li><a href="manage_students.php" class="menu-link <?php echo ($cp=='manage_students.php'||$cp=='edit_student.php')?'active':''; ?>"><i class="fa-solid fa-user-graduate"></i> Học Sinh</a></li>
            
            <li class="menu-label">Hệ Thống</li>
            <li><a href="manage_applications.php" class="menu-link <?php echo ($cp=='manage_applications.php')?'active':''; ?>"><i class="fa-solid fa-file-signature"></i> Duyệt Đơn</a></li>
            <li><a href="manage_news.php" class="menu-link <?php echo ($cp=='manage_news.php' || $cp=='news.php')?'active':''; ?>"><i class="fa-regular fa-newspaper"></i> Tin Tức</a></li>
        <?php endif; ?>

    </ul>
    <div class="sidebar-footer">
        <a href="logout.php" class="menu-link" style="color:#EF4444; justify-content:center; background:#FEF2F2;">
            <i class="fa-solid fa-arrow-right-from-bracket"></i> Đăng Xuất
        </a>
    </div>
</aside>