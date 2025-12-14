<?php
include "connection.php";
include "auth.php";
requireRole(['admin']);

// Lấy ID giáo viên từ URL
$teacher_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Lấy thông tin giáo viên
$teacher_query = "SELECT t.*, u.full_name, s.name as subject_name 
                  FROM teachers t 
                  JOIN users u ON t.user_id = u.id
                  LEFT JOIN subjects s ON t.subject_id = s.id
                  WHERE t.id = $teacher_id";
$teacher_result = mysqli_query($link, $teacher_query);
$teacher = mysqli_fetch_assoc($teacher_result);

if(!$teacher) {
    header("Location: manage_teachers.php");
    exit;
}

// Lấy danh sách lớp đang dạy
$classes_query = "SELECT c.*, 
                  (SELECT COUNT(*) FROM students s WHERE s.class_id = c.id) as student_count
                  FROM classes c 
                  WHERE c.teacher_id = $teacher_id 
                  ORDER BY c.id DESC";
$classes_result = mysqli_query($link, $classes_query);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Chi tiết giáo viên | Teacher Bee</title>
    <link rel="stylesheet" href="https://site-assets.fontawesome.com/releases/v6.4.2/css/all.css">
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="dashboard_style.css">
    <style>
        body { 
            background-color: #FFFDF7; 
            font-family: 'Be Vietnam Pro', sans-serif; 
        }
         /* Match brand and topbar font/size used in manage_applications.php */
        .brand { font-family: 'Nunito', sans-serif; font-size: 26px; font-weight: 800; }
        .topbar .page-breadcrumb { font-family: 'Nunito', sans-serif; font-size: 24px; font-weight: 800; color: #0F172A; margin: 0; }
        
        /* Back Link */
        .back-link { 
            display: inline-block; 
            margin-bottom: 20px; 
            font-size: 15px; 
            color: #64748B; 
            text-decoration: none; 
            font-weight: 600;
            transition: 0.2s;
        }
        .back-link:hover { color: #F59E0B; }
        
        /* Teacher Info Card */
        .teacher-info-card {
            background: linear-gradient(135deg, #3B82F6 0%, #60A5FA 100%);
            border-radius: 20px;
            padding: 40px;
            color: white;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(59, 130, 246, 0.3);
            position: relative;
            overflow: hidden;
        }
        
        .teacher-info-card::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 300px;
            height: 300px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
        }
        
        .teacher-header {
            display: flex;
            align-items: center;
            gap: 30px;
            position: relative;
            z-index: 1;
        }
        
        .teacher-avatar-large {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 42px;
            font-weight: 800;
            color: #3B82F6;
            border: 5px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        }
        
        .teacher-details h1 {
            margin: 0 0 10px 0;
            font-size: 32px;
            font-weight: 800;
        }
        
        .teacher-meta {
            display: flex;
            gap: 30px;
            margin-top: 15px;
            flex-wrap: wrap;
        }
        
        .meta-item {
            display: flex;
            align-items: center;
            gap: 10px;
            background: rgba(255, 255, 255, 0.2);
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
        }
        
        .meta-item i {
            font-size: 16px;
        }
        
        /* Classes Section */
        .classes-section {
            background: white;
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.05);
        }
        
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }
        
        .section-header h2 {
            font-size: 22px;
            font-weight: 700;
            color: #1E293B;
            margin: 0;
        }
        
        .class-count-badge {
            background: #EFF6FF;
            color: #1E40AF;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 700;
            border: 1px solid #DBEAFE;
        }
        
        /* Classes Grid */
        .classes-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }
        
        .class-card {
            background: #F8FAFC;
            border: 1px solid #E2E8F0;
            border-radius: 16px;
            padding: 20px;
            transition: 0.2s;
            cursor: pointer;
        }
        
        .class-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            border-color: #F59E0B;
        }
        
        .class-code {
            background: #F0F9FF;
            color: #0369A1;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 700;
            font-family: 'Courier New', monospace;
            display: inline-block;
            margin-bottom: 10px;
        }
        
        .class-name {
            font-size: 18px;
            font-weight: 700;
            color: #1E293B;
            margin: 10px 0;
        }
        
        .class-stats {
            display: flex;
            gap: 15px;
            margin-top: 15px;
        }
        
        .stat-item {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 13px;
            color: #64748B;
            font-weight: 600;
        }
        
        .stat-item i {
            color: #3B82F6;
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
        }
    </style>
</head>
<body>
    <?php include "includes/sidebar.php"; ?>
    
    <div class="main-wrapper">
        <?php include "includes/topbar.php"; ?>
        
        <div class="content-scroll">
            <!-- WRAPPER CĂN GIỮA -->
            <div style="width: 100%; max-width: 1200px; margin: 0 auto; padding: 30px 0;">
                
                <a href="manage_teachers.php" class="back-link">
                    <i class="fa-solid fa-arrow-left"></i> Quay lại danh sách giáo viên
                </a>
                
                <!-- Teacher Info Card -->
                <div class="teacher-info-card">
                    <div class="teacher-header">
                        <div class="teacher-avatar-large">
                            <?php echo strtoupper(substr($teacher['full_name'], 0, 1)); ?>
                        </div>
                        
                        <div class="teacher-details">
                            <h1><?php echo htmlspecialchars($teacher['full_name']); ?></h1>
                            
                            <div class="teacher-meta">
                                <div class="meta-item">
                                    <i class="fa-solid fa-id-card"></i>
                                    <?php echo $teacher['teacher_code'] ? htmlspecialchars($teacher['teacher_code']) : 'N/A'; ?>
                                </div>
                                
                                <div class="meta-item">
                                    <i class="fa-solid fa-envelope"></i>
                                    <?php echo htmlspecialchars($teacher['email']); ?>
                                </div>
                                
                                <?php if($teacher['subject_name']): ?>
                                <div class="meta-item">
                                    <i class="fa-solid fa-book"></i>
                                    <?php echo htmlspecialchars($teacher['subject_name']); ?>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Classes Section -->
                <div class="classes-section">
                    <div class="section-header">
                        <h2>
                            <i class="fa-solid fa-chalkboard-user" style="color: #F59E0B;"></i>
                            Danh sách lớp đang dạy
                        </h2>
                        <span class="class-count-badge">
                            <?php echo mysqli_num_rows($classes_result); ?> lớp
                        </span>
                    </div>
                    
                    <?php if(mysqli_num_rows($classes_result) > 0): ?>
                        <div class="classes-grid">
                            <?php while($class = mysqli_fetch_assoc($classes_result)): ?>
                                <div class="class-card" onclick="window.location.href='view_class.php?id=<?php echo $class['id']; ?>'">
                                    <span class="class-code">
                                        <?php echo htmlspecialchars($class['class_code']); ?>
                                    </span>
                                    
                                    <div class="class-name">
                                        <?php echo htmlspecialchars($class['name']); ?>
                                    </div>
                                    
                                    <div class="class-stats">
                                        <div class="stat-item">
                                            <i class="fa-solid fa-users"></i>
                                            <?php echo $class['student_count']; ?> học sinh
                                        </div>
                                        
                                        <div class="stat-item">
                                            <i class="fa-solid fa-gauge-high"></i>
                                            Giới hạn: <?php echo $class['student_limit'] ? $class['student_limit'] : '40'; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    <?php else: ?>
                        <div class="empty-state">
                            <i class="fa-solid fa-chalkboard"></i>
                            <p>Giáo viên chưa được phân công dạy lớp nào</p>
                        </div>
                    <?php endif; ?>
                </div>
                
            </div>
            <!-- KẾT THÚC WRAPPER -->
        </div>
    </div>
</body>
</html>