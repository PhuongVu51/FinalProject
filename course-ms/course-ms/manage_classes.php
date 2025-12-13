<?php
include "connection.php";
include "auth.php";
requireRole(['admin']);

// Lấy danh sách giáo viên cho dropdown
$teacherOptions = [];
$tRes = mysqli_query($link, "SELECT t.id, u.full_name FROM teachers t JOIN users u ON t.user_id=u.id ORDER BY u.full_name");
while($t = mysqli_fetch_assoc($tRes)) $teacherOptions[] = $t;

if(isset($_POST['add'])){
    $name = trim($_POST['name'] ?? '');
    $tid = intval($_POST['tid'] ?? 0);
    $tsql = ($tid>0)?$tid:"NULL";
    $nameEsc = mysqli_real_escape_string($link, $name);
    mysqli_query($link, "INSERT INTO classes (name, teacher_id) VALUES ('$nameEsc', $tsql)");
    header("Location: manage_classes.php");
}
if(isset($_GET['del'])){
    mysqli_query($link, "DELETE FROM classes WHERE id=".intval($_GET['del']));
    header("Location: manage_classes.php");
}
?>
<!DOCTYPE html>
<html>
<link rel="stylesheet" href="https://site-assets.fontawesome.com/releases/v6.4.2/css/all.css">
<head><title>Classes</title>
<link rel="stylesheet" href="dashboard_style.css">
<style>
    body { background-color: #F8FAFC; font-family: 'Segoe UI', sans-serif; }
    .card { background: white; border-radius: 16px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); border: 1px solid #E2E8F0; overflow: hidden; margin-bottom: 24px; }
    .card-header { padding: 20px 24px; border-bottom: 1px solid #F1F5F9; background: white; }
    .card-header h3 { margin:0; color: #1E293B; font-size: 18px; display:flex; align-items:center; gap:10px;}
    .form-control { border: 1px solid #CBD5E1; border-radius: 8px; padding: 10px; width: 100%; background: #F8FAFC; }
    .btn-primary { background: #F59E0B; border:none; border-radius:8px; color:white; font-weight:600; cursor:pointer; }
    .btn-primary:hover { background: #D97706; }
    
    .dataTable { width: 100%; border-collapse: collapse; }
    .dataTable th { background: #F8FAFC; color: #64748B; font-size: 12px; text-transform: uppercase; padding: 16px 24px; text-align: left; border-bottom: 1px solid #E2E8F0; }
    .dataTable td { padding: 16px 24px; border-bottom: 1px solid #F1F5F9; color: #334155; font-size: 14px; vertical-align: middle; }
    .dataTable tr:last-child td { border-bottom: none; }
    
    /* CSS cho nút hành động */
    .action-btn { width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center; border-radius: 6px; transition: 0.2s; border: none; cursor: pointer; }
    .btn-edit { background: #EFF6FF; color: #3B82F6; } .btn-edit:hover { background: #DBEAFE; }
    .btn-delete { background: #FEF2F2; color: #EF4444; } .btn-delete:hover { background: #FEE2E2; }
</style>
</head>
<body>
<?php include "includes/sidebar.php"; ?>
<div class="main-wrapper"><?php include "includes/topbar.php"; ?>
<div class="content-scroll">

    <div class="card">
        <div class="card-header"><h3><i class="fa-solid fa-circle-plus" style="color:#F59E0B"></i> Tạo Lớp Học</h3></div>
        <div style="padding: 24px;">
            <form method="post" style="display:grid; grid-template-columns: 1.4fr 1fr auto; gap:16px; align-items:end;">
                <div>
                    <label style="font-weight:600; color:#475569; font-size:13px; margin-bottom:6px; display:block;">Tên lớp học</label>
                    <input type="text" name="name" class="form-control" placeholder="VD: Toán 12A" required>
                </div>
                <div>
                    <label style="font-weight:600; color:#475569; font-size:13px; margin-bottom:6px; display:block;">Giáo viên</label>
                    <select name="tid" class="form-control">
                        <option value="0">-- Chưa gán giáo viên --</option>
                        <?php foreach($teacherOptions as $t): ?>
                            <option value="<?php echo $t['id']; ?>"><?php echo $t['full_name']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button name="add" class="btn-primary" style="height:42px; padding:0 24px;">Tạo lớp</button>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><h3>Danh Sách Lớp Học</h3></div>
        <table class="dataTable">
            <thead>
                <tr>
                    <th>Tên Lớp</th>
                    <th>Giáo Viên Chủ Nhiệm</th>
                    <th width="150" style="text-align: center;">CHỈNH SỬA</th>
                </tr>
            </thead>
            <tbody>
            <?php $res=mysqli_query($link, "SELECT c.*, u.full_name FROM classes c LEFT JOIN teachers t ON c.teacher_id=t.id LEFT JOIN users u ON t.user_id=u.id");
            while($r=mysqli_fetch_assoc($res)): ?>
                <tr>
                    <td style="font-size:15px; font-weight:700; color:#0F172A;"><?php echo $r['name']; ?></td>
                    <td>
                        <?php if($r['full_name']): ?>
                            <div style="display:flex; align-items:center; gap:10px;">
                                <div style="width:28px; height:28px; background:#EFF6FF; color:#3B82F6; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:12px;"><i class="fa-solid fa-user-tie"></i></div>
                                <span style="font-weight:500;"><?php echo $r['full_name']; ?></span>
                            </div>
                        <?php else: ?>
                            <span style="color:#94A3B8; font-style:italic; font-size:13px;">-- Trống --</span>
                        <?php endif; ?>
                    </td>
                    <td style="text-align: center;">
                        <div style="display: flex; align-items: center; justify-content: center; gap: 8px;">
                            <a href="edit_class.php?id=<?php echo $r['id']; ?>" class="action-btn btn-edit" title="Sửa">
                                <i class="fa-solid fa-pen"></i>
                            </a>
                            <a href="?del=<?php echo $r['id']; ?>" onclick="return confirm('Xóa lớp?')" class="action-btn btn-delete" title="Xóa">
                                <i class="fa-solid fa-trash"></i>
                            </a>
                        </div>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div></div>
</body></html>