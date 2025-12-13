<?php
include "connection.php";
include "auth.php";
// Sửa lỗi: Thay requirePermission bằng requireRole
requireRole(['admin']); 

$id = isset($_GET["id"]) ? intval($_GET["id"]) : 0;

// Xử lý Xóa
if (isset($_GET['confirm']) && $_GET['confirm'] === 'yes') {
    // Xóa user sẽ tự động xóa student nhờ khóa ngoại CASCADE trong DB mới
    mysqli_query($link, "DELETE FROM users WHERE id=$id");
    header("location:manage_students.php"); 
    exit;
}

// Lấy tên để hiển thị xác nhận
$res = mysqli_query($link, "SELECT full_name FROM users WHERE id=$id");
$item = mysqli_fetch_assoc($res);
if(!$item) { header("Location: manage_students.php"); exit; }
?>

<!DOCTYPE html>
<html lang="en">
<link rel="stylesheet" href="https://site-assets.fontawesome.com/releases/v6.4.2/css/all.css">
<head>
    <title>Xóa Học Sinh</title>
    <link rel="stylesheet" href="dashboard_style.css">
</head>
<body style="padding: 50px; background-color: #FFF8E1;">
    <div class="container text-center" style="max-width:500px; margin:0 auto; background:white; padding:40px; border-radius:10px; box-shadow:0 5px 15px rgba(0,0,0,0.1);">
        <h2 style="color:#DC2626">Cảnh báo xóa!</h2>
        <p>Bạn có chắc chắn muốn xóa học sinh <b>"<?php echo $item['full_name']; ?>"</b>?</p>
        <p style="color:#666; font-size:13px">Hành động này sẽ xóa cả tài khoản đăng nhập và điểm số của học sinh này.</p>
        
        <div style="margin-top:20px; display:flex; gap:10px; justify-content:center;">
            <a href="delete_student.php?id=<?php echo $id ?>&confirm=yes" class="btn-primary" style="background:#DC2626; text-decoration:none;">Có, Xóa Luôn</a>
            <a href="manage_students.php" class="btn-secondary" style="text-decoration:none;">Không, Quay lại</a>
        </div>
    </div>
</body>
</html>