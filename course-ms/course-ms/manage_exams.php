<?php
include "connection.php"; include "auth.php"; requireRole(['teacher']);
$tid = $_SESSION['teacher_id'];

if(isset($_POST['add'])){
    $tit=$_POST['title']; $sub=$_POST['sub']; $date=$_POST['date']; $cid=intval($_POST['cid']);
    mysqli_query($link, "INSERT INTO exams (exam_title,subject,exam_date,class_id,teacher_id) VALUES ('$tit','$sub','$date',$cid,$tid)");
    header("Location: manage_exams.php");
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Quản Lý Thi</title>
    <link rel="stylesheet" href="https://site-assets.fontawesome.com/releases/v6.4.2/css/all.css">
    <link rel="stylesheet" href="dashboard_style.css">
</head>
<body>
<?php include "includes/sidebar.php"; ?>
<div class="main-wrapper">
    <?php include "includes/topbar.php"; ?>
    <div class="content-scroll">
        <div style="margin-bottom:16px;"><h2 class="page-title">Quản Lý Bài Kiểm Tra</h2></div>
        
        <div class="card">
            <div class="card-header"><h3><i class="fa-solid fa-circle-plus" style="color:#F59E0B"></i> Tạo Bài Thi Mới</h3></div>
            <form method="post" style="display:grid; grid-template-columns: 1fr 1fr 1fr 1fr auto; gap:15px; align-items:end;">
                <div><label class="form-label">Tên bài thi</label><input type="text" name="title" class="form-control" required></div>
                <div><label class="form-label">Môn học</label><input type="text" name="sub" class="form-control" required></div>
                <div><label class="form-label">Ngày thi</label><input type="date" name="date" class="form-control" required></div>
                <div><label class="form-label">Lớp áp dụng</label>
                    <select name="cid" class="form-control">
                        <?php $rs=mysqli_query($link, "SELECT * FROM classes WHERE teacher_id=$tid");
                        while($c=mysqli_fetch_assoc($rs)) echo "<option value='{$c['id']}'>{$c['name']}</option>"; ?>
                    </select>
                </div>
                <button name="add" class="btn-primary" style="height:52px;">Tạo</button>
            </form>
        </div>

        <div class="card">
            <div class="card-header"><h3>Danh Sách Bài Thi</h3></div>
            <table class="pretty-table">
                <thead><tr><th>Tên Bài</th><th>Môn</th><th>Lớp</th><th>Ngày Thi</th><th>Thao Tác</th></tr></thead>
                <tbody>
                <?php $rs=mysqli_query($link, "SELECT e.*, c.name FROM exams e JOIN classes c ON e.class_id=c.id WHERE e.teacher_id=$tid ORDER BY e.id DESC");
                while($r=mysqli_fetch_assoc($rs)): ?>
                    <tr>
                        <td style="font-weight:700; color:#D97706"><?php echo $r['exam_title']; ?></td>
                        <td><?php echo $r['subject']; ?></td>
                        <td><span style="background:#ECFDF5; color:#059669; padding:5px 10px; border-radius:6px; font-size:13px; font-weight:700;"><?php echo $r['name']; ?></span></td>
                        <td><?php echo date('d/m/Y', strtotime($r['exam_date'])); ?></td>
                        <td>
                            <a href="enter_scores.php?eid=<?php echo $r['id']; ?>" class="btn-primary" style="padding:8px 16px; font-size:13px; box-shadow:none;">
                                <i class="fa-solid fa-star"></i> Nhập Điểm
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