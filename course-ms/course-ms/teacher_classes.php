<?php
include "connection.php";
include "auth.php";
requireRole(['teacher']);

$tid = intval($_SESSION['teacher_id']);
$classes = [];

$q = "SELECT c.*, 
            (SELECT COUNT(*) FROM students s WHERE s.class_id=c.id) as student_count,
            (SELECT COUNT(*) FROM exams e WHERE e.class_id=c.id) as exam_count
      FROM classes c WHERE c.teacher_id=$tid ORDER BY c.id DESC";
$rs = mysqli_query($link, $q);
while($row = mysqli_fetch_assoc($rs)) $classes[] = $row;

?>
<!DOCTYPE html>
<html lang="en">
<link rel="stylesheet" href="https://site-assets.fontawesome.com/releases/v6.4.2/css/all.css">
<head>
    <title>Lớp Học | Teacher</title>
    <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"> -->
    <link rel="stylesheet" href="dashboard_style.css">
</head>
<body>
<?php include "includes/sidebar.php"; ?>
<div class="main-wrapper">
    <?php include "includes/topbar.php"; ?>
    <div class="content-scroll">
        <div class="card" style="background:transparent; box-shadow:none; border:none; padding:0;">
            <div class="card-header" style="padding:0 0 12px 0;">
                <h3 class="card-title">Lớp Học Phụ Trách</h3>
            </div>

            <?php if(empty($classes)): ?>
                <div class="card" style="padding:18px; text-align:center; color:#94A3B8;">Bạn chưa được gán lớp nào.</div>
            <?php else: ?>
                <div style="display:grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap:16px;">
                    <?php foreach($classes as $c): ?>
                        <div class="card" style="padding:0; overflow:hidden; border:1px solid #F1F5F9;">
                            <div style="background:linear-gradient(120deg, #F59E0B, #FBBF24); color:#fff; padding:12px 16px; font-weight:800; font-size:16px; letter-spacing:-0.2px;">
                                <?php echo htmlspecialchars($c['name']); ?>
                            </div>
                            <div style="padding:14px 16px; display:flex; flex-direction:column; gap:10px;">
                                <div style="display:flex; justify-content:flex-start; align-items:center; gap:10px;">
                                    <span style="color:#0F172A; font-weight:700;"><i class="fa-solid fa-user-group" style="margin-right:6px; color:#475569;"></i><?php echo $c['student_count']; ?> HS</span>
                                </div>
                                <div style="color:#475569; font-size:14px;">Có <?php echo $c['exam_count']; ?> bài kiểm tra.</div>
                                <div style="display:flex; gap:10px; align-items:center;">
                                    <a href="teacher_class_detail.php?class_id=<?php echo $c['id']; ?>" class="btn-primary" style="flex:1; justify-content:center; box-shadow:none;">Vào lớp</a>
                                    <a href="manage_exams.php?cid=<?php echo $c['id']; ?>#create" title="Tạo bài kiểm tra" style="width:38px; height:38px; border-radius:12px; border:1px solid #E2E8F0; display:grid; place-items:center; color:#F59E0B; text-decoration:none;">
                                        <i class="fa-solid fa-file-pen"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        
    </div>
</div>
</body>
</html>
