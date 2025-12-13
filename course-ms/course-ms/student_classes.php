<?php
include "connection.php";
include "auth.php";
requireRole(['student']);
$sid = $_SESSION['student_id'];

if(isset($_GET['reg'])){
    $cid = intval($_GET['reg']);
    mysqli_query($link, "INSERT INTO applications (student_id,class_id,status) VALUES ($sid,$cid,'pending')");
    header("Location: student_classes.php");
}
?>
<!DOCTYPE html>
<html>
<head><title>Classes</title><link rel="stylesheet" href="https://site-assets.fontawesome.com/releases/v6.4.2/css/all.css"><link rel="stylesheet" href="dashboard_style.css"></head>
<body>
<?php include "includes/sidebar.php"; ?>
<div class="main-wrapper"><?php include "includes/topbar.php"; ?>
<div class="content-scroll">
<div class="card">
        <h3>Các Lớp Đang Mở</h3>
        <table class="dataTable">
            <thead><tr><th>Tên Lớp</th><th>Giáo Viên</th><th width="150">Trạng Thái</th></tr></thead>
            <tbody>
            <?php 
            $q = "SELECT c.*, u.full_name, 
                  (SELECT status FROM applications WHERE student_id=$sid AND class_id=c.id) as app, 
                  (SELECT class_id FROM students WHERE id=$sid) as cur 
                  FROM classes c LEFT JOIN teachers t ON c.teacher_id=t.id LEFT JOIN users u ON t.user_id=u.id";
            $rs = mysqli_query($link, $q);
            while($r=mysqli_fetch_assoc($rs)){
                $stt = "<a href='?reg={$r['id']}' class='btn-primary' style='padding:8px 16px; font-size:12px;'>Đăng Ký Ngay</a>";
                
                if($r['app']=='pending') 
                    $stt="<span class='badge badge-pending'><i class='fa-regular fa-clock'></i> Chờ duyệt</span>";
                if($r['cur']==$r['id']) 
                    $stt="<span class='badge badge-active'><i class='fa-solid fa-check'></i> Đang học</span>";
                
                echo "<tr>
                        <td style='font-weight:700; font-size:15px'>{$r['name']}</td>
                        <td>".($r['full_name']??'--')."</td>
                        <td>$stt</td>
                      </tr>";
            }
            ?>
            </tbody>
        </table>
    </div>
</div></div>
</body></html>