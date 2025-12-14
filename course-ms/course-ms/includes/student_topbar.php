<header class="topbar">
    <h2 class="page-breadcrumb">Cổng Học Tập</h2>
    <div class="user-box">
        <div class="user-info" style="display:flex; flex-direction:column; align-items:flex-end;">
            <span class="user-name" style="font-weight:800; color:#0F172A;"><?php echo $_SESSION['full_name']; ?></span>
            <span class="user-role" style="color:#64748B; font-size:12px; text-transform:capitalize;"><?php echo ucfirst($_SESSION['role']); ?></span>
        </div>
        <div class="user-avatar">
            <?php echo strtoupper(substr($_SESSION['full_name'], 0, 1)); ?>
        </div>
    </div>
</header>