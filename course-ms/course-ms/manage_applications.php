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
<head><title>Applications</title>
<link rel="stylesheet" href="dashboard_style.css">
<style>
    body { background-color: #F8FAFC; font-family: 'Segoe UI', sans-serif; }
    .card { background: white; border-radius: 16px; border: 1px solid #E2E8F0; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); overflow:hidden; }
    .card h3 { padding: 20px 24px; margin:0; border-bottom: 1px solid #F1F5F9; color: #1E293B; font-size: 18px; }
    
    .dataTable { width: 100%; border-collapse: collapse; }
    .dataTable th { background: #F8FAFC; color: #64748B; font-size: 12px; font-weight: 600; text-transform: uppercase; padding: 16px 24px; text-align: left; border-bottom: 1px solid #E2E8F0; }
    .dataTable td { padding: 16px 24px; border-bottom: 1px solid #F1F5F9; color: #334155; font-size: 14px; vertical-align: middle; }
    .dataTable tr:last-child td { border-bottom: none; }
    
    .btn-success { background: #10B981; color: white; border: none; padding: 8px 16px; border-radius: 6px; font-weight: 600; text-decoration: none; font-size: 13px; display: inline-flex; align-items: center; gap: 6px; transition:0.2s; }
    .btn-success:hover { background: #059669; transform: translateY(-1px); }
</style>
</head>
<body>
<?php include "includes/sidebar.php"; ?>
<div class="main-wrapper"><?php include "includes/topbar.php"; ?>
<div class="content-scroll">
<div class="card">
        <h3>Đơn Xin Vào Lớp <span style="font-size:14px; font-weight:normal; color:#64748B; margin-left:10px; background:#F1F5F9; padding:4px 10px; border-radius:12px;">Cần duyệt</span></h3>
        <table class="dataTable">
            <thead><tr><th>Học Sinh</th><th>Mã SV</th><th>Nguyện Vọng</th><th>Ngày Nộp</th><th width="120">Xử Lý</th></tr></thead>
            <tbody>
            <?php $res=mysqli_query($link, "SELECT a.*, u.full_name, s.student_code, c.name FROM applications a JOIN students s ON a.student_id=s.id JOIN users u ON s.user_id=u.id JOIN classes c ON a.class_id=c.id WHERE a.status='pending'");
            if(mysqli_num_rows($res)==0) echo "<tr><td colspan='5' align='center' style='padding:40px; color:#94A3B8; font-style:italic;'>Không có đơn nào đang chờ duyệt.</td></tr>";
            while($r=mysqli_fetch_assoc($res)): ?>
                <tr>
                    <td style="font-weight:700; color:#0F172A;"><?php echo $r['full_name']; ?></td>
                    <td><span style="font-family:'Courier New', monospace; background:#F8FAFC; padding:2px 6px; border-radius:4px;"><?php echo $r['student_code']; ?></span></td>
                    <td style="color:#F59E0B; font-weight:600;"><?php echo $r['name']; ?></td>
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