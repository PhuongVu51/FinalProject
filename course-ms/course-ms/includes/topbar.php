<div class="topbar">
    <div class="page-title">
        Hệ Thống Quản Lý
    </div>

    <div class="user-box">
        <div class="user-info">
            <span class="user-name"><?php echo isset($_SESSION['full_name']) ? $_SESSION['full_name'] : 'Admin'; ?></span>
            
            <span class="user-role"><?php echo isset($_SESSION['role']) ? ucfirst($_SESSION['role']) : 'User'; ?></span>
        </div>
        
        <div class="user-avatar">
            <?php 
                $name = isset($_SESSION['full_name']) ? $_SESSION['full_name'] : 'A';
                echo strtoupper(substr($name, 0, 1)); 
            ?>
        </div>
    </div>
</div>