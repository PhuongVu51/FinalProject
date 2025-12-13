<?php
include "connection.php"; include "auth.php"; requireRole(['admin']);

if(isset($_POST['add'])){
    $name=trim($_POST['name']); $user=trim($_POST['user']); $pass=md5($_POST['pass']); $code=trim($_POST['code']);
    mysqli_query($link, "INSERT INTO users (username,password,role,full_name) VALUES ('$user','$pass','student','$name')");
    $uid=mysqli_insert_id($link);
    mysqli_query($link, "INSERT INTO students (user_id,student_code) VALUES ($uid,'$code')");
    header("Location: manage_students.php");
}
if(isset($_GET['del'])){
    mysqli_query($link, "DELETE FROM users WHERE id=".intval($_GET['del']));
    header("Location: manage_students.php");
}
?>
<!DOCTYPE html>
<html>
<link rel="stylesheet" href="https://site-assets.fontawesome.com/releases/v6.4.2/css/all.css">
<head>
    <title>Quản Lý Học Sinh</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="dashboard_style.css">
</head>
<body>
<?php include "includes/sidebar.php"; ?>
<div class="main-wrapper">
    <div class="topbar"><h2 class="page-title">Quản Lý Học Sinh</h2></div>
    
    <div class="content-scroll">
        <div style="display:grid; grid-template-columns: 1fr 2fr; gap:30px">
            <div class="card">
                <div class="card-header"><h3><i class="fa-solid fa-user-plus" style="color:#F59E0B"></i> Thêm Học Sinh</h3></div>
                <form method="post">
                    <div style="margin-bottom:15px"><label class="form-label">Mã Sinh Viên</label><input type="text" name="code" class="form-control" required></div>
                    <div style="margin-bottom:15px"><label class="form-label">Họ Tên</label><input type="text" name="name" class="form-control" required></div>
                    <div style="margin-bottom:15px"><label class="form-label">Tên Đăng Nhập</label><input type="text" name="user" class="form-control" required></div>
                    <div style="margin-bottom:20px"><label class="form-label">Mật Khẩu</label><input type="password" name="pass" class="form-control" required></div>
                    <button name="add" class="btn-primary" style="width:100%; justify-content:center;">Thêm Mới</button>
                </form>
            </div>

            <div class="card">
        <h3>Danh Sách Học Sinh</h3>
        <table class="dataTable">
            <thead><tr><th>Mã SV</th><th>Họ Tên</th><th>Lớp</th><th width="100">Hành Động</th></tr></thead>
            <tbody>
            <?php 
            $q = "SELECT s.*, u.full_name, c.name as cname, u.id as uid FROM students s JOIN users u ON s.user_id=u.id LEFT JOIN classes c ON s.class_id=c.id ORDER BY s.id DESC";
            $rs = mysqli_query($link, $q);
            while($r = mysqli_fetch_assoc($rs)): ?>
                <tr>
                    <td><span style="font-family:monospace; font-weight:700; color:#64748B">#<?php echo $r['student_code']; ?></span></td>
                    <td><b><?php echo $r['full_name']; ?></b></td>
                    <td>
                        <?php if($r['cname']): ?>
                            <span class="badge badge-active"><?php echo $r['cname']; ?></span>
                        <?php else: ?>
                            <span style="color:#94A3B8; font-size:13px; font-style:italic">Chưa có lớp</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="manage_students.php?delete=<?php echo $r['uid']; ?>" onclick="return confirm('Xóa học sinh này?')" class="action-btn btn-delete" title="Xóa">
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
</div>
</body></html>