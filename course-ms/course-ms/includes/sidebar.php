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
            <li><a href="student_scores.php" class="menu-link <?php echo ($cp=='student_scores.php')?'active':''; ?>"><i class="fa-solid fa-star"></i> Kết Quả Học Tập</a></li>
            <li class="menu-label">Thông tin</li>
            <li><a href="student_news.php" class="menu-link <?php echo ($cp=='student_news.php')?'active':''; ?>"><i class="fa-solid fa-newspaper"></i> Tin Tức</a></li>


        <?php elseif($role == 'teacher'): ?>
            <li class="menu-label">Quản Lý</li>
            <li><a href="home.php" class="menu-link <?php echo ($cp=='home.php')?'active':''; ?>"><i class="fa-solid fa-chart-line"></i> Dashboard</a></li>
            <li class="menu-label">Giảng Dạy</li>
            <li><a href="manage_exams.php" class="menu-link <?php echo ($cp=='manage_exams.php'||$cp=='enter_scores.php')?'active':''; ?>"><i class="fa-solid fa-file-pen"></i> Bài Thi & Điểm</a></li>

        <?php elseif($role == 'admin'): ?>
            <li class="menu-label">Tổng Quan</li>
            <li><a href="home.php" class="menu-link <?php echo ($cp=='home.php')?'active':''; ?>"><i class="fa-solid fa-chart-simple"></i> Dashboard</a></li>
            
            <li class="menu-label">Quản Trị</li>
            <li><a href="manage_classes.php" class="menu-link <?php echo ($cp=='manage_classes.php'||$cp=='edit_class.php')?'active':''; ?>"><i class="fa-solid fa-chalkboard"></i> Lớp Học</a></li>
            <li><a href="manage_teachers.php" class="menu-link <?php echo ($cp=='manage_teachers.php')?'active':''; ?>"><i class="fa-solid fa-person-chalkboard"></i> Giáo Viên</a></li>
            <li><a href="manage_students.php" class="menu-link <?php echo ($cp=='manage_students.php'||$cp=='edit_student.php')?'active':''; ?>"><i class="fa-solid fa-user-graduate"></i> Học Sinh</a></li>
            
            <li class="menu-label">Hệ Thống</li>
            <li><a href="manage_applications.php" class="menu-link <?php echo ($cp=='manage_applications.php')?'active':''; ?>"><i class="fa-solid fa-file-signature"></i> Duyệt Đơn</a></li>
            <li><a href="manage_news.php" class="menu-link <?php echo ($cp=='manage_news.php')?'active':''; ?>"><i class="fa-regular fa-newspaper"></i> Tin Tức</a></li>
        <?php endif; ?>

    </ul>
    <div class="sidebar-footer">
        <a href="logout.php" class="menu-link" style="color:#EF4444; justify-content:center; background:#FEF2F2;">
            <i class="fa-solid fa-arrow-right-from-bracket"></i> Đăng Xuất
        </a>
    </div>
</aside>

<style>
.menu-list {
    flex: 1;
    overflow-y: auto;
    padding: 1rem 0;
    list-style: none;
    margin: 0;
}

.menu-label {
    padding: 1.25rem 1.5rem 0.5rem;
    margin-top: 1rem;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    color: #9ca3af;
    letter-spacing: 0.05em;
}
</style>
