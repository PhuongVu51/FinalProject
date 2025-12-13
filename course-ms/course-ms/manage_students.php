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
<head>
    <title>Hệ Thống Quản Lý</title>
    <link rel="stylesheet" href="https://site-assets.fontawesome.com/releases/v6.4.2/css/all.css">
    <link rel="stylesheet" href="dashboard_style.css">
    <style>
        body { background-color: #F8FAFC; font-family: 'Segoe UI', sans-serif; }
        .page-title { font-size: 24px; font-weight: 800; color: #1E293B; margin-bottom: 24px; }
        .card { background: white; border-radius: 16px; border: 1px solid #E2E8F0; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); overflow:hidden; }
        .card-header { padding: 20px 24px; border-bottom: 1px solid #F1F5F9; background: white; }
        .card-header h3 { margin:0; font-size:18px; color:#1E293B; display:flex; align-items:center; gap:10px; }
        .form-content { padding: 24px; }
        .form-group { margin-bottom: 16px; }
        .form-label { font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 6px; display: block; }
        .form-control { width: 100%; padding: 10px 14px; border: 1px solid #CBD5E1; border-radius: 8px; background: #F8FAFC; transition: 0.2s; }
        .form-control:focus { border-color: #F59E0B; background: white; outline:none; }
        .btn-primary { width: 100%; padding: 12px; background: #F59E0B; color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; transition: 0.2s; }
        .btn-primary:hover { background: #D97706; }
        
        .dataTable { width: 100%; border-collapse: collapse; }
        .dataTable th { background: #F8FAFC; color: #64748B; font-size: 12px; font-weight: 600; text-transform: uppercase; padding: 16px 24px; text-align: left; border-bottom: 1px solid #E2E8F0; }
        .dataTable td { padding: 14px 24px; border-bottom: 1px solid #F1F5F9; color: #334155; font-size: 14px; }
        .badge-active { background: #ECFDF5; color: #059669; padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 600; }
        .btn-delete { width:32px; height:32px; background:#FEF2F2; color:#EF4444; border-radius:6px; display:flex; align-items:center; justify-content:center; transition:0.2s; }
        .btn-delete:hover { background:#FEE2E2; }
    </style>
</head>
<body>
<?php include "includes/sidebar.php"; ?>
<div class="main-wrapper">
    <div class="content-scroll">
        <h2 class="page-title">Hệ Thống Quản Lý</h2>
        <div style="display:grid; grid-template-columns: 1fr 2.5fr; gap:24px">
            <div class="card" style="height: fit-content;">
                <div class="card-header"><h3><i class="fa-solid fa-user-plus" style="color:#F59E0B"></i> Thêm Mới</h3></div>
                <div class="form-content">
                    <form method="post">
                        <div class="form-group"><label class="form-label">Mã Sinh Viên</label><input type="text" name="code" class="form-control" required placeholder="VD: SV001"></div>
                        <div class="form-group"><label class="form-label">Họ Tên</label><input type="text" name="name" class="form-control" required placeholder="Nguyễn Văn A"></div>
                        <div class="form-group"><label class="form-label">Tên Đăng Nhập</label><input type="text" name="user" class="form-control" required></div>
                        <div class="form-group"><label class="form-label">Mật Khẩu</label><input type="password" name="pass" class="form-control" required></div>
                        <button name="add" class="btn-primary">Thêm Học Sinh</button>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><h3>Danh Sách Học Sinh</h3></div>
                <table class="dataTable">
                    <thead><tr><th>Mã SV</th><th>Họ Tên</th><th>Lớp</th><th width="80" align="center">Xóa</th></tr></thead>
                    <tbody>
                    <?php 
                    $q = "SELECT s.*, u.full_name, c.name as cname, u.id as uid FROM students s JOIN users u ON s.user_id=u.id LEFT JOIN classes c ON s.class_id=c.id ORDER BY s.id DESC";
                    $rs = mysqli_query($link, $q);
                    while($r = mysqli_fetch_assoc($rs)): ?>
                        <tr>
                            <td><span style="font-family:'Courier New', monospace; font-weight:700; color:#64748B; background:#F1F5F9; padding:2px 6px; border-radius:4px;">#<?php echo $r['student_code']; ?></span></td>
                            <td><b style="color:#0F172A;"><?php echo $r['full_name']; ?></b></td>
                            <td>
                                <?php if($r['cname']): ?>
                                    <span class="badge-active"><?php echo $r['cname']; ?></span>
                                <?php else: ?>
                                    <span style="color:#94A3B8; font-size:12px; font-style:italic">Chưa xếp lớp</span>
                                <?php endif; ?>
                            </td>
                            <td align="center">
                                <a href="manage_students.php?delete=<?php echo $r['uid']; ?>" onclick="return confirm('Xóa học sinh này?')" class="btn-delete" title="Xóa">
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