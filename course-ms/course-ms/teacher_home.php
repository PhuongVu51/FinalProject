<?php
include "connection.php";
include "auth.php";
requireRole(['teacher']);

$tid = intval($_SESSION['teacher_id']);

$teacherClasses = [];
$activeClassId = 0;
$activeClass = null;
$studentsRs = null;
$examsRs = null;

// Lấy danh sách lớp giáo viên phụ trách
$rs = mysqli_query($link, "SELECT c.*, 
            (SELECT COUNT(*) FROM students s WHERE s.class_id=c.id) as student_count,
            (SELECT COUNT(*) FROM exams e WHERE e.class_id=c.id) as exam_count
        FROM classes c WHERE c.teacher_id=$tid ORDER BY c.id DESC");
while($row = mysqli_fetch_assoc($rs)) $teacherClasses[] = $row;

if(!empty($teacherClasses)){
    $activeClassId = isset($_GET['class_id']) ? intval($_GET['class_id']) : $teacherClasses[0]['id'];
    foreach($teacherClasses as $cls){ if($cls['id'] == $activeClassId){ $activeClass = $cls; break; } }
    if($activeClass){
        $studentsRs = mysqli_query($link, "SELECT s.student_code, u.full_name, u.username FROM students s JOIN users u ON s.user_id=u.id WHERE s.class_id=$activeClassId ORDER BY u.full_name");
        $examsRs = mysqli_query($link, "SELECT id, exam_title, subject, exam_date FROM exams WHERE class_id=$activeClassId ORDER BY exam_date DESC");
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<link rel="stylesheet" href="https://site-assets.fontawesome.com/releases/v6.4.2/css/all.css">
<head>
    <title>Dashboard | Teacher</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="dashboard_style.css">
</head>
<body>
    <?php include "includes/sidebar.php"; ?>
    <div class="main-wrapper">
        <?php include "includes/topbar.php"; ?>
        <div class="content-scroll">
            <div class="hero-banner" style="margin-bottom:24px;">
                <i class="fa-solid fa-bee hero-icon"></i>
                <h1>Xin chào, <?php echo $_SESSION['full_name']; ?>! 👋</h1>
            </div>

            <div class="card">
                <div class="card-header" style="align-items:center;">
                    <h3 style="margin:0;">Lớp học bạn phụ trách</h3>
                    <div style="color:#64748B; font-size:14px;">Tổng: <?php echo count($teacherClasses); ?> lớp</div>
                </div>
                <?php if(empty($teacherClasses)): ?>
                    <div style="padding:16px; color:#94A3B8;">Bạn chưa được gán lớp nào.</div>
                <?php else: ?>
                    <div style="display:grid; grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap:16px;">
                        <?php foreach($teacherClasses as $cls): ?>
                            <a href="teacher_home.php?class_id=<?php echo $cls['id']; ?>" class="card" style="margin:0; border:1px solid <?php echo ($cls['id']==$activeClassId)?'#F59E0B':'#E2E8F0'; ?>; box-shadow:none; text-decoration:none; color:inherit;">
                                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:6px;">
                                    <span style="font-weight:700; color:#B45309; font-size:16px;"><?php echo $cls['name']; ?></span>
                                    <?php if($cls['id']==$activeClassId): ?><span class="badge badge-active">Đang xem</span><?php endif; ?>
                                </div>
                                <div style="display:flex; gap:10px; color:#64748B; font-size:13px;">
                                    <span><i class="fa-solid fa-users"></i> <?php echo $cls['student_count']; ?> HS</span>
                                    <span><i class="fa-solid fa-file-pen"></i> <?php echo $cls['exam_count']; ?> bài kiểm tra</span>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <?php if($activeClass): ?>
            <div style="display:grid; grid-template-columns: 2fr 1fr; gap:24px;">
                <div class="card">
                    <div class="card-header" style="align-items:center;">
                        <div>
                            <div style="font-size:18px; font-weight:800; color:#0F172A;">Thông tin lớp: <?php echo $activeClass['name']; ?></div>
                            <div style="color:#64748B; font-size:13px;"><?php echo $activeClass['student_count']; ?> học sinh · <?php echo $activeClass['exam_count']; ?> bài kiểm tra</div>
                        </div>
                        <div style="display:flex; gap:10px;">
                            <a href="manage_exams.php?cid=<?php echo $activeClassId; ?>#create" class="btn-primary" style="background:#F59E0B; border:none;">+ Tạo bài kiểm tra</a>
                            <a href="manage_exams.php" class="btn-secondary">Danh sách bài thi</a>
                        </div>
                    </div>
                    <div style="display:grid; grid-template-columns: 1fr 1fr; gap:16px;">
                        <div class="card" style="margin:0;">
                            <div class="card-header"><h3 style="margin:0;">Danh sách học sinh</h3></div>
                            <?php if(mysqli_num_rows($studentsRs)==0): ?>
                                <div style="padding:12px; color:#94A3B8;">Chưa có học sinh.</div>
                            <?php else: ?>
                                <table class="pretty-table">
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
                            <div class="card-header"><h3 style="margin:0;">Bài kiểm tra</h3></div>
                            <?php if(mysqli_num_rows($examsRs)==0): ?>
                                <div style="padding:12px; color:#94A3B8;">Chưa có bài kiểm tra.</div>
                            <?php else: ?>
                                <table class="pretty-table">
                                    <thead><tr><th>Tên bài</th><th>Ngày thi</th><th></th></tr></thead>
                                    <tbody>
                                        <?php while($ex = mysqli_fetch_assoc($examsRs)): ?>
                                            <tr>
                                                <td style="font-weight:700; color:#D97706;"><?php echo $ex['exam_title']; ?></td>
                                                <td><?php echo date('d/m/Y', strtotime($ex['exam_date'])); ?></td>
                                                <td style="text-align:right;">
                                                    <a href="enter_scores.php?eid=<?php echo $ex['id']; ?>" class="btn-primary" style="padding:6px 12px; font-size:12px; box-shadow:none;">Nhập điểm</a>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header"><h3 style="margin:0;">Tài liệu lớp học</h3></div>
                    <div style="color:#94A3B8; font-size:14px;">Chưa có tài liệu. Bạn có thể tải lên mục này sau.</div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
