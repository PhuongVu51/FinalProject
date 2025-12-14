<?php
include "connection.php";
include "auth.php";
requireRole(['admin','teacher','student']);

$role = $_SESSION['role'];
$teacherId = $_SESSION['teacher_id'] ?? 0;
$studentId = $_SESSION['student_id'] ?? 0;

$newTeachers = [];
$applications = [];
$teacherClasses = [];
$latestNews = [];

// Admin: newest teachers
if($role === 'admin'){
    $newTeachersRes = mysqli_query($link, "SELECT full_name, created_at FROM users WHERE role='teacher' ORDER BY created_at DESC LIMIT 5");
    if($newTeachersRes){
        while($r = mysqli_fetch_assoc($newTeachersRes)) $newTeachers[] = $r;
    }
}

// Applications for admin or student
if($role === 'admin'){
    $appRes = mysqli_query($link, "SELECT a.status, a.applied_at,
                                    COALESCE(u.full_name, '') AS student_name,
                                    c.name AS class_name
                                    FROM applications a
                                    JOIN students s ON a.student_id = s.id
                                    LEFT JOIN users u ON s.user_id = u.id
                                    JOIN classes c ON a.class_id = c.id
                                    ORDER BY a.applied_at DESC LIMIT 6");
    if($appRes){
        while($r = mysqli_fetch_assoc($appRes)) $applications[] = $r;
    }
} elseif($role === 'student' && $studentId){
    $appRes = mysqli_query($link, "SELECT a.status, a.applied_at, c.name AS class_name
                                    FROM applications a
                                    JOIN classes c ON a.class_id = c.id
                                    WHERE a.student_id = $studentId
                                    ORDER BY a.applied_at DESC LIMIT 6");
    if($appRes){
        while($r = mysqli_fetch_assoc($appRes)) $applications[] = $r;
    }
}

// Teacher assignments
if($role === 'teacher' && $teacherId){
    $clsRes = mysqli_query($link, "SELECT id, name FROM classes WHERE teacher_id=$teacherId ORDER BY id DESC");
    if($clsRes){
        while($r = mysqli_fetch_assoc($clsRes)) $teacherClasses[] = $r;
    }
}

// Latest news (for teacher & student)
if($role === 'teacher' || $role === 'student' || $role === 'admin'){
    $newsRes = mysqli_query($link, "SELECT id, title, created_at FROM news ORDER BY created_at DESC LIMIT 4");
    if($newsRes){
        while($r = mysqli_fetch_assoc($newsRes)) $latestNews[] = $r;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Thông báo</title>
    <link rel="stylesheet" href="https://site-assets.fontawesome.com/releases/v6.4.2/css/all.css">
    <link rel="stylesheet" href="dashboard_style.css">
</head>
<body>
<?php include "includes/sidebar.php"; ?>
<div class="main-wrapper">
    <?php include "includes/topbar.php"; ?>
    <div class="content-scroll">
        <div class="card" style="margin-bottom:20px;">
            <div class="card-header" style="display:flex; align-items:center; gap:10px;">
                <i class="fa-solid fa-bell" style="color:#F59E0B;"></i>
                <h3 class="card-title" style="margin:0;">Thông báo</h3>
            </div>
        </div>

        <?php if($role === 'admin'): ?>
        <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap:16px;">
            <div class="card" style="margin:0;">
                <h4 style="margin:0 0 12px 0; display:flex; align-items:center; gap:8px; color:#0F172A;">
                    <i class="fa-solid fa-user-plus" style="color:#F59E0B;"></i> Giáo viên mới đăng ký
                </h4>
                <?php if(empty($newTeachers)): ?>
                    <div style="color:#94A3B8;">Chưa có đăng ký mới.</div>
                <?php else: ?>
                    <ul style="list-style:none; padding:0; margin:0; display:flex; flex-direction:column; gap:10px;">
                        <?php foreach($newTeachers as $t): ?>
                        <li style="padding:10px 12px; border:1px solid #E2E8F0; border-radius:10px;">
                            <div style="font-weight:800; color:#0F172A;">Có giáo viên <?php echo htmlspecialchars($t['full_name']); ?> mới đăng ký</div>
                            <div style="color:#94A3B8; font-size:12px; display:flex; align-items:center; gap:6px;">
                                <i class="fa-regular fa-clock"></i> <?php echo date('d/m/Y H:i', strtotime($t['created_at'])); ?>
                            </div>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>

            <div class="card" style="margin:0;">
                <h4 style="margin:0 0 12px 0; display:flex; align-items:center; gap:8px; color:#0F172A;">
                    <i class="fa-solid fa-file-signature" style="color:#F59E0B;"></i> Yêu cầu tham gia lớp
                </h4>
                <?php if(empty($applications)): ?>
                    <div style="color:#94A3B8;">Chưa có yêu cầu.</div>
                <?php else: ?>
                    <ul style="list-style:none; padding:0; margin:0; display:flex; flex-direction:column; gap:10px;">
                        <?php foreach($applications as $a): ?>
                        <li>
                            <a href="manage_applications.php" style="padding:10px 12px; border:1px solid #E2E8F0; border-radius:10px; text-decoration:none; color:inherit; display:flex; justify-content:space-between; align-items:center; gap:12px;">
                                <div>
                                    <div style="font-weight:800; color:#0F172A;">Học sinh <?php echo htmlspecialchars($a['student_name']); ?> đăng ký lớp <?php echo htmlspecialchars($a['class_name']); ?> (<?php echo htmlspecialchars($a['status']); ?>)</div>
                                    <div style="color:#94A3B8; font-size:12px; display:flex; align-items:center; gap:6px;">
                                        <i class="fa-regular fa-clock"></i> <?php echo date('d/m/Y H:i', strtotime($a['applied_at'])); ?>
                                    </div>
                                </div>
                                <span style="font-size:12px; color:#F59E0B; white-space:nowrap;">Xem</span>
                            </a>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

        <?php if($role === 'teacher'): ?>
        <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap:16px;">
            <div class="card" style="margin:0;">
                <h4 style="margin:0 0 12px 0; display:flex; align-items:center; gap:8px; color:#0F172A;">
                    <i class="fa-solid fa-layer-group" style="color:#F59E0B;"></i> Lớp được phân công
                </h4>
                <?php if(empty($teacherClasses)): ?>
                    <div style="color:#94A3B8;">Bạn chưa được phân lớp.</div>
                <?php else: ?>
                    <ul style="list-style:none; padding:0; margin:0; display:flex; flex-direction:column; gap:10px;">
                        <?php foreach($teacherClasses as $c): ?>
                        <li style="padding:10px 12px; border:1px solid #E2E8F0; border-radius:10px; display:flex; justify-content:space-between; align-items:center;">
                            <span style="font-weight:800; color:#0F172A;">Bạn vừa được phân vào lớp <?php echo htmlspecialchars($c['name']); ?></span>
                            <a href="teacher_classes.php" style="font-size:12px; color:#F59E0B; text-decoration:none;">Xem</a>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>

            <div class="card" style="margin:0;">
                <h4 style="margin:0 0 12px 0; display:flex; align-items:center; gap:8px; color:#0F172A;">
                    <i class="fa-regular fa-newspaper" style="color:#F59E0B;"></i> Tin tức mới
                </h4>
                <?php if(empty($latestNews)): ?>
                    <div style="color:#94A3B8;">Chưa có tin tức.</div>
                <?php else: ?>
                    <ul style="list-style:none; padding:0; margin:0; display:flex; flex-direction:column; gap:10px;">
                        <?php foreach($latestNews as $n): ?>
                        <li style="padding:10px 12px; border:1px solid #E2E8F0; border-radius:10px;">
                            <a href="news_detail.php?id=<?php echo intval($n['id']); ?>" style="text-decoration:none; color:#0F172A; font-weight:800; display:block; margin-bottom:4px;">
                                <?php echo htmlspecialchars($n['title']); ?>
                            </a>
                            <div style="color:#94A3B8; font-size:12px; display:flex; align-items:center; gap:6px;">
                                <i class="fa-regular fa-clock"></i> <?php echo date('d/m/Y H:i', strtotime($n['created_at'])); ?>
                            </div>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

        <?php if($role === 'student'): ?>
        <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap:16px;">
            <div class="card" style="margin:0;">
                <h4 style="margin:0 0 12px 0; display:flex; align-items:center; gap:8px; color:#0F172A;">
                    <i class="fa-solid fa-user-check" style="color:#F59E0B;"></i> Trạng thái yêu cầu lớp
                </h4>
                <?php if(empty($applications)): ?>
                    <div style="color:#94A3B8;">Chưa có yêu cầu.</div>
                <?php else: ?>
                    <ul style="list-style:none; padding:0; margin:0; display:flex; flex-direction:column; gap:10px;">
                        <?php foreach($applications as $a): ?>
                        <li style="padding:10px 12px; border:1px solid #E2E8F0; border-radius:10px; display:flex; justify-content:space-between; align-items:center; gap:12px;">
                            <div>
                                <div style="font-weight:800; color:#0F172A;">Bạn vừa được tham gia lớp <?php echo htmlspecialchars($a['class_name']); ?><?php echo $a['status']==='rejected' ? ' (bị từ chối)' : ''; ?></div>
                                <div style="color:#94A3B8; font-size:12px; display:flex; align-items:center; gap:6px;">
                                    <i class="fa-regular fa-clock"></i> <?php echo date('d/m/Y H:i', strtotime($a['applied_at'])); ?>
                                </div>
                            </div>
                            <a href="student_classes.php" style="font-size:12px; color:#F59E0B; text-decoration:none; white-space:nowrap;">Xem</a>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>

            <div class="card" style="margin:0;">
                <h4 style="margin:0 0 12px 0; display:flex; align-items:center; gap:8px; color:#0F172A;">
                    <i class="fa-regular fa-newspaper" style="color:#F59E0B;"></i> Tin tức mới
                </h4>
                <?php if(empty($latestNews)): ?>
                    <div style="color:#94A3B8;">Chưa có tin tức.</div>
                <?php else: ?>
                    <ul style="list-style:none; padding:0; margin:0; display:flex; flex-direction:column; gap:10px;">
                        <?php foreach($latestNews as $n): ?>
                        <li style="padding:10px 12px; border:1px solid #E2E8F0; border-radius:10px;">
                            <a href="news_detail.php?id=<?php echo intval($n['id']); ?>" style="text-decoration:none; color:#0F172A; font-weight:800; display:block; margin-bottom:4px;">
                                <?php echo htmlspecialchars($n['title']); ?>
                            </a>
                            <div style="color:#94A3B8; font-size:12px; display:flex; align-items:center; gap:6px;">
                                <i class="fa-regular fa-clock"></i> <?php echo date('d/m/Y H:i', strtotime($n['created_at'])); ?>
                            </div>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

    </div>
</div>
</body>
</html>