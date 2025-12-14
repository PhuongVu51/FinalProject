<?php
include "connection.php"; include "auth.php"; requireRole(['admin']);

if(isset($_POST['add'])) {
    $name = mysqli_real_escape_string($link, $_POST['name']); 
    $email = mysqli_real_escape_string($link, $_POST['email']); 
    $pass = md5($_POST['pass']);
    mysqli_query($link, "INSERT INTO users (username,password,role,full_name) VALUES ('$email','$pass','teacher','$name')");
    $uid = mysqli_insert_id($link);
    mysqli_query($link, "INSERT INTO teachers (user_id,email) VALUES ($uid,'$email')");
    header("Location: manage_teachers.php");
}
if(isset($_GET['del'])){
    mysqli_query($link, "DELETE FROM users WHERE id=".intval($_GET['del']));
    header("Location: manage_teachers.php");
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Quản Lý Giáo Viên</title>
    <link rel="stylesheet" href="https://site-assets.fontawesome.com/releases/v6.4.2/css/all.css">
    <link rel="stylesheet" href="dashboard_style.css">
    <style>
        body { background-color: #F8FAFC; font-family: 'Segoe UI', sans-serif; }
        
        /* ĐỒNG BỘ TIÊU ĐỀ */
        .page-title { font-size: 24px; font-weight: 800; color: #1E293B; margin-bottom: 30px; margin-top: 0; }
        
        .card { background: white; border-radius: 16px; border: 1px solid #E2E8F0; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); overflow:hidden; height: fit-content; }
        .card-header { padding: 20px 24px; border-bottom: 1px solid #F1F5F9; background: white; }
        .card-header h3 { margin:0; font-size:18px; color:#1E293B; display:flex; align-items:center; gap:10px; }
        .form-content { padding: 24px; }
        .form-group { margin-bottom: 16px; }
        .form-label { font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 6px; display: block; }
        .form-control { width: 100%; padding: 10px 14px; border: 1px solid #CBD5E1; border-radius: 8px; background: #F8FAFC; transition: 0.2s; box-sizing: border-box; }
        .form-control:focus { border-color: #F59E0B; background: white; outline:none; }
        .btn-primary { width: 100%; padding: 12px; background: #F59E0B; color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; transition: 0.2s; display: flex; align-items: center; justify-content: center; gap: 8px; }
        .btn-primary:hover { background: #D97706; }
        .dataTable { width: 100%; border-collapse: collapse; }
        .dataTable th { background: #F8FAFC; color: #64748B; font-size: 12px; font-weight: 600; text-transform: uppercase; padding: 16px 24px; text-align: left; border-bottom: 1px solid #E2E8F0; }
        .dataTable td { padding: 14px 24px; border-bottom: 1px solid #F1F5F9; color: #334155; font-size: 14px; }
        .dataTable tr:last-child td { border-bottom: none; }
        .btn-delete { width:32px; height:32px; background:#FEF2F2; color:#EF4444; border-radius:6px; display:flex; align-items:center; justify-content:center; transition:0.2s; }
        .btn-delete:hover { background:#FEE2E2; }
    </style>
</head>
<body>
<?php include "includes/sidebar.php"; ?>
<div class="main-wrapper">
    <?php include "includes/topbar.php"; ?>
    <div class="content-scroll">
        
        <div style="display:grid; grid-template-columns: 1fr 2fr; gap:24px">
            <div class="card">
                <div class="card-header"><h3><i class="fa-solid fa-user-plus" style="color:#F59E0B"></i> Thêm Giáo Viên</h3></div>
                <div class="form-content">
                    <form method="post">
                        <div class="form-group"><label class="form-label">Họ và Tên</label><input type="text" name="name" class="form-control" placeholder="Nhập tên giáo viên..." required></div>
                        <div class="form-group"><label class="form-label">Email Đăng Nhập</label><input type="email" name="email" class="form-control" placeholder="example@bee.com" required></div>
                        <div class="form-group"><label class="form-label">Mật Khẩu</label><input type="password" name="pass" class="form-control" placeholder="••••••" required></div>
                        <button name="add" class="btn-primary"><i class="fa-solid fa-check"></i> Lưu Lại</button>
                    </form>
                </div>
            </div>
            <div class="card">
                <div class="card-header"><h3>Danh Sách Giáo Viên</h3></div>
                <table class="dataTable">
                    <thead><tr><th>Họ Tên</th><th>Email</th><th width="80" align="center">Hành Động</th></tr></thead>
                    <tbody>
                    <?php $res=mysqli_query($link, "SELECT t.*, u.full_name, u.id as uid FROM teachers t JOIN users u ON t.user_id=u.id ORDER BY u.id DESC");
                    while($r=mysqli_fetch_assoc($res)): ?>
                        <tr>
                            <td><div style="display:flex; align-items:center; gap:10px;"><div style="width:32px; height:32px; background:#EFF6FF; color:#3B82F6; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:13px;"><i class="fa-solid fa-chalkboard-user"></i></div><b style="color:#0F172A;"><?php echo $r['full_name']; ?></b></div></td>
                            <td style="color:#64748B"><?php echo $r['email']; ?></td>
                            <td align="center"><a href="?del=<?php echo $r['uid']; ?>" onclick="return confirm('Xóa giáo viên này?')" class="btn-delete" title="Xóa"><i class="fa-solid fa-trash"></i></a></td>
                        </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
</body></html>