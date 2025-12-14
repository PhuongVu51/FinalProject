<?php
include "connection.php";
include "auth.php";
requireRole(['teacher']);

$tid = intval($_SESSION['teacher_id']);
$classId = isset($_GET['class_id']) ? intval($_GET['class_id']) : 0;
$class = null;
$studentsRs = null;
$examsRs = null;
$examsCount = 0;

if($classId > 0){
    $class = mysqli_fetch_assoc(mysqli_query($link, "SELECT c.*, (SELECT COUNT(*) FROM students s WHERE s.class_id=c.id) AS student_count FROM classes c WHERE c.id=$classId AND c.teacher_id=$tid"));
    if($class){
        $studentsRs = mysqli_query($link, "SELECT s.student_code, u.full_name FROM students s JOIN users u ON s.user_id=u.id WHERE s.class_id=$classId ORDER BY u.full_name");
        $examRow = mysqli_fetch_assoc(mysqli_query($link, "SELECT COUNT(*) as c FROM exams WHERE class_id=$classId"));
        $examsCount = $examRow['c'] ?? 0;
        $examsRs = mysqli_query($link, "SELECT id, exam_title, subject, exam_date FROM exams WHERE class_id=$classId ORDER BY exam_date DESC");
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<link rel="stylesheet" href="https://site-assets.fontawesome.com/releases/v6.4.2/css/all.css">
<head>
    <title>Lớp học | Chi tiết</title>
    <link rel="stylesheet" href="dashboard_style.css">
</head>
<body>
<?php include "includes/sidebar.php"; ?>
<div class="main-wrapper">
    <?php include "includes/topbar.php"; ?>
    <div class="content-scroll">
        <div style="margin-bottom:12px; display:flex; align-items:center;">
            <a href="teacher_classes.php" style="display:inline-flex; align-items:center; gap:6px; color:#0F172A; text-decoration:none; font-weight:700;">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
        </div>
        <?php if(!$class): ?>
            <div class="card" style="padding:16px; color:#B91C1C; background:#FEF2F2; border:1px solid #FECACA;">Lớp không tồn tại hoặc không thuộc quyền của bạn.</div>
        <?php else: ?>
            <div class="card" style="margin-bottom:20px; display:flex; align-items:center; justify-content:space-between;">
                <div>
                    <div style="font-size:22px; font-weight:900; color:#0F172A;">Lớp: <?php echo htmlspecialchars($class['name']); ?></div>
                    <div style="color:#64748B; font-size:13px;">Có <?php echo $class['student_count']; ?> học sinh · <?php echo $examsCount; ?> bài kiểm tra</div>
                </div>
            </div>

            <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(360px, 1fr)); gap:16px;">
                <div class="card" style="margin:0;">
                    <div class="card-header" style="align-items:center;">
                        <h3 style="margin:0;">Danh sách học sinh</h3>
                    </div>
                    <?php if(!$studentsRs || mysqli_num_rows($studentsRs)==0): ?>
                        <div style="padding:14px; color:#94A3B8;">Chưa có học sinh.</div>
                    <?php else: ?>
                        <table class="pretty-table" style="margin:0;">
                            <thead><tr><th>Mã SV</th><th>Họ tên</th></tr></thead>
                            <tbody>
                                <?php while($stu = mysqli_fetch_assoc($studentsRs)): ?>
                                    <tr>
                                        <td style="font-family:monospace; color:#64748B; font-weight:700;">#<?php echo $stu['student_code']; ?></td>
                                        <td><?php echo $stu['full_name']; ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>

                <div class="card" style="margin:0;">
                    <div class="card-header" style="align-items:center; display:flex; justify-content:space-between;">
                        <h3 style="margin:0;">Danh sách bài kiểm tra</h3>
                        <a href="manage_exams.php?cid=<?php echo $classId; ?>#create" class="btn-primary" style="padding:10px 14px;">+ Tạo bài kiểm tra</a>
                    </div>
                    <?php if(!$examsRs || mysqli_num_rows($examsRs)==0): ?>
                        <div style="padding:14px; color:#94A3B8;">Chưa có bài kiểm tra.</div>
                    <?php else: ?>
                        <table class="pretty-table" style="margin:0;">
                            <thead><tr><th>Tên bài</th><th>Môn</th><th>Ngày thi</th><th width="140">Thao tác</th></tr></thead>
                            <tbody>
                                <?php while($ex = mysqli_fetch_assoc($examsRs)): ?>
                                    <tr>
                                        <td style="font-weight:700; color:#D97706;"><?php echo $ex['exam_title']; ?></td>
                                        <td><?php echo $ex['subject']; ?></td>
                                        <td><?php echo date('d/m/Y', strtotime($ex['exam_date'])); ?></td>
                                        <td>
                                            <a href="enter_scores.php?eid=<?php echo $ex['id']; ?>" class="btn-primary" style="padding:6px 10px; font-size:12px; box-shadow:none;">Nhập điểm</a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
