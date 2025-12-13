<?php
include "connection.php";
include "auth.php";
requireRole(['admin']);

if(isset($_POST['add'])){
    $name = $_POST['name']; $tid = intval($_POST['tid']);
    $tsql = ($tid>0)?$tid:"NULL";
    mysqli_query($link, "INSERT INTO classes (name, teacher_id) VALUES ('$name', $tsql)");
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
<head><title>Classes</title><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"><link rel="stylesheet" href="dashboard_style.css"></head>
<body>
<?php include "includes/sidebar.php"; ?>
<div class="main-wrapper"><?php include "includes/topbar.php"; ?>
<div class="content-scroll">
<<div class="card">
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