<?php
// Exercise 5: Hàm lấy danh sách quyền từ Database
function getUserPermissions($link, $user_id) {
    // Kết nối 3 bảng: teachers -> roles -> role_permissions -> permissions
    $sql = "SELECT p.permission_name 
            FROM teachers t 
            JOIN roles r ON t.role_id = r.role_id 
            JOIN role_permissions rp ON r.role_id = rp.role_id 
            JOIN permissions p ON rp.permission_id = p.permission_id 
            WHERE t.id = $user_id";
            
    $result = mysqli_query($link, $sql);
    $permissions = [];
    
    if($result) {
        while($row = mysqli_fetch_assoc($result)) {
            $permissions[] = $row['permission_name'];
        }
    }
    return $permissions;
}

// Exercise 3: Hàm kiểm tra quyền (dựa trên Session)
function checkAccess($required_permission) {
    // Lấy danh sách quyền từ Session (sẽ được lưu khi login)
    $user_permissions = isset($_SESSION['permissions']) ? $_SESSION['permissions'] : [];
    
    // Nếu là Admin (role_id = 1) thì luôn return true (quyền lực tối cao)
    if(isset($_SESSION['role_id']) && $_SESSION['role_id'] == 1) {
        return true;
    }

    // Kiểm tra xem quyền yêu cầu có trong danh sách quyền của user không
    return in_array($required_permission, $user_permissions);
}

// Exercise 6: Hàm chặn truy cập nếu không có quyền
function requirePermission($permission) {
    if (!checkAccess($permission)) {
        // Giao diện báo lỗi đẹp một chút
        echo "<div style='text-align:center; padding:50px; font-family:sans-serif; background:#fff0f0;'>";
        echo "<h1 style='color:red;'>🚫 Access Denied</h1>";
        echo "<p>Bạn không có quyền thực hiện hành động này: <strong>$permission</strong>.</p>";
        echo "<a href='home.php' style='padding:10px 20px; background:#FFC107; text-decoration:none; color:black; border-radius:5px;'>Quay lại Dashboard</a>";
        echo "</div>";
        exit(); // Dừng code ngay lập tức
    }
}
?>