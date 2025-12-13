<?php
include "connection.php"; include "auth.php"; requireRole(['teacher']);
$tid = $_SESSION['teacher_id'];

// Lấy danh sách lớp mà giáo viên phụ trách trước khi xử lý form
$teacherClasses = [];
$classMap = [];
$classesRes = mysqli_query($link, "SELECT id, name FROM classes WHERE teacher_id=$tid ORDER BY id DESC");
while($c = mysqli_fetch_assoc($classesRes)){
    $teacherClasses[] = $c;
    $classMap[$c['id']] = $c['name'];
}
$noClasses = empty($teacherClasses);

$errorMsg = '';

if(isset($_POST['add'])){
    $tit = trim($_POST['title'] ?? '');
    $sub = trim($_POST['sub'] ?? '');
    $date = trim($_POST['date'] ?? '');
    $cid = isset($_POST['cid']) ? intval($_POST['cid']) : 0;

    if(empty($teacherClasses)){
        $errorMsg = "Bạn chưa có lớp nào để tạo bài thi.";
    } elseif($cid === 0 || !array_key_exists($cid, $classMap)){
        $errorMsg = "Vui lòng chọn lớp hợp lệ.";
    } else {
        $tit = mysqli_real_escape_string($link, $tit);
        $sub = mysqli_real_escape_string($link, $sub);
        $date = mysqli_real_escape_string($link, $date);
        mysqli_query($link, "INSERT INTO exams (exam_title,subject,exam_date,class_id,teacher_id) VALUES ('$tit','$sub','$date',$cid,$tid)");
        header("Location: manage_exams.php");
        exit;
    }
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

        <?php if($errorMsg): ?>
            <div class="card" style="background:#FEF2F2; color:#B91C1C; border:1px solid #FECACA;">
                <?php echo $errorMsg; ?>
            </div>
        <?php endif; ?>
        
        <div class="card">
            <div class="card-header"><h3><i class="fa-solid fa-circle-plus" style="color:#F59E0B"></i> Tạo Bài Thi Mới</h3></div>
            <form method="post" style="display:grid; grid-template-columns: 1fr 1fr 1fr 1fr auto; gap:15px; align-items:end;">
                <div><label class="form-label">Tên bài thi</label><input type="text" name="title" class="form-control" required <?php echo $noClasses?'disabled':''; ?>></div>
                <div><label class="form-label">Môn học</label><input type="text" name="sub" class="form-control" required <?php echo $noClasses?'disabled':''; ?>></div>
                <div><label class="form-label">Ngày thi</label><input type="date" name="date" class="form-control" required <?php echo $noClasses?'disabled':''; ?>></div>
                <div><label class="form-label">Lớp áp dụng</label>
                    <select name="cid" class="form-control" <?php echo $noClasses?'disabled':''; ?>>
                        <?php if($noClasses): ?>
                            <option value="">Chưa có lớp nào</option>
                        <?php else: ?>
                            <?php foreach($teacherClasses as $c): ?>
                                <option value="<?php echo $c['id']; ?>"><?php echo $c['name']; ?></option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
                <button name="add" class="btn-primary" style="height:52px; opacity:<?php echo $noClasses?'0.5':'1'; ?>; cursor:<?php echo $noClasses?'not-allowed':'pointer'; ?>;" <?php echo $noClasses?'disabled':''; ?>>Tạo</button>
            </form>
            <?php if($noClasses): ?>
                <div style="margin-top:10px; color:#B45309; font-weight:700; font-size:14px;">Bạn chưa có lớp nào, hãy đợi admin giao lớp trước rồi quay lại tạo bài thi.</div>
            <?php endif; ?>
        </div>

        <div class="card">
            <div class="card-header"><h3>Danh Sách Bài Thi</h3></div>
            <table class="pretty-table">
                <thead><tr><th>Tên Bài</th><th>Môn</th><th>Lớp</th><th>Ngày Thi</th><th width="220">Bài thi & Điểm</th></tr></thead>
                <tbody>
                <?php $rs=mysqli_query($link, "SELECT e.*, c.name FROM exams e JOIN classes c ON e.class_id=c.id WHERE e.teacher_id=$tid ORDER BY e.id DESC");
                while($r=mysqli_fetch_assoc($rs)): ?>
                    <tr>
                        <td style="font-weight:700; color:#D97706"><?php echo $r['exam_title']; ?></td>
                        <td><?php echo $r['subject']; ?></td>
                        <td><span style="background:#ECFDF5; color:#059669; padding:5px 10px; border-radius:6px; font-size:13px; font-weight:700;"><?php echo $r['name']; ?></span></td>
                        <td><?php echo date('d/m/Y', strtotime($r['exam_date'])); ?></td>
                        <td>
                            <div style="display:flex; gap:8px; flex-wrap:wrap;">
                                <a href="enter_scores.php?eid=<?php echo $r['id']; ?>" class="btn-primary" style="padding:8px 14px; font-size:13px; box-shadow:none;">
                                    <i class="fa-solid fa-star"></i> Nhập Điểm
                                </a>
                                <a href="exam_scores.php?eid=<?php echo $r['id']; ?>" class="btn-primary" style="padding:8px 14px; font-size:13px; box-shadow:none; background:#E2E8F0; color:#0F172A; border:1px solid #CBD5E1;">
                                    Chi tiết
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>

    </div>
</div>
</body></html>