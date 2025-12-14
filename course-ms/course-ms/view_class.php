<?php
include "connection.php";
include "auth.php";
requireRole(['admin', 'teacher']); // Admin và Teacher đều xem được

// Lấy ID lớp từ URL
if(!isset($_GET['id'])){
    header("Location: manage_classes.php");
    exit;
}

$class_id = intval($_GET['id']);

// Lấy thông tin lớp học
$class_sql = "SELECT c.*, u.full_name as teacher_name 
              FROM classes c 
              LEFT JOIN teachers t ON c.teacher_id=t.id 
              LEFT JOIN users u ON t.user_id=u.id 
              WHERE c.id=$class_id";
$class_result = mysqli_query($link, $class_sql);
$class = mysqli_fetch_assoc($class_result);

if(!$class){
    header("Location: manage_classes.php");
    exit;
}

// Lấy danh sách học sinh trong lớp
$students_sql = "SELECT s.*, u.full_name, u.username 
                 FROM students s 
                 JOIN users u ON s.user_id=u.id 
                 WHERE s.class_id=$class_id 
                 ORDER BY u.full_name ASC";
$students_result = mysqli_query($link, $students_sql);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Lớp <?php echo htmlspecialchars($class['name']); ?> | Teacher Bee</title>
    <link rel="stylesheet" href="https://site-assets.fontawesome.com/releases/v6.4.2/css/all.css">
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="dashboard_style.css">
    <style>
        body { background-color: #FFFDF7; font-family: 'Be Vietnam Pro', sans-serif; }
        
        /* Page Header */
        .page-header {
            display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;
        }
        .page-header h2 { font-size: 24px; font-weight: 700; color: #1E293B; margin: 0; }
        
        /* Back Link */
        .back-link { 
            display: inline-block; margin-bottom: 15px; font-size: 15px; 
            color: #64748B; text-decoration: none; font-weight: 600; 
        }
        .back-link:hover { color: #F59E0B; }

        /* Class Info Card */
        .class-info-card {
            background: linear-gradient(135deg, #F59E0B 0%, #FBBF24 100%);
            border-radius: 20px;
            padding: 30px;
            margin-bottom: 30px;
            color: white;
            box-shadow: 0 10px 30px rgba(245, 158, 11, 0.3);
        }
        .class-info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        .info-item {
            background: rgba(255,255,255,0.2);
            padding: 15px;
            border-radius: 12px;
            backdrop-filter: blur(10px);
        }
        .info-label {
            font-size: 12px;
            opacity: 0.9;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 5px;
        }
        .info-value {
            font-size: 18px;
            font-weight: 700;
        }

        /* White Card */
        .white-card {
            background: #FFFFFF;
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.05);
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            padding-bottom: 20px;
            border-bottom: 1px solid #F1F5F9;
        }
        .card-header h3 { 
            font-size: 18px; 
            font-weight: 700; 
            color: #1E293B; 
            margin: 0; 
        }

        /* Table */
        .custom-table { width: 100%; border-collapse: collapse; }
        .custom-table th { 
            text-align: left; color: #64748B; font-weight: 600; 
            font-size: 14px; padding: 15px 10px; 
            border-bottom: 1px solid #F1F5F9; 
        }
        .custom-table td { 
            padding: 15px 10px; color: #1E293B; font-size: 14px; 
            font-weight: 600; border-bottom: 1px solid #F1F5F9; 
            vertical-align: middle; 
        }
        .custom-table tr:last-child td { border-bottom: none; }
        .custom-table tr:hover td { background-color: #FFFBEB; }

        /* Student Avatar */
        .student-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #3B82F6 0%, #60A5FA 100%);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 16px;
            margin-right: 12px;
            vertical-align: middle;
        }

        .student-info {
            display: inline-block;
            vertical-align: middle;
        }
        .student-name {
            font-weight: 700;
            color: #1E293B;
            display: block;
        }
        .student-code {
            font-size: 12px;
            color: #64748B;
            font-weight: 500;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
        }
        .empty-state i {
            font-size: 64px;
            color: #CBD5E1;
            margin-bottom: 20px;
        }
        .empty-state p {
            color: #94A3B8;
            font-size: 16px;
        }
    </style>
</head>
<body>
    <?php include "includes/sidebar.php"; ?>

    <div class="main-wrapper">
        <?php include "includes/topbar.php"; ?>

        <div class="content-scroll" style="padding: 30px;">
            
            <div class="page-header">
                <h2>Chi tiết lớp học</h2>
            </div>
            
            <a href="manage_classes.php" class="back-link">
                &lt; Quay lại danh sách lớp
            </a>

            <!-- Class Info Card -->
            <div class="class-info-card">
                <h2 style="margin: 0 0 5px 0; font-size: 28px;">
                    <?php echo htmlspecialchars($class['name']); ?>
                </h2>
                <p style="margin: 0; opacity: 0.9; font-size: 14px;">
                    Mã lớp: <strong><?php echo htmlspecialchars($class['class_code']); ?></strong>
                </p>
                
                <div class="class-info-grid">
                    <div class="info-item">
                        <div class="info-label">
                            <i class="fa-solid fa-users"></i> Sĩ số hiện tại
                        </div>
                        <div class="info-value">
                            <?php echo mysqli_num_rows($students_result); ?> / <?php echo $class['student_limit']; ?>
                        </div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-label">
                            <i class="fa-solid fa-user-tie"></i> Giáo viên
                        </div>
                        <div class="info-value">
                            <?php echo $class['teacher_name'] ? htmlspecialchars($class['teacher_name']) : 'Chưa có'; ?>
                        </div>
                    </div>
                    
                    <?php if($class['description']): ?>
                    <div class="info-item" style="grid-column: 1 / -1;">
                        <div class="info-label">
                            <i class="fa-solid fa-align-left"></i> Mô tả
                        </div>
                        <div class="info-value" style="font-size: 14px; font-weight: 500;">
                            <?php echo htmlspecialchars($class['description']); ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Students List -->
            <div class="white-card">
                <div class="card-header">
                    <h3>
                        <i class="fa-solid fa-user-graduate"></i> 
                        Danh sách học sinh (<?php echo mysqli_num_rows($students_result); ?>)
                    </h3>
                </div>

                <?php if(mysqli_num_rows($students_result) > 0): ?>
                    <table class="custom-table">
                        <thead>
                            <tr>
                                <th>STT</th>
                                <th>Học sinh</th>
                                <th>Mã học sinh</th>
                                <th>Tên đăng nhập</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php 
                        $stt = 1;
                        mysqli_data_seek($students_result, 0); // Reset pointer
                        while($student = mysqli_fetch_assoc($students_result)): 
                        ?>
                            <tr>
                                <td><?php echo $stt++; ?></td>
                                
                                <td>
                                    <div class="student-avatar">
                                        <?php echo strtoupper(substr($student['full_name'], 0, 1)); ?>
                                    </div>
                                    <div class="student-info">
                                        <span class="student-name">
                                            <?php echo htmlspecialchars($student['full_name']); ?>
                                        </span>
                                    </div>
                                </td>
                                
                                <td>
                                    <span style="font-family: 'Courier New', monospace; font-weight: 700; color: #3B82F6;">
                                        <?php echo htmlspecialchars($student['student_code']); ?>
                                    </span>
                                </td>
                                
                                <td style="color: #64748B; font-weight: 500;">
                                    <?php echo htmlspecialchars($student['username']); ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fa-solid fa-users-slash"></i>
                        <p>Chưa có học sinh nào trong lớp này</p>
                    </div>
                <?php endif; ?>
            </div>

        </div>
    </div>

</body>
</html>