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

// Không còn workflow duyệt giáo viên nhận lớp
?>
<!DOCTYPE html>
<html>
<link rel="stylesheet" href="https://site-assets.fontawesome.com/releases/v6.4.2/css/all.css">
<head><title>Classes</title><link rel="stylesheet" href="dashboard_style.css"></head>
<body>
<?php include "includes/sidebar.php"; ?>
<div class="main-wrapper"><?php include "includes/topbar.php"; ?>
<div class="content-scroll">

    <div class="card" style="margin-bottom:24px;">
        <div class="card-header"><h3><i class="fa-solid fa-circle-plus" style="color:#F59E0B"></i> Tạo Lớp Học</h3></div>
        <form method="post" style="display:grid; grid-template-columns: 1.4fr 1fr auto; gap:12px; align-items:end;">
            <div>
                <label class="form-label">Tên lớp học</label>
                <input type="text" name="name" class="form-control" placeholder="VD: Toán 12A" required>
            </div>
            <div>
                <label class="form-label">Giáo viên</label>
                <select name="tid" class="form-control">
                    <option value="0">-- Chưa gán giáo viên --</option>
                    <?php foreach($teacherOptions as $t): ?>
                        <option value="<?php echo $t['id']; ?>"><?php echo $t['full_name']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button name="add" class="btn-primary" style="height:52px;">Tạo lớp</button>
        </form>
    </div>

    <div class="card">
        <h3>Danh Sách Lớp Học</h3>
        <table class="dataTable">
            <thead><tr><th>Tên Lớp</th><th>Giáo Viên Chủ Nhiệm</th><th width="120">Hành Động</th></tr></thead>
            <tbody>
            <?php $res=mysqli_query($link, "SELECT c.*, u.full_name FROM classes c LEFT JOIN teachers t ON c.teacher_id=t.id LEFT JOIN users u ON t.user_id=u.id");
            while($r=mysqli_fetch_assoc($res)): ?>
                <tr>
                    <td style="font-size:15px; font-weight:700"><?php echo $r['name']; ?></td>
                    <td>
                        <?php if($r['full_name']): ?>
                            <div style="display:flex; align-items:center; gap:8px;">
                                <div style="width:24px; height:24px; background:#EFF6FF; color:#3B82F6; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:10px;"><i class="fa-solid fa-user-tie"></i></div>
                                <?php echo $r['full_name']; ?>
                            </div>
                        <?php else: ?>
                            <span style="color:#94A3B8">-- Trống --</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="edit_class.php?id=<?php echo $r['id']; ?>" class="action-btn btn-edit" title="Sửa"><i class="fa-solid fa-pen"></i></a>
                        <a href="?del=<?php echo $r['id']; ?>" onclick="return confirm('Xóa lớp?')" class="action-btn btn-delete" title="Xóa"><i class="fa-solid fa-trash"></i></a>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div></div>
</body></html>