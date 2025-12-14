<?php
include "connection.php";
include "auth.php";
requireRole(['teacher']);

$tid = intval($_SESSION['teacher_id']);
$eid = isset($_GET['eid']) ? intval($_GET['eid']) : 0;
$exam = null;
$students = [];
$totalStudents = 0;
$filled = 0;
$missing = 0;

if($eid > 0){
    $exam = mysqli_fetch_assoc(mysqli_query($link, "SELECT e.*, c.name AS class_name FROM exams e JOIN classes c ON e.class_id=c.id WHERE e.id=$eid AND e.teacher_id=$tid"));
    if($exam){
        $stuRs = mysqli_query($link, "SELECT s.id, s.student_code, u.full_name, sc.score FROM students s JOIN users u ON s.user_id=u.id LEFT JOIN scores sc ON sc.student_id=s.id AND sc.exam_id=$eid WHERE s.class_id=".$exam['class_id']." ORDER BY u.full_name");
        while($row = mysqli_fetch_assoc($stuRs)){
            $students[] = $row;
        }
        $totalStudents = count($students);
        foreach($students as $st){
            if($st['score'] === null || $st['score'] === ''){
                $missing++;
            } else {
                $filled++;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<link rel="stylesheet" href="https://site-assets.fontawesome.com/releases/v6.4.2/css/all.css">
<head>
    <title>Chi tiết điểm</title>
    <link rel="stylesheet" href="dashboard_style.css">
</head>
<body>
<?php include "includes/sidebar.php"; ?>
<div class="main-wrapper">
    <?php include "includes/topbar.php"; ?>
    <div class="content-scroll">
        <div style="margin-bottom:12px; display:flex; align-items:center; gap:10px;">
            <a href="<?php echo $exam ? 'teacher_class_detail.php?class_id='.$exam['class_id'] : 'teacher_classes.php'; ?>" style="display:inline-flex; align-items:center; gap:6px; color:#0F172A; text-decoration:none; font-weight:700;">
                <i class="fa-solid fa-arrow-left"></i> Quay lại lớp
            </a>
        </div>

        <?php if(!$exam): ?>
            <div class="card" style="padding:16px; color:#B91C1C; background:#FEF2F2; border:1px solid #FECACA;">Bài kiểm tra không tồn tại hoặc không thuộc quyền của bạn.</div>
        <?php else: ?>
            <div class="card" style="display:flex; justify-content:space-between; align-items:center; gap:12px;">
                <div>
                    <div style="font-size:22px; font-weight:900; color:#0F172A;"><?php echo htmlspecialchars($exam['exam_title']); ?></div>
                    <div style="color:#64748B; font-size:13px;">Môn: <?php echo htmlspecialchars($exam['subject']); ?> · Lớp: <?php echo htmlspecialchars($exam['class_name']); ?></div>
                    <div style="color:#0F172A; font-weight:700; margin-top:6px;">Tổng: <?php echo $totalStudents; ?> · Đã nhập: <?php echo $filled; ?> · Chưa nhập: <?php echo $missing; ?></div>
                </div>
                <div style="display:flex; gap:8px;">
                    <a href="enter_scores.php?eid=<?php echo $eid; ?>" class="btn-primary" style="padding:10px 14px;">Nhập điểm</a>
                </div>
            </div>

            <div class="card" style="margin-top:14px;">
                <?php if($totalStudents === 0): ?>
                    <div style="padding:14px; color:#94A3B8;">Chưa có học sinh trong lớp.</div>
                <?php else: ?>
                    <table class="pretty-table" style="margin:0;">
                        <thead><tr><th>Mã SV</th><th>Họ tên</th><th>Điểm</th><th>Trạng thái</th></tr></thead>
                        <tbody>
                            <?php foreach($students as $st): ?>
                                <?php $hasScore = !($st['score'] === null || $st['score'] === ''); ?>
                                <tr>
                                    <td style="font-family:monospace; color:#64748B; font-weight:700;">#<?php echo $st['student_code']; ?></td>
                                    <td><?php echo $st['full_name']; ?></td>
                                    <td style="font-weight:700; color:<?php echo $hasScore ? '#0F172A' : '#94A3B8'; ?>;">
                                        <?php echo $hasScore ? $st['score'] : 'Chưa nhập'; ?>
                                    </td>
                                    <td>
                                        <?php if($hasScore): ?>
                                            <span style="background:#ECFDF3; color:#15803D; padding:6px 10px; border-radius:8px; font-weight:700; font-size:12px;">Đã nhập</span>
                                        <?php else: ?>
                                            <span style="background:#FEF9C3; color:#B45309; padding:6px 10px; border-radius:8px; font-weight:700; font-size:12px;">Chưa nhập</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
