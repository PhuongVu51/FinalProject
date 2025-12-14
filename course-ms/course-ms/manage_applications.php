<?php
include "connection.php";
include "auth.php";
requireRole(['admin']);

if(isset($_GET['ok'])){
    $id = intval($_GET['ok']);
    $app = mysqli_fetch_assoc(mysqli_query($link, "SELECT * FROM applications WHERE id=$id"));
    mysqli_query($link, "UPDATE applications SET status='approved' WHERE id=$id");
    mysqli_query($link, "UPDATE students SET class_id={$app['class_id']} WHERE id={$app['student_id']}");
    header("Location: manage_applications.php");
}
?>
<!DOCTYPE html>
<html>
<link rel="stylesheet" href="https://site-assets.fontawesome.com/releases/v6.4.2/css/all.css">
<head><title>Applications</title><link rel="stylesheet" href="dashboard_style.css"></head>
<body>
<?php include "includes/sidebar.php"; ?>
<div class="main-wrapper"><?php include "includes/topbar.php"; ?>
<div class="content-scroll">
<div class="card">
        <h3>Đơn Xin Vào Lớp <span style="font-size:14px; font-weight:normal; color:#64748B; margin-left:10px;">(Cần duyệt)</span></h3>
        <table class="dataTable">
            <thead><tr><th>Học Sinh</th><th>Mã SV</th><th>Nguyện Vọng</th><th>Ngày Nộp</th><th width="100">Xử Lý</th></tr></thead>
            <tbody>
            <?php $res=mysqli_query($link, "SELECT a.*, u.full_name, s.student_code, c.name FROM applications a JOIN students s ON a.student_id=s.id JOIN users u ON s.user_id=u.id JOIN classes c ON a.class_id=c.id WHERE a.status='pending'");
            if(mysqli_num_rows($res)==0) echo "<tr><td colspan='5' align='center' style='padding:30px; color:#94A3B8;'>Không có đơn nào đang chờ.</td></tr>";
            while($r=mysqli_fetch_assoc($res)): ?>
                <tr>
                    <td style="font-weight:700"><?php echo $r['full_name']; ?></td>
                    <td><?php echo $r['student_code']; ?></td>
                    <td style="color:var(--primary); font-weight:600;"><?php echo $r['name']; ?></td>
                    <td style="font-size:13px; color:#64748B"><?php echo date('d/m/Y', strtotime($r['applied_at'])); ?></td>
                    <td>
                        <a href="?ok=<?php echo $r['id']; ?>" class="btn-success" title="Chấp nhận">
                            <i class="fa-solid fa-check"></i> Duyệt
                        </a>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div></div>
</body></html>