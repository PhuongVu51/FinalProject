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
<html>
<link rel="stylesheet" href="https://site-assets.fontawesome.com/releases/v6.4.2/css/all.css">
<head>
    <title>Lớp Học</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="dashboard_style.css">
    <style>
        .search-bar {
            background: white;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 24px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
        .search-bar input {
            width: 100%;
            padding: 12px 20px;
            border: 2px solid #E0E0E0;
            border-radius: 8px;
            font-size: 15px;
        }
        .search-bar input:focus {
            outline: none;
            border-color: #FFC107;
        }
        .class-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            cursor: pointer;
            transition: all 0.3s;
        }
        .class-card:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            transform: translateY(-2px);
        }
        .class-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }
        .class-title {
            font-size: 20px;
            font-weight: 700;
            color: #333;
        }
        .class-teacher {
            color: #666;
            font-size: 14px;
            margin-top: 5px;
        }
        .class-details {
            display: none;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 2px solid #f0f0f0;
        }
        .class-details.active {
            display: block;
            animation: slideDown 0.3s ease;
        }
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .exams-table {
            width: 100%;
            margin-top: 15px;
            border-collapse: collapse;
        }
        .exams-table th {
            background: #f8f8f8;
            padding: 12px;
            text-align: left;
            font-weight: 600;
            border-bottom: 2px solid #e0e0e0;
            font-size: 14px;
        }
        .exams-table td {
            padding: 12px;
            border-bottom: 1px solid #f0f0f0;
        }
        .enrolled-badge {
            background: #E8F5E9;
            color: #2E7D32;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        .pending-badge {
            background: #FFF3E0;
            color: #F57C00;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        .section-title {
            font-size: 22px;
            font-weight: 700;
            color: #E65100;
            margin: 30px 0 20px 0;
            padding-bottom: 10px;
            border-bottom: 2px solid #FCE592;
        }
        .btn-register {
            display: inline-block;
            background: #FFC107;
            color: #333;
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            margin-top: 20px;
            transition: all 0.3s;
        }
        .btn-register:hover {
            background: #FFA000;
            transform: translateY(-2px);
        }
        .intro-text {
            color: #666;
            line-height: 1.6;
            margin: 10px 0;
        }
        .score-display {
            font-weight: 700;
            font-size: 16px;
        }
        .score-excellent { color: #10B981; }
        .score-good { color: #F59E0B; }
        .score-fair { color: #EF4444; }
        .status-done {
            color: #10B981;
            font-weight: 600;
        }
        .status-pending {
            color: #F59E0B;
            font-weight: 600;
        }
        .empty-state {
            text-align: center;
            padding: 40px;
            color: #999;
        }
        .empty-state i {
            font-size: 48px;
            margin-bottom: 15px;
            opacity: 0.5;
        }
    </style>
</head>
<body>
<?php include "includes/sidebar.php"; ?>
<div class="main-wrapper">
    <?php include "includes/topbar.php"; ?>
    <div class="content-scroll">

        <!-- Search Bar -->
        <div class="search-bar">
            <input type="text" id="searchInput" placeholder="🔍 Tìm kiếm lớp học theo tên hoặc giáo viên..." onkeyup="searchClasses()">
        </div>

        <?php
        // Get enrolled classes
        $enrolled_query = "SELECT c.*, u.full_name as teacher_name, s.class_id as enrolled
                          FROM classes c 
                          LEFT JOIN teachers t ON c.teacher_id=t.id 
                          LEFT JOIN users u ON t.user_id=u.id
                          INNER JOIN students s ON s.class_id=c.id
                          WHERE s.id=$sid";
        $enrolled_rs = mysqli_query($link, $enrolled_query);
        ?>

        <!-- Enrolled Classes -->
        <h2 class="section-title">
            <i class="fa-solid fa-check-circle"></i> Lớp Học Đã Tham Gia
        </h2>
        
        <?php if(mysqli_num_rows($enrolled_rs) > 0): ?>
            <?php while($class = mysqli_fetch_assoc($enrolled_rs)): ?>
                <div class="class-card searchable-class" onclick="toggleDetails(<?php echo $class['id']; ?>)">
                    <div class="class-header">
                        <div>
                            <div class="class-title">
                                <i class="fa-solid fa-book"></i> <?php echo $class['name']; ?>
                            </div>
                            <div class="class-teacher">
                                <i class="fa-solid fa-user-tie"></i> 
                                Giáo viên: <strong><?php echo $class['teacher_name'] ?? 'Chưa phân công'; ?></strong>
                            </div>
                        </div>
                        <span class="enrolled-badge">
                            <i class="fa-solid fa-check"></i> Đang học
                        </span>
                    </div>
                    
                    <div class="class-details" id="details-<?php echo $class['id']; ?>">
                        <h4 style="margin-top:0; color:#E65100;">
                            <i class="fa-solid fa-info-circle"></i> Giới thiệu khóa học
                        </h4>
                        <p class="intro-text">
                            Lớp học <strong><?php echo $class['name']; ?></strong> do giáo viên 
                            <strong><?php echo $class['teacher_name'] ?? 'Chưa có'; ?></strong> phụ trách. 
                            Đây là môi trường học tập chuyên nghiệp, giúp học sinh phát triển kiến thức và kỹ năng.
                        </p>
                        
                        <h4 style="margin-top:20px; color:#E65100;">
                            <i class="fa-solid fa-file-lines"></i> Danh sách bài kiểm tra
                        </h4>
                        <?php
                        $exam_query = "SELECT e.*, s.score, s.id as score_id
                                      FROM exams e
                                      LEFT JOIN scores s ON e.id=s.exam_id AND s.student_id=$sid
                                      WHERE e.class_id={$class['id']}
                                      ORDER BY e.exam_date DESC";
                        $exams = mysqli_query($link, $exam_query);
                        ?>
                        <table class="exams-table">
                            <thead>
                                <tr>
                                    <th><i class="fa-solid fa-graduation-cap"></i> Tên bài thi</th>
                                    <th><i class="fa-solid fa-book-open"></i> Môn học</th>
                                    <th><i class="fa-regular fa-calendar"></i> Ngày thi</th>
                                    <th style="text-align:center;"><i class="fa-solid fa-star"></i> Điểm</th>
                                    <th><i class="fa-solid fa-circle-check"></i> Trạng thái</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(mysqli_num_rows($exams) > 0): ?>
                                    <?php while($exam = mysqli_fetch_assoc($exams)): 
                                        $score_class = '';
                                        if($exam['score_id']) {
                                            if($exam['score'] >= 8) $score_class = 'score-excellent';
                                            elseif($exam['score'] >= 5) $score_class = 'score-good';
                                            else $score_class = 'score-fair';
                                        }
                                    ?>
                                        <tr>
                                            <td style="font-weight:600;"><?php echo $exam['exam_title']; ?></td>
                                            <td><?php echo $exam['subject']; ?></td>
                                            <td style="color:#64748B;">
                                                <?php echo date('d/m/Y', strtotime($exam['exam_date'])); ?>
                                            </td>
                                            <td style="text-align:center;">
                                                <?php if($exam['score_id']): ?>
                                                    <span class="score-display <?php echo $score_class; ?>">
                                                        <?php echo $exam['score']; ?>
                                                    </span>
                                                <?php else: ?>
                                                    <span style="color:#999;">--</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if($exam['score_id']): ?>
                                                    <span class="status-done">
                                                        <i class="fa-solid fa-circle-check"></i> Đã chấm điểm
                                                    </span>
                                                <?php else: ?>
                                                    <span class="status-pending">
                                                        <i class="fa-regular fa-clock"></i> Chưa có điểm
                                                    </span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="empty-state">
                                            <i class="fa-regular fa-folder-open"></i>
                                            <div>Chưa có bài kiểm tra nào trong lớp này</div>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="empty-state" style="background:white; border-radius:12px; margin-bottom:30px;">
                <i class="fa-solid fa-inbox"></i>
                <div>Bạn chưa tham gia lớp học nào. Hãy đăng ký lớp học bên dưới!</div>
            </div>
        <?php endif; ?>

        <?php
        // Get available classes (not enrolled, not pending)
        $available_query = "SELECT c.*, u.full_name as teacher_name,
                           (SELECT status FROM applications WHERE student_id=$sid AND class_id=c.id) as app_status
                           FROM classes c 
                           LEFT JOIN teachers t ON c.teacher_id=t.id 
                           LEFT JOIN users u ON t.user_id=u.id
                           WHERE c.id NOT IN (SELECT class_id FROM students WHERE id=$sid AND class_id IS NOT NULL)";
        $available_rs = mysqli_query($link, $available_query);
        ?>

        <!-- Available Classes -->
        <h2 class="section-title">
            <i class="fa-solid fa-plus-circle"></i> Lớp Học Có Thể Đăng Ký
        </h2>
        
        <?php if(mysqli_num_rows($available_rs) > 0): ?>
            <?php while($class = mysqli_fetch_assoc($available_rs)): ?>
                <div class="class-card searchable-class" onclick="toggleDetails('available-<?php echo $class['id']; ?>')">
                    <div class="class-header">
                        <div>
                            <div class="class-title">
                                <i class="fa-solid fa-book"></i> <?php echo $class['name']; ?>
                            </div>
                            <div class="class-teacher">
                                <i class="fa-solid fa-user-tie"></i> 
                                Giáo viên: <strong><?php echo $class['teacher_name'] ?? 'Chưa phân công'; ?></strong>
                            </div>
                        </div>
                        <?php if($class['app_status'] == 'pending'): ?>
                            <span class="pending-badge">
                                <i class="fa-regular fa-clock"></i> Chờ duyệt
                            </span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="class-details" id="details-available-<?php echo $class['id']; ?>">
                        <h4 style="margin-top:0; color:#E65100;">
                            <i class="fa-solid fa-info-circle"></i> Giới thiệu khóa học
                        </h4>
                        <p class="intro-text">
                            Lớp học <strong><?php echo $class['name']; ?></strong> do giáo viên 
                            <strong><?php echo $class['teacher_name'] ?? 'Chưa có'; ?></strong> phụ trách. 
                            Đây là môi trường học tập chuyên nghiệp với chương trình giảng dạy hiện đại.
                        </p>
                        
                        <h4 style="margin-top:20px; color:#E65100;">
                            <i class="fa-solid fa-file-lines"></i> Danh sách bài kiểm tra
                        </h4>
                        <?php
                        $exam_query = "SELECT * FROM exams WHERE class_id={$class['id']} ORDER BY exam_date DESC";
                        $exams = mysqli_query($link, $exam_query);
                        ?>
                        <table class="exams-table">
                            <thead>
                                <tr>
                                    <th><i class="fa-solid fa-graduation-cap"></i> Tên bài thi</th>
                                    <th><i class="fa-solid fa-book-open"></i> Môn học</th>
                                    <th><i class="fa-regular fa-calendar"></i> Ngày thi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(mysqli_num_rows($exams) > 0): ?>
                                    <?php while($exam = mysqli_fetch_assoc($exams)): ?>
                                        <tr>
                                            <td style="font-weight:600;"><?php echo $exam['exam_title']; ?></td>
                                            <td><?php echo $exam['subject']; ?></td>
                                            <td style="color:#64748B;">
                                                <?php echo date('d/m/Y', strtotime($exam['exam_date'])); ?>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="3" class="empty-state">
                                            <i class="fa-regular fa-folder-open"></i>
                                            <div>Chưa có bài kiểm tra nào</div>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>

                        <?php if($class['app_status'] != 'pending'): ?>
                            <a href="?reg=<?php echo $class['id']; ?>" 
                               class="btn-register"
                               onclick="event.stopPropagation(); return confirm('Bạn có chắc muốn đăng ký lớp <?php echo $class['name']; ?>?');">
                                <i class="fa-solid fa-paper-plane"></i> Đăng Ký Ngay
                            </a>
                        <?php else: ?>
                            <div style="margin-top:20px; padding:15px; background:#FFF3E0; border-radius:8px; color:#F57C00;">
                                <i class="fa-regular fa-clock"></i> 
                                Đơn đăng ký của bạn đang chờ giáo viên phê duyệt
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="empty-state" style="background:white; border-radius:12px;">
                <i class="fa-solid fa-graduation-cap"></i>
                <div>Không có lớp học nào khả dụng để đăng ký</div>
            </div>
        <?php endif; ?>

    </div>
</div>

<script>
    function toggleDetails(classId) {
        const details = document.getElementById('details-' + classId);
        const allDetails = document.querySelectorAll('.class-details');
        
        // Close all other details first
        allDetails.forEach(detail => {
            if(detail.id !== 'details-' + classId) {
                detail.classList.remove('active');
            }
        });
        
        // Toggle current detail
        details.classList.toggle('active');
    }

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