<?php
include "connection.php"; include "auth.php"; requireRole(['admin']);

if(isset($_POST['add'])) {
    $name = mysqli_real_escape_string($link, $_POST['name']); 
    $email = mysqli_real_escape_string($link, $_POST['email']); 
    $pass = md5($_POST['pass']);
    // ... (Giữ nguyên logic thêm)
    mysqli_query($link, "INSERT INTO users (username,password,role,full_name) VALUES ('$email','$pass','teacher','$name')");
    $uid = mysqli_insert_id($link);
    mysqli_query($link, "INSERT INTO teachers (user_id,email) VALUES ($uid,'$email')");
    header("Location: manage_teachers.php");
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Quản Lý Giáo Viên</title>
    <link rel="stylesheet" href="https://site-assets.fontawesome.com/releases/v6.4.2/css/all.css">
    <link rel="stylesheet" href="dashboard_style.css">
</head>
<body>
<?php include "includes/sidebar.php"; ?>
<div class="main-wrapper">
    <div class="topbar"><h2 class="page-breadcrumb">Quản Lý Giáo Viên</h2></div>
    
    <div class="content-scroll">
        <div class="card">
            <h3><i class="fa-solid fa-user-plus" style="color:#F59E0B"></i> Thêm Giáo Viên Mới</h3>
            <form method="post">
                <div class="form-group">
                    <label class="form-label">Họ và Tên</label>
                    <input type="text" name="name" class="form-control" placeholder="Nhập tên giáo viên..." required>
                </div>
                <div class="form-group">
                    <label class="form-label">Email Đăng Nhập</label>
                    <input type="email" name="email" class="form-control" placeholder="example@bee.com" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Mật Khẩu</label>
                    <input type="password" name="pass" class="form-control" placeholder="••••••" required>
                </div>
                <button name="add" class="btn-primary">
                    <i class="fa-solid fa-check"></i> Lưu Lại
                </button>
            </form>
        </div>

        <div class="card">
            <h3>Danh Sách Giáo Viên</h3>
            <table class="dataTable">
                <thead><tr><th>Họ Tên</th><th>Email</th><th width="100">Hành Động</th></tr></thead>
                <tbody>
                <?php $res=mysqli_query($link, "SELECT t.*, u.full_name, u.id as uid FROM teachers t JOIN users u ON t.user_id=u.id");
                while($r=mysqli_fetch_assoc($res)): ?>
                    <tr>
                        <td><b><?php echo $r['full_name']; ?></b></td>
                        <td style="color:#64748B"><?php echo $r['email']; ?></td>
                        <td>
                            <a href="?del=<?php echo $r['uid']; ?>" onclick="return confirm('Xóa giáo viên này?')" class="action-btn btn-delete" title="Xóa">
                                <i class="fa-solid fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body></html>