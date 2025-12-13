<header class="topbar">
    <h2 class="page-breadcrumb">Hệ Thống Quản Lý</h2>
    <div class="user-profile">
        <div class="user-info">
            <span class="user-name"><?php echo $_SESSION['full_name']; ?></span>
            <span class="user-role"><?php echo ucfirst($_SESSION['role']); ?></span>
        </div>
        <div class="user-avatar">
            <?php echo strtoupper(substr($_SESSION['full_name'], 0, 1)); ?>
        </div>
    </div>
</header>