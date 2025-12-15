<?php
include "connection.php";
include "auth.php";
requireRole(['student']);
$sid = $_SESSION['student_id'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Bài Kiểm Tra</title>
    <link rel="stylesheet" href="https://site-assets.fontawesome.com/releases/v6.4.2/css/all.css">
    <link rel="stylesheet" href="dashboard_style.css">
    <style>
        .exam-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 16px;
            border-left: 4px solid #3B82F6;
            transition: all 0.3s;
        }
        .exam-card:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            transform: translateY(-2px);
        }
        .exam-status {
            display: inline-block;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        .status-graded {
            background: #D1FAE5;
            color: #059669;
        }
        .status-not-graded {
            background: #FEF3C7;
            color: #D97706;
        }
        .exam-info {
            display: flex;
            gap: 24px;
            margin-top: 12px;
            flex-wrap: wrap;
        }
        .info-item {
            display: flex;
            align-items: center;
            gap: 6px;
            color: #64748B;
            font-size: 14px;
        }
        .info-item i {
            color: #3B82F6;
        }
        .score-display {
            font-size: 18px;
            font-weight: 700;
            margin-top: 8px;
        }
        .score-high {
            color: #10B981;
        }
        .score-medium {
            color: #F59E0B;
        }
        .score-low {
            color: #EF4444;
        }
    </style>
</head>
<body>
    <?php include "includes/sidebar.php"; ?>
    <div class="main-wrapper">
        <?php include "includes/student_topbar.php"; ?>
        <div class="content-scroll">
            
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title" style="display:flex; align-items:center; gap:10px;">
                        <i class="fa-solid fa-file-lines" style="color:#3B82F6;"></i> Danh Sách Bài Kiểm Tra
                    </h3>
                </div>
                <div style="padding:20px;">
                    <?php
                    // Lấy danh sách bài thi từ các lớp đã tham gia kèm điểm số
                    $exams = mysqli_query($link, "
                        SELECT e.*, 
                               c.name as class_name,
                               u.full_name as teacher_name,
                               s.score,
                               s.comments
                        FROM exams e
                        INNER JOIN classes c ON e.class_id = c.id
                        INNER JOIN student_classes sc ON c.id = sc.class_id
                        LEFT JOIN teachers t ON c.teacher_id = t.id
                        LEFT JOIN users u ON t.user_id = u.id
                        LEFT JOIN scores s ON e.id = s.exam_id AND s.student_id = $sid
                        WHERE sc.student_id = $sid
                        ORDER BY e.exam_date DESC
                    ");

                    if(mysqli_num_rows($exams) == 0): ?>
                        <div style="padding:40px; text-align:center; color:#94A3B8;">
                            <i class="fa-solid fa-inbox" style="font-size:48px; margin-bottom:16px; display:block; opacity:0.3;"></i>
                            Chưa có bài kiểm tra nào
                        </div>
                    <?php else:
                        while($e = mysqli_fetch_assoc($exams)):
                            $has_score = ($e['score'] !== null);
                            
                            if($has_score) {
                                $score_class = $e['score'] >= 8 ? 'score-high' : ($e['score'] >= 5 ? 'score-medium' : 'score-low');
                                $status_class = 'status-graded';
                                $status_text = 'Đã có điểm';
                            } else {
                                $status_class = 'status-not-graded';
                                $status_text = 'Chưa có điểm';
                            }
                    ?>
                        <div class="exam-card">
                            <div style="display:flex; justify-content:space-between; align-items:start;">
                                <div style="flex:1;">
                                    <div style="font-size:18px; font-weight:700; color:#0F172A; margin-bottom:4px;">
                                        <?php echo htmlspecialchars($e['exam_title']); ?>
                                    </div>
                                    <div style="color:#64748B; font-size:14px;">
                                        <i class="fa-solid fa-chalkboard"></i> <?php echo htmlspecialchars($e['class_name']); ?>
                                        • <i class="fa-solid fa-user-tie"></i> <?php echo htmlspecialchars($e['teacher_name'] ?? 'N/A'); ?>
                                    </div>
                                </div>
                                <span class="exam-status <?php echo $status_class; ?>">
                                    <?php echo $status_text; ?>
                                </span>
                            </div>

                            <div class="exam-info">
                                <div class="info-item">
                                    <i class="fa-solid fa-book"></i>
                                    <span><?php echo htmlspecialchars($e['subject']); ?></span>
                                </div>
                                <div class="info-item">
                                    <i class="fa-solid fa-calendar"></i>
                                    <span><?php echo date('d/m/Y', strtotime($e['exam_date'])); ?></span>
                                </div>
                            </div>

                            <?php if($has_score): ?>
                                <div class="score-display <?php echo $score_class; ?>">
                                    <i class="fa-solid fa-star"></i> Điểm: <?php echo number_format($e['score'], 1); ?>/10
                                </div>
                                <?php if($e['comments']): ?>
                                    <div style="margin-top:12px; padding:12px; background:#F8FAFC; border-radius:8px; color:#64748B; font-size:14px;">
                                        <strong><i class="fa-solid fa-comment"></i> Nhận xét:</strong> <?php echo htmlspecialchars($e['comments']); ?>
                                    </div>
                                <?php endif; ?>
                            <?php else: ?>
                                <div style="margin-top:12px; color:#94A3B8; font-size:14px;">
                                    <i class="fa-solid fa-hourglass-half"></i> Giáo viên chưa chấm điểm
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endwhile; endif; ?>
                </div>
            </div>

            <?php include "includes/footer.php"; ?>
        </div>
    </div>
</body>
</html>