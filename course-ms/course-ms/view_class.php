<?php
include "connection.php";
include "auth.php";
requireRole(['admin']);

// Get class ID
if(!isset($_GET['id'])){
    header("Location: manage_classes.php");
    exit;
}

$class_id = intval($_GET['id']);

// Get class info
$class_query = "SELECT c.*, u.full_name as teacher_name, s.name as subject_name,
                (SELECT COUNT(*) FROM students st WHERE st.class_id = c.id) as student_count
                FROM classes c
                LEFT JOIN teachers t ON c.teacher_id = t.id
                LEFT JOIN users u ON t.user_id = u.id
                LEFT JOIN subjects s ON t.subject_id = s.id
                WHERE c.id = $class_id";
$class_result = mysqli_query($link, $class_query);

if(mysqli_num_rows($class_result) == 0){
    header("Location: manage_classes.php");
    exit;
}

$class = mysqli_fetch_assoc($class_result);

// Handle remove student from class
if(isset($_GET['remove_student'])){
    $student_id = intval($_GET['remove_student']);
    mysqli_query($link, "UPDATE students SET class_id = NULL WHERE id = $student_id");
    header("Location: view_class.php?id=$class_id");
    exit;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($class['name']); ?> | Teacher Bee</title>
    <link rel="stylesheet" href="https://site-assets.fontawesome.com/releases/v6.4.2/css/all.css">
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="dashboard_style.css">
    <style>
        body { background-color: #FFFDF7; font-family: 'Be Vietnam Pro', sans-serif; }
        /* Match brand and topbar font/size used in manage_applications.php */
        .brand { font-family: 'Nunito', sans-serif; font-size: 26px; font-weight: 800; }
        .topbar .page-breadcrumb { font-family: 'Nunito', sans-serif; font-size: 24px; font-weight: 800; color: #0F172A; margin: 0; }
        
        .content-container {
            max-width: 1400px;
            margin: 0 auto;
        }
        
        .back-link { 
            display: inline-block; margin-bottom: 15px; font-size: 15px; 
            color: #64748B; text-decoration: none; font-weight: 600; 
        }
        .back-link:hover { color: #F59E0B; }
        
        /* Class Header Card */
        .class-header-card {
            background: linear-gradient(135deg, #F59E0B 0%, #D97706 100%);
            border-radius: 20px;
            padding: 35px;
            margin-bottom: 25px;
            color: white;
            box-shadow: 0 10px 30px rgba(245, 158, 11, 0.3);
        }
        
        .class-title {
            font-size: 32px;
            font-weight: 700;
            margin: 0 0 10px 0;
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .class-code-display {
            background: rgba(255, 255, 255, 0.2);
            padding: 8px 16px;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 700;
            font-family: 'Courier New', monospace;
            border: 2px solid rgba(255, 255, 255, 0.3);
        }
        
        .class-meta {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-top: 25px;
        }
        
        .meta-item {
            background: rgba(255, 255, 255, 0.15);
            padding: 15px 20px;
            border-radius: 12px;
            backdrop-filter: blur(10px);
        }
        
        .meta-label {
            font-size: 13px;
            opacity: 0.9;
            margin-bottom: 5px;
            font-weight: 500;
        }
        
        .meta-value {
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
            border-bottom: 2px solid #F1F5F9;
        }
        
        .card-title {
            font-size: 20px;
            font-weight: 700;
            color: #1E293B;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        /* Student Table */
        .student-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .student-table th {
            text-align: left;
            padding: 15px 12px;
            color: #64748B;
            font-weight: 600;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #F1F5F9;
        }
        
        .student-table td {
            padding: 18px 12px;
            border-bottom: 1px solid #F1F5F9;
            vertical-align: middle;
        }
        
        .student-table tr:last-child td {
            border-bottom: none;
        }
        
        .student-table tr:hover {
            background: #FEFCE8;
        }
        
        /* Student Avatar */
        .student-avatar {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: linear-gradient(135deg, #3B82F6, #60A5FA);
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
            font-size: 15px;
            display: block;
        }
        
        .student-code {
            font-size: 12px;
            color: #64748B;
            font-weight: 600;
            font-family: 'Courier New', monospace;
        }
        
        /* Action Button */
        .btn-remove {
            background: #FEF2F2;
            color: #DC2626;
            border: none;
            padding: 8px 16px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 13px;
            cursor: pointer;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            text-decoration: none;
        }
        
        .btn-remove:hover {
            background: #DC2626;
            color: white;
        }
        
        .btn-edit-class {
            background: #F59E0B;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }
        
        .btn-edit-class:hover {
            background: #D97706;
            transform: translateY(-1px);
        }
        
        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #94A3B8;
        }
        
        .empty-state i {
            font-size: 64px;
            margin-bottom: 20px;
            opacity: 0.5;
        }
        
        .empty-state p {
            font-size: 16px;
            font-weight: 600;
            margin: 0;
        }
        
        /* Stats Badge */
        .stats-badge {
            background: rgba(255, 255, 255, 0.2);
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
    </style>
</head>
<body>
    <?php include "includes/sidebar.php"; ?>
    
    <div class="main-wrapper">
        <?php include "includes/topbar.php"; ?>
        
        <div class="content-scroll" style="padding: 30px;">
            <div class="content-container">
                
                <a href="manage_classes.php" class="back-link">
                    <i class="fa-solid fa-arrow-left"></i> Quay lại danh sách lớp
                </a>
                
                <!-- Class Header -->
                <div class="class-header-card">
                    <div class="class-title">
                        <i class="fa-solid fa-chalkboard"></i>
                        <?php echo htmlspecialchars($class['name']); ?>
                        <span class="class-code-display">
                            <?php echo htmlspecialchars($class['class_code']); ?>
                        </span>
                    </div>
                    
                    <div class="class-meta">
                        <div class="meta-item">
                            <div class="meta-label">
                                <i class="fa-solid fa-user-tie"></i> Giáo viên
                            </div>
                            <div class="meta-value">
                                <?php echo $class['teacher_name'] ? htmlspecialchars($class['teacher_name']) : 'Chưa phân công'; ?>
                            </div>
                        </div>
                        
                        <div class="meta-item">
                            <div class="meta-label">
                                <i class="fa-solid fa-book"></i> Môn học
                            </div>
                            <div class="meta-value">
                                <?php echo $class['subject_name'] ? htmlspecialchars($class['subject_name']) : 'N/A'; ?>
                            </div>
                        </div>
                        
                        <div class="meta-item">
                            <div class="meta-label">
                                <i class="fa-solid fa-users"></i> Số học sinh
                            </div>
                            <div class="meta-value">
                                <?php echo $class['student_count']; ?> / <?php echo $class['student_limit'] ?? 40; ?>
                            </div>
                        </div>
                        
                        <div class="meta-item">
                            <div class="meta-label">
                                <i class="fa-solid fa-calendar"></i> Năm học
                            </div>
                            <div class="meta-value">
                                <?php echo $class['academic_year'] ?? '2024-2025'; ?>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Students List -->
                <div class="white-card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fa-solid fa-users" style="color: #3B82F6;"></i>
                            Danh sách học sinh
                            <span class="stats-badge" style="background: #DBEAFE; color: #1E40AF; margin-left: 10px;">
                                <?php echo $class['student_count']; ?> học sinh
                            </span>
                        </h3>
                        
                        <a href="edit_class.php?id=<?php echo $class_id; ?>" class="btn-edit-class">
                            <i class="fa-solid fa-pen"></i>
                            Chỉnh sửa lớp
                        </a>
                    </div>
                    
                    <?php
                    // Get students in this class
                    $students_query = "SELECT s.*, u.full_name 
                                      FROM students s
                                      JOIN users u ON s.user_id = u.id
                                      WHERE s.class_id = $class_id
                                      ORDER BY u.full_name ASC";
                    $students_result = mysqli_query($link, $students_query);
                    ?>
                    
                    <?php if(mysqli_num_rows($students_result) > 0): ?>
                        <table class="student-table">
                            <thead>
                                <tr>
                                    <th>STT</th>
                                    <th>HỌC SINH</th>
                                    <th>MÃ SINH VIÊN</th>
                                    <th style="text-align: center;">THAO TÁC</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $stt = 1;
                                while($student = mysqli_fetch_assoc($students_result)): 
                                    $initial = strtoupper(substr($student['full_name'], 0, 1));
                                ?>
                                    <tr>
                                        <td style="font-weight: 700; color: #64748B;">
                                            <?php echo $stt++; ?>
                                        </td>
                                        <td>
                                            <div class="student-avatar"><?php echo $initial; ?></div>
                                            <div class="student-info">
                                                <span class="student-name">
                                                    <?php echo htmlspecialchars($student['full_name']); ?>
                                                </span>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="student-code">
                                                #<?php echo htmlspecialchars($student['student_code']); ?>
                                            </span>
                                        </td>
                                        <td style="text-align: center;">
                                            <a href="?id=<?php echo $class_id; ?>&remove_student=<?php echo $student['id']; ?>" 
                                               class="btn-remove"
                                               onclick="return confirm('Bạn có chắc muốn xóa học sinh này khỏi lớp?');">
                                                <i class="fa-solid fa-user-minus"></i>
                                                Xóa khỏi lớp
                                            </a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <div class="empty-state">
                            <i class="fa-solid fa-users-slash"></i>
                            <p>Lớp này chưa có học sinh nào</p>
                        </div>
                    <?php endif; ?>
                </div>
                
            </div>
        </div>
    </div>
</body>
</html>