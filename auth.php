<?php
// auth.php - Xử lý kiểm tra quyền
function checkAccess($required_permission) {
    if(isset($_SESSION['role_id']) && $_SESSION['role_id'] == 1) return true; // Admin chấp hết
    $perms = isset($_SESSION['permissions']) ? $_SESSION['permissions'] : [];
    return in_array($required_permission, $perms);
}

function requirePermission($permission) {
    if (!checkAccess($permission)) {
        echo "<div style='padding:50px; text-align:center; color:red;'><h1>🚫 Access Denied</h1><p>Bạn không có quyền: $permission</p><a href='home.php'>Quay lại</a></div>";
        exit();
    }
}
?>