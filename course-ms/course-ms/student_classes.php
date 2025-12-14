<?php
include "connection.php";
include "auth.php";
requireRole(['student']);
$sid = $_SESSION['student_id'];

if(isset($_GET['reg'])){
    $cid = intval($_GET['reg']);
    mysqli_query($link, "INSERT INTO applications (student_id,class_id,status) VALUES ($sid,$cid,'pending')");
    header("Location: student_classes.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Lớp Học</title>
    <link rel="stylesheet" href="https://site-assets.fontawesome.com/releases/v6.4.2/css/all.css">
    <link rel="stylesheet" href="dashboard_style.css">
    <style>
        .search-bar {
            background: white;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 24px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .search-bar .search-icon {
            color: #999;
            font-size: 20px;
            flex-shrink: 0;
        }
        .search-bar input {
            flex: 1;
            padding: 14px 20px;
            border: 2px solid #E0E0E0;
            border-radius: 10px;
            font-size: 15px;
            transition: all 0.3s;
        }
        .search-bar input:focus {
            outline: none;
            border-color: #FFC107;
            box-shadow: 0 0 0 3px rgba(255, 193, 7, 0.1);
        }
        .search-bar input::placeholder {
            color: #999;
        }
    </style>
</head>
<body>
    <?php include "includes/sidebar.php"; ?>
    <div class="main-wrapper">
        <?php include "includes/student_topbar.php"; ?>
        <div class="content-scroll">
            
            <!-- Thanh tìm kiếm -->
            <div class="search-bar">
                <i class="fa-solid fa-magnifying-glass search-icon"></i>
                <input type="text" id="searchInput" placeholder="Tìm kiếm lớp học theo tên hoặc giáo viên..." onkeyup="searchClasses()">
            </div>

            <!-- Lớp Đang Học -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title" style="display:flex; align-items:center; gap:10px;">
                        <i class="fa-solid fa-chalkboard-user" style="color:#F59E0B;"></i> Lớp Học Đã Tham Gia
                    </h3>
                </div>
                <div style="padding:14px;">
                <?php 
                $enrolled = mysqli_query($link, "SELECT c.*, u.full_name as teacher_name
                    FROM classes c 
                    LEFT JOIN teachers t ON c.teacher_id=t.id 
                    LEFT JOIN users u ON t.user_id=u.id
                    INNER JOIN student_classes sc ON c.id=sc.class_id
                    WHERE sc.student_id=$sid
                    ORDER BY sc.enrolled_at DESC");
                if(mysqli_num_rows($enrolled)==0): ?>
                    <div style="padding:20px; color:#94A3B8; text-align:center;">Bạn chưa tham gia lớp học nào.</div>
                <?php else: 
                    while($class = mysqli_fetch_assoc($enrolled)):
                ?>
                    <div class="searchable-class" style="margin-bottom:16px; padding:16px; background:#F8FAFC; border-radius:8px; border-left:4px solid #10B981;">
                        <div style="display:flex; justify-content:space-between; align-items:start; margin-bottom:8px;">
                            <div>
                                <div style="font-size:17px; font-weight:700; color:#0F172A;">
                                    <?php echo htmlspecialchars($class['name']); ?>
                                </div>
                                <div style="color:#64748B; font-size:14px; margin-top:4px;">
                                    <i class="fa-solid fa-user-tie"></i> <?php echo htmlspecialchars($class['teacher_name'] ?? 'Chưa phân công'); ?>
                                </div>
                            </div>
                            <span style="background:#E8F5E9; color:#2E7D32; padding:5px 12px; border-radius:6px; font-size:12px; font-weight:600; white-space:nowrap;">
                                <i class="fa-solid fa-check"></i> Đang học
                            </span>
                        </div>
                        
                        <!-- Exams List -->
                        <div style="margin-top:12px; padding-top:12px; border-top:1px solid #E2E8F0;">
                            <div style="font-weight:600; font-size:13px; margin-bottom:8px; color:#64748B;">📝 Bài kiểm tra</div>
                            <?php
                            $exams = mysqli_query($link, "SELECT e.*, s.score, s.id as score_id
                                FROM exams e
                                LEFT JOIN scores s ON e.id=s.exam_id AND s.student_id=$sid
                                WHERE e.class_id={$class['id']}
                                ORDER BY e.exam_date DESC");
                            if(mysqli_num_rows($exams) > 0):
                                while($exam = mysqli_fetch_assoc($exams)):
                            ?>
                                <div style="padding:8px 0; display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid #E2E8F0;">
                                    <div style="flex:1;">
                                        <div style="font-weight:600; color:#334155; font-size:14px;"><?php echo htmlspecialchars($exam['exam_title']); ?></div>
                                        <div style="color:#94A3B8; font-size:12px; margin-top:2px;">
                                            <?php echo htmlspecialchars($exam['subject']); ?> • <?php echo date('d/m/Y', strtotime($exam['exam_date'])); ?>
                                        </div>
                                    </div>
                                    <?php if($exam['score_id']): 
                                        $color = $exam['score'] >= 8 ? '#10B981' : ($exam['score'] >= 5 ? '#F59E0B' : '#EF4444');
                                    ?>
                                        <span style="font-size:20px; font-weight:700; color:<?php echo $color; ?>; margin-left:12px;"><?php echo $exam['score']; ?></span>
                                    <?php else: ?>
                                        <span style="color:#94A3B8; font-size:12px; margin-left:12px;">Chưa có điểm</span>
                                    <?php endif; ?>
                                </div>
                            <?php endwhile; else: ?>
                                <div style="padding:8px 0; color:#94A3B8; font-size:13px;">Chưa có bài kiểm tra</div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endwhile; endif; ?>
                </div>
            </div>

            <!-- Lớp Có Thể Đăng Ký -->
            <div class="card" style="margin-top:24px;">
                <div class="card-header">
                    <h3 class="card-title" style="display:flex; align-items:center; gap:10px;">
                        <i class="fa-solid fa-plus-circle" style="color:#F59E0B;"></i> Lớp Học Có Thể Đăng Ký
                    </h3>
                </div>
                <div style="padding:14px;">
                <?php 
                // FIXED: Exclude classes already enrolled via student_classes table
                $available = mysqli_query($link, "SELECT c.*, u.full_name as teacher_name,
                    (SELECT status FROM applications WHERE student_id=$sid AND class_id=c.id) as app_status
                    FROM classes c 
                    LEFT JOIN teachers t ON c.teacher_id=t.id 
                    LEFT JOIN users u ON t.user_id=u.id
                    WHERE c.id NOT IN (SELECT class_id FROM student_classes WHERE student_id=$sid)
                    ORDER BY c.name ASC");
                if(mysqli_num_rows($available)==0): ?>
                    <div style="padding:20px; color:#94A3B8; text-align:center;">Không có lớp học khả dụng.</div>
                <?php else: 
                    while($class = mysqli_fetch_assoc($available)):
                ?>
                    <div class="searchable-class" style="margin-bottom:16px; padding:16px; background:white; border-radius:8px; border:1px solid #E2E8F0;">
                        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
                            <div style="flex:1;">
                                <div style="font-size:17px; font-weight:700; color:#0F172A;">
                                    <?php echo htmlspecialchars($class['name']); ?>
                                </div>
                                <div style="color:#64748B; font-size:14px; margin-top:4px;">
                                    <i class="fa-solid fa-user-tie"></i> <?php echo htmlspecialchars($class['teacher_name'] ?? 'Chưa phân công'); ?>
                                </div>
                            </div>
                            <?php if($class['app_status'] == 'pending'): ?>
                                <span style="background:#FFF3E0; color:#F57C00; padding:5px 12px; border-radius:6px; font-size:12px; font-weight:600; white-space:nowrap;">
                                    <i class="fa-regular fa-clock"></i> Chờ duyệt
                                </span>
                            <?php else: ?>
                                <a href="?reg=<?php echo $class['id']; ?>" 
                                   onclick="return confirm('Bạn có chắc muốn đăng ký lớp này?');"
                                   style="display:inline-block; padding:9px 20px; background:#FDB022; color:#1F2937; 
                                          border-radius:6px; font-weight:600; font-size:14px; text-decoration:none; transition:all 0.2s; white-space:nowrap;">
                                    <i class="fa-solid fa-paper-plane"></i> Đăng Ký Ngay
                                </a>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Exams Preview -->
                        <div style="margin-top:12px; padding-top:12px; border-top:1px solid #E2E8F0;">
                            <?php
                            $exams = mysqli_query($link, "SELECT * FROM exams WHERE class_id={$class['id']} ORDER BY exam_date DESC LIMIT 3");
                            if(mysqli_num_rows($exams) > 0):
                                while($exam = mysqli_fetch_assoc($exams)):
                            ?>
                                <div style="padding:8px 0; border-bottom:1px solid #E2E8F0;">
                                    <div style="font-weight:600; color:#334155; font-size:14px;"><?php echo htmlspecialchars($exam['exam_title']); ?></div>
                                    <div style="color:#94A3B8; font-size:12px; margin-top:2px;">
                                        <?php echo htmlspecialchars($exam['subject']); ?> • <?php echo date('d/m/Y', strtotime($exam['exam_date'])); ?>
                                    </div>
                                </div>
                            <?php endwhile; else: ?>
                                <div style="padding:8px 0; color:#94A3B8; font-size:13px;">Chưa có bài kiểm tra</div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endwhile; endif; ?>
                </div>
            </div>

            <?php include "includes/footer.php"; ?>
        </div>
    </div>

    <script>
        function searchClasses() {
            const input = document.getElementById('searchInput').value.toLowerCase();
            const classes = document.querySelectorAll('.searchable-class');
            
            classes.forEach(classCard => {
                const text = classCard.textContent.toLowerCase();
                classCard.style.display = text.includes(input) ? '' : 'none';
            });
        }
    </script>
</body>
</html>