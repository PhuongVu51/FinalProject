<div class="topbar">
    <!-- Left: Logo + Title -->
    <div class="topbar-left">
        <i class="fa-solid fa-honey-pot topbar-logo"></i>
        <span class="topbar-title">Hệ Thống Quản Lý</span>
    </div>

    <!-- Right: User Info -->
    <div class="user-box">
        <div class="user-info">
            <p class="user-name">
                <?php echo isset($_SESSION['full_name']) ? $_SESSION['full_name'] : 'Admin'; ?>
            </p>
            <p class="user-role">
                <?php 
                    $role = isset($_SESSION['role']) ? $_SESSION['role'] : 'admin';
                    // Hiển thị tên chức vụ tiếng Việt
                    if($role == 'admin') echo 'Quản Trị Viên';
                    elseif($role == 'teacher') echo 'Giáo Viên';
                    elseif($role == 'student') echo 'Học Sinh';
                    else echo ucfirst($role);
                ?>
            </p>
        </div>
        
        <div class="user-avatar">
            <?php 
                // Lấy chữ cái đầu của tên để làm Avatar
                $name = isset($_SESSION['full_name']) ? $_SESSION['full_name'] : 'A';
                echo strtoupper(substr($name, 0, 1)); 
            ?>
        </div>
    </div>
</div>