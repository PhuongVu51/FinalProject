<?php
include "connection.php"; 
include "auth.php"; 
requireRole(['admin']);

if(isset($_GET['ok'])){
    $id = intval($_GET['ok']);
    $app = mysqli_fetch_assoc(mysqli_query($link, "SELECT * FROM applications WHERE id=$id"));
    mysqli_query($link, "UPDATE applications SET status='approved' WHERE id=$id");
    mysqli_query($link, "UPDATE students SET class_id={$app['class_id']} WHERE id={$app['student_id']}");
    header("Location: manage_applications.php");
    exit;
}

if(isset($_GET['reject'])){
    $id = intval($_GET['reject']);
    mysqli_query($link, "UPDATE applications SET status='rejected' WHERE id=$id");
    header("Location: manage_applications.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <title>Duyệt Đơn | Teacher Bee</title>
    <link rel="stylesheet" href="https://site-assets.fontawesome.com/releases/v6.4.2/css/all.css">
    <link rel="stylesheet" href="dashboard_style.css">
    <link rel="stylesheet" href="admin_tables.css">
    <style>
        .tabs-container {
            display: flex;
            gap: 8px;
            margin-bottom: 24px;
            border-bottom: 2px solid #F1F5F9;
        }
        
        .tab-button {
            padding: 12px 24px;
            background: none;
            border: none;
            color: #64748B;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            border-bottom: 3px solid transparent;
            transition: all 0.2s;
            position: relative;
            bottom: -2px;
        }
        
        .tab-button:hover {
            color: #1E293B;
        }
        
        .tab-button.active {
            color: #F59E0B;
            border-bottom-color: #F59E0B;
        }
        
        .tab-content {
            display: none;
        }
        
        .tab-content.active {
            display: block;
        }
        
        /* Class Row Styles */
        .class-row {
            cursor: pointer;
            transition: all 0.2s;
        }
        
        .class-row:hover {
            background: #FFFBEB !important;
        }
        
        .class-row.expanded {
            background: #FEF3C7 !important;
        }
        
        .class-header {
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 600;
            color: #1E293B;
            font-size: 15px;
        }
        
        .toggle-icon {
            color: #94A3B8;
            font-size: 14px;
            transition: transform 0.3s;
        }
        
        .class-row.expanded .toggle-icon {
            transform: rotate(90deg);
        }
        
        .class-name {
            color: #F59E0B;
            font-weight: 700;
            font-size: 15px;
        }
        
        .student-count {
            background: #DBEAFE;
            color: #1E40AF;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 700;
        }
        
        /* Dropdown Students */
        .students-dropdown {
            display: none;
            background: #F8FAFC;
        }
        
        .students-dropdown.show {
            display: table-row;
        }
        
        .students-list {
            padding: 0;
        }
        
        .student-item {
            background: white;
            border: 1px solid #E2E8F0;
            border-radius: 12px;
            padding: 16px 20px;
            margin-bottom: 12px;
            display: grid;
            grid-template-columns: 120px 1fr 150px 200px;
            gap: 20px;
            align-items: center;
            transition: all 0.2s;
        }
        
        .student-item:hover {
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }
        
        .student-item:last-child {
            margin-bottom: 0;
        }
        
        .student-code {
            font-family: 'Courier New', monospace;
            font-weight: 700;
            color: #64748B;
            font-size: 13px;
        }
        
        .student-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .student-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: linear-gradient(135deg, #3B82F6, #60A5FA);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 14px;
        }
        
        .student-name {
            font-weight: 700;
            color: #1E293B;
            font-size: 14px;
        }
        
        .date-cell {
            color: #64748B;
            font-size: 13px;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        
        .btn-approve {
            background: #D1FAE5;
            color: #065F46;
            padding: 8px 16px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 13px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s;
            border: none;
            cursor: pointer;
        }
        
        .btn-approve:hover {
            background: #A7F3D0;
        }
        
        .btn-reject {
            background: #FEE2E2;
            color: #DC2626;
            padding: 8px 16px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 13px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s;
            border: none;
            cursor: pointer;
        }
        
        .btn-reject:hover {
            background: #FECACA;
        }
        
        .action-buttons {
            display: flex;
            gap: 8px;
        }
    </style>
</head>
<body>
    <?php include "includes/sidebar.php"; ?>
    
    <div class="main-wrapper">
        <?php include "includes/topbar.php"; ?>
        
        <div class="content-scroll">
            <div class="page-header">
                <div>
                    <h2 class="page-title">Đơn Xin Vào Lớp</h2>
                    <p class="page-subtitle">
                        <a href="home.php" style="color: #94A3B8; text-decoration: none;">
                            &lt; Quay lại trang chủ
                        </a>
                    </p>
                </div>
            </div>
            
            <div class="tabs-container">
                <button class="tab-button active" onclick="switchTab('pending')">
                    <i class="fa-solid fa-clock"></i> Cần duyệt
                </button>
                <button class="tab-button" onclick="switchTab('approved')">
                    <i class="fa-solid fa-check"></i> Đã duyệt
                </button>
            </div>
            
            <!-- Pending Applications Tab -->
            <div id="pending-tab" class="tab-content active">
                <div class="white-card">
                    <div class="card-top-row">
                        <h3 class="card-title">
                            Đơn đang chờ duyệt
                        </h3>
                    </div>
                    
                    <table class="modern-table">
                        <thead>
                            <tr>
                                <th>NGUYỆN VỌNG</th>
                                <th width="150" class="text-center">SỐ LƯỢNG</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            // Group applications by class
                            $classes_query = "SELECT c.id, c.name, COUNT(a.id) as student_count
                                            FROM applications a 
                                            JOIN classes c ON a.class_id = c.id
                                            WHERE a.status='pending'
                                            GROUP BY c.id, c.name
                                            ORDER BY c.name ASC";
                            $classes_res = mysqli_query($link, $classes_query);
                            
                            if(mysqli_num_rows($classes_res) > 0):
                                while($class = mysqli_fetch_assoc($classes_res)): 
                                    $class_id = $class['id'];
                            ?>
                                <!-- Class Header Row -->
                                <tr class="class-row" onclick="toggleStudents(<?php echo $class_id; ?>)">
                                    <td>
                                        <div class="class-header">
                                            <i class="fa-solid fa-chevron-right toggle-icon" id="icon-<?php echo $class_id; ?>"></i>
                                            <span class="class-name"><?php echo htmlspecialchars($class['name']); ?></span>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="student-count">
                                            <?php echo $class['student_count']; ?> học sinh
                                        </span>
                                    </td>
                                </tr>
                                
                                <!-- Students Dropdown Row -->
                                <tr class="students-dropdown" id="students-<?php echo $class_id; ?>">
                                    <td colspan="2">
                                        <div class="students-list">
                                            <?php 
                                            // Get students for this class
                                            $students_query = "SELECT a.id as app_id, a.applied_at, 
                                                                     u.full_name, s.student_code
                                                              FROM applications a 
                                                              JOIN students s ON a.student_id=s.id 
                                                              JOIN users u ON s.user_id=u.id 
                                                              WHERE a.status='pending' AND a.class_id=$class_id
                                                              ORDER BY a.applied_at DESC";
                                            $students_res = mysqli_query($link, $students_query);
                                            
                                            while($student = mysqli_fetch_assoc($students_res)):
                                                $initial = strtoupper(substr($student['full_name'], 0, 1));
                                            ?>
                                                <div class="student-item">
                                                    <div class="student-code">
                                                        <?php echo htmlspecialchars($student['student_code']); ?>
                                                    </div>
                                                    
                                                    <div class="student-info">
                                                        <div class="student-avatar"><?php echo $initial; ?></div>
                                                        <div class="student-name">
                                                            <?php echo htmlspecialchars($student['full_name']); ?>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="date-cell">
                                                        <i class="fa-regular fa-calendar"></i>
                                                        <?php echo date('d/m/Y', strtotime($student['applied_at'])); ?>
                                                    </div>
                                                    
                                                    <div class="action-buttons">
                                                        <a href="?ok=<?php echo $student['app_id']; ?>" 
                                                           class="btn-approve" 
                                                           title="Duyệt">
                                                            <i class="fa-solid fa-check"></i> Duyệt
                                                        </a>
                                                        <a href="?reject=<?php echo $student['app_id']; ?>" 
                                                           class="btn-reject" 
                                                           onclick="return confirm('Từ chối đơn này?')"
                                                           title="Từ chối">
                                                            <i class="fa-solid fa-xmark"></i> Từ chối
                                                        </a>
                                                    </div>
                                                </div>
                                            <?php endwhile; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php 
                                endwhile;
                            else: 
                            ?>
                                <tr>
                                    <td colspan="2" class="empty-state">
                                        <i class="fa-solid fa-inbox"></i>
                                        <p>Không có đơn nào đang chờ duyệt</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Approved Applications Tab -->
            <div id="approved-tab" class="tab-content">
                <div class="white-card">
                    <div class="card-top-row">
                        <h3 class="card-title">
                            Đơn đã được duyệt
                        </h3>
                    </div>
                    
                    <table class="modern-table">
                        <thead>
                            <tr>
                                <th>LỚP HỌC</th>
                                <th width="150" class="text-center">SỐ LƯỢNG</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            // Group approved applications by class
                            $approved_classes_query = "SELECT c.id, c.name, COUNT(a.id) as student_count
                                                      FROM applications a 
                                                      JOIN classes c ON a.class_id = c.id
                                                      WHERE a.status='approved'
                                                      GROUP BY c.id, c.name
                                                      ORDER BY c.name ASC";
                            $approved_classes_res = mysqli_query($link, $approved_classes_query);
                            
                            if(mysqli_num_rows($approved_classes_res) > 0):
                                while($class = mysqli_fetch_assoc($approved_classes_res)): 
                                    $class_id = $class['id'];
                            ?>
                                <!-- Class Header Row -->
                                <tr class="class-row" onclick="toggleStudents('approved_<?php echo $class_id; ?>')">
                                    <td>
                                        <div class="class-header">
                                            <i class="fa-solid fa-chevron-right toggle-icon" id="icon-approved_<?php echo $class_id; ?>"></i>
                                            <span class="class-name"><?php echo htmlspecialchars($class['name']); ?></span>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="student-count" style="background: #D1FAE5; color: #065F46;">
                                            <?php echo $class['student_count']; ?> học sinh
                                        </span>
                                    </td>
                                </tr>
                                
                                <!-- Students Dropdown Row -->
                                <tr class="students-dropdown" id="students-approved_<?php echo $class_id; ?>">
                                    <td colspan="2">
                                        <div class="students-list">
                                            <?php 
                                            // Get approved students for this class
                                            $approved_students_query = "SELECT a.id as app_id, a.applied_at, 
                                                                              u.full_name, s.student_code
                                                                       FROM applications a 
                                                                       JOIN students s ON a.student_id=s.id 
                                                                       JOIN users u ON s.user_id=u.id 
                                                                       WHERE a.status='approved' AND a.class_id=$class_id
                                                                       ORDER BY a.applied_at DESC";
                                            $approved_students_res = mysqli_query($link, $approved_students_query);
                                            
                                            while($student = mysqli_fetch_assoc($approved_students_res)):
                                                $initial = strtoupper(substr($student['full_name'], 0, 1));
                                            ?>
                                                <div class="student-item">
                                                    <div class="student-code">
                                                        <?php echo htmlspecialchars($student['student_code']); ?>
                                                    </div>
                                                    
                                                    <div class="student-info">
                                                        <div class="student-avatar" style="background: linear-gradient(135deg, #10B981, #34D399);">
                                                            <?php echo $initial; ?>
                                                        </div>
                                                        <div class="student-name">
                                                            <?php echo htmlspecialchars($student['full_name']); ?>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="date-cell">
                                                        <i class="fa-regular fa-calendar"></i>
                                                        <?php echo date('d/m/Y', strtotime($student['applied_at'])); ?>
                                                    </div>
                                                    
                                                    <div class="action-buttons">
                                                        <span style="background: #D1FAE5; color: #065F46; padding: 8px 16px; border-radius: 8px; font-weight: 600; font-size: 13px; display: inline-flex; align-items: center; gap: 6px;">
                                                            <i class="fa-solid fa-circle-check"></i> Đã duyệt
                                                        </span>
                                                    </div>
                                                </div>
                                            <?php endwhile; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php 
                                endwhile;
                            else: 
                            ?>
                                <tr>
                                    <td colspan="2" class="empty-state">
                                        <i class="fa-solid fa-inbox"></i>
                                        <p>Chưa có đơn nào được duyệt</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
    
    <script>
        function switchTab(tabName) {
            // Hide all tabs
            document.querySelectorAll('.tab-content').forEach(tab => {
                tab.classList.remove('active');
            });
            
            // Remove active class from all buttons
            document.querySelectorAll('.tab-button').forEach(btn => {
                btn.classList.remove('active');
            });
            
            // Show selected tab
            document.getElementById(tabName + '-tab').classList.add('active');
            
            // Add active class to clicked button
            event.target.closest('.tab-button').classList.add('active');
        }
        
        function toggleStudents(classId) {
            const dropdown = document.getElementById('students-' + classId);
            const icon = document.getElementById('icon-' + classId);
            const row = dropdown.previousElementSibling;
            
            // Toggle dropdown
            dropdown.classList.toggle('show');
            row.classList.toggle('expanded');
        }
    </script>
</body>
</html>