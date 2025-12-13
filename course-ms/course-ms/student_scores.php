<?php
include "connection.php";
include "auth.php";
requireRole(['student']);
$sid = $_SESSION['student_id'];
?>
<!DOCTYPE html>
<html>
<link rel="stylesheet" href="https://site-assets.fontawesome.com/releases/v6.4.2/css/all.css">
<head>
    <title>Kết Quả Học Tập</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="dashboard_style.css">
    <style>
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-box {
            background: white;
            padding: 24px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            text-align: center;
        }
        .stat-box .icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            font-size: 28px;
        }
        .stat-box.total .icon { background: #E3F2FD; color: #1976D2; }
        .stat-box.avg .icon { background: #FFF3E0; color: #F57C00; }
        .stat-box.high .icon { background: #E8F5E9; color: #388E3C; }
        .stat-box.low .icon { background: #FFEBEE; color: #D32F2F; }
        .stat-number {
            font-size: 32px;
            font-weight: 700;
            color: #333;
            margin-bottom: 5px;
        }
        .stat-label {
            color: #666;
            font-size: 14px;
        }
        .scores-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
        .scores-table thead th {
            background: #FFF8E1;
            color: #E65100;
            padding: 15px;
            text-align: left;
            font-weight: 700;
            font-size: 15px;
        }
        .scores-table tbody td {
            padding: 15px;
            border-bottom: 1px solid #f0f0f0;
        }
        .scores-table tbody tr:hover {
            background: #fafafa;
        }
        .score-badge {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 700;
            font-size: 16px;
        }
        .score-excellent { background: #E8F5E9; color: #2E7D32; }
        .score-good { background: #FFF3E0; color: #F57C00; }
        .score-fair { background: #FFEBEE; color: #D32F2F; }
        .subject-tag {
            display: inline-block;
            padding: 4px 12px;
            background: #f0f0f0;
            border-radius: 12px;
            font-size: 13px;
            font-weight: 600;
            color: #666;
        }
        .search-filter {
            background: white;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 24px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            display: flex;
            gap: 15px;
        }
        .search-filter input, .search-filter select {
            flex: 1;
            padding: 12px 20px;
            border: 2px solid #E0E0E0;
            border-radius: 8px;
            font-size: 15px;
        }
    </style>
</head>
<body>
    <?php include "includes/sidebar.php"; ?>
    <div class="main-wrapper">
        <?php include "includes/topbar.php"; ?>
        <div class="content-scroll">
            
            <div class="hero-box">
                <i class="fa-solid fa-trophy hero-bg-icon"></i>
                <h1>Kết Quả Học Tập</h1>
                <p>Theo dõi và nâng cao thành tích học tập của bạn</p>
            </div>

            <?php
            // Calculate statistics
            $stats_query = "SELECT 
                            COUNT(*) as total_exams,
                            AVG(score) as avg_score,
                            MAX(score) as highest_score,
                            MIN(score) as lowest_score
                            FROM scores WHERE student_id=$sid";
            $stats_result = mysqli_query($link, $stats_query);
            $stats = mysqli_fetch_assoc($stats_result);
            ?>

            <!-- Statistics -->
            <div class="stats-grid">
                <div class="stat-box total">
                    <div class="icon"><i class="fa-solid fa-file-lines"></i></div>
                    <div class="stat-number"><?php echo $stats['total_exams'] ?? 0; ?></div>
                    <div class="stat-label">Tổng bài thi</div>
                </div>
                <div class="stat-box avg">
                    <div class="icon"><i class="fa-solid fa-chart-line"></i></div>
                    <div class="stat-number"><?php echo number_format($stats['avg_score'] ?? 0, 1); ?></div>
                    <div class="stat-label">Điểm trung bình</div>
                </div>
                <div class="stat-box high">
                    <div class="icon"><i class="fa-solid fa-arrow-up"></i></div>
                    <div class="stat-number"><?php echo $stats['highest_score'] ?? '--'; ?></div>
                    <div class="stat-label">Điểm cao nhất</div>
                </div>
                <div class="stat-box low">
                    <div class="icon"><i class="fa-solid fa-arrow-down"></i></div>
                    <div class="stat-number"><?php echo $stats['lowest_score'] ?? '--'; ?></div>
                    <div class="stat-label">Điểm thấp nhất</div>
                </div>
            </div>

            <!-- Search and Filter -->
            <div class="search-filter">
                <input type="text" id="searchInput" placeholder="🔍 Tìm kiếm bài thi..." onkeyup="filterScores()">
                <select id="subjectFilter" onchange="filterScores()">
                    <option value="">Tất cả môn học</option>
                    <?php
                    $subjects = mysqli_query($link, "SELECT DISTINCT e.subject FROM scores sc JOIN exams e ON sc.exam_id=e.id WHERE sc.student_id=$sid");
                    while($subj = mysqli_fetch_assoc($subjects)):
                    ?>
                        <option value="<?php echo $subj['subject']; ?>"><?php echo $subj['subject']; ?></option>
                    <?php endwhile; ?>
                </select>
            </div>

            <!-- Scores Table -->
            <div class="card">
                <table class="scores-table">
                    <thead>
                        <tr>
                            <th>Môn Học</th>
                            <th>Bài Kiểm Tra</th>
                            <th>Thời Gian Thi</th>
                            <th style="text-align:center;">Điểm Số</th>
                            <th>Nhận xét</th>
                        </tr>
                    </thead>
                    <tbody id="scoresTableBody">
                        <?php 
                        $scores_query = "SELECT sc.score, sc.comments, e.exam_title, e.subject, e.exam_date 
                                        FROM scores sc 
                                        JOIN exams e ON sc.exam_id=e.id 
                                        WHERE sc.student_id=$sid 
                                        ORDER BY e.exam_date DESC";
                        $scores = mysqli_query($link, $scores_query);
                        
                        if(mysqli_num_rows($scores) > 0):
                            while($score = mysqli_fetch_assoc($scores)): 
                                $score_class = '';
                                if($score['score'] >= 8) $score_class = 'score-excellent';
                                elseif($score['score'] >= 5) $score_class = 'score-good';
                                else $score_class = 'score-fair';
                        ?>
                            <tr class="score-row" data-subject="<?php echo $score['subject']; ?>">
                                <td>
                                    <span class="subject-tag"><?php echo $score['subject']; ?></span>
                                </td>
                                <td style="font-weight:600;"><?php echo $score['exam_title']; ?></td>
                                <td style="color:#64748B;">
                                    <i class="fa-regular fa-calendar"></i>
                                    <?php echo date('d/m/Y', strtotime($score['exam_date'])); ?>
                                </td>
                                <td style="text-align:center;">
                                    <span class="score-badge <?php echo $score_class; ?>">
                                        <?php echo $score['score']; ?>
                                    </span>
                                </td>
                                <td style="color:#666; font-style:italic;">
                                    <?php echo $score['comments'] ?? 'Chưa có nhận xét'; ?>
                                </td>
                            </tr>
                        <?php 
                            endwhile;
                        else:
                        ?>
                            <tr>
                                <td colspan="5" style="text-align:center; padding:40px; color:#999;">
                                    <i class="fa-regular fa-folder-open" style="font-size:48px; margin-bottom:15px;"></i>
                                    <div>Chưa có kết quả học tập nào</div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <?php include "includes/footer.php"; ?>
        </div>
    </div>

    <script>
        function filterScores() {
            const searchText = document.getElementById('searchInput').value.toLowerCase();
            const subjectFilter = document.getElementById('subjectFilter').value;
            const rows = document.querySelectorAll('.score-row');
            
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                const subject = row.getAttribute('data-subject');
                
                const matchesSearch = text.includes(searchText);
                const matchesSubject = !subjectFilter || subject === subjectFilter;
                
                row.style.display = (matchesSearch && matchesSubject) ? '' : 'none';
            });
        }
    </script>
</body>
</html>