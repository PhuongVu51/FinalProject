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
        
        .student-info-cell {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }
        
        .student-name {
            font-weight: 700;
            color: #1E293B;
            font-size: 15px;
        }
        
        .student-code {
            font-family: 'Courier New', monospace;
            font-size: 12px;
            color: #64748B;
        }
        
        .class-wish {
            color: #F59E0B;
            font-weight: 600;
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
        
        .action-cell {
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
                                <th>HỌC SINH</th>
                                <th width="120">MÃ SV</th>
                                <th>NGUYỆN VỌNG</th>
                                <th width="150">NGÀY NỘP</th>
                                <th width="200">XỬ LÝ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $res = mysqli_query($link, "SELECT a.*, u.full_name, s.student_code, c.name 
                                                       FROM applications a 
                                                       JOIN students s ON a.student_id=s.id 
                                                       JOIN users u ON s.user_id=u.id 
                                                       JOIN classes c ON a.class_id=c.id 
                                                       WHERE a.status='pending' 
                                                       ORDER BY a.applied_at DESC");
                            
                            if(mysqli_num_rows($res) > 0):
                                while($r = mysqli_fetch_assoc($res)): 
                            ?>
                                <tr>
                                    <td>
                                        <div class="student-info-cell">
                                            <div class="student-name">
                                                <?php echo htmlspecialchars($r['full_name']); ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="student-code">
                                            <?php echo htmlspecialchars($r['student_code']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="class-wish">
                                            <?php echo htmlspecialchars($r['name']); ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="date-cell">
                                            <i class="fa-regular fa-calendar"></i>
                                            <?php echo date('d/m/Y', strtotime($r['applied_at'])); ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="action-cell">
                                            <a href="?ok=<?php echo $r['id']; ?>" 
                                               class="btn-approve" 
                                               title="Chấp nhận">
                                                <i class="fa-solid fa-check"></i> Duyệt
                                            </a>
                                            <a href="?reject=<?php echo $r['id']; ?>" 
                                               class="btn-reject" 
                                               onclick="return confirm('Từ chối đơn này?')"
                                               title="Từ chối">
                                                <i class="fa-solid fa-xmark"></i> Từ chối
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php 
                                endwhile;
                            else: 
                            ?>
                                <tr>
                                    <td colspan="5" class="empty-state">
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
                                <th>HỌC SINH</th>
                                <th width="120">MÃ SV</th>
                                <th>LỚP</th>
                                <th width="150">NGÀY DUYỆT</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $res2 = mysqli_query($link, "SELECT a.*, u.full_name, s.student_code, c.name 
                                                        FROM applications a 
                                                        JOIN students s ON a.student_id=s.id 
                                                        JOIN users u ON s.user_id=u.id 
                                                        JOIN classes c ON a.class_id=c.id 
                                                        WHERE a.status='approved' 
                                                        ORDER BY a.applied_at DESC 
                                                        LIMIT 20");
                            
                            if(mysqli_num_rows($res2) > 0):
                                while($r = mysqli_fetch_assoc($res2)): 
                            ?>
                                <tr>
                                    <td>
                                        <div class="student-name">
                                            <?php echo htmlspecialchars($r['full_name']); ?>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="student-code">
                                            <?php echo htmlspecialchars($r['student_code']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="class-wish">
                                            <?php echo htmlspecialchars($r['name']); ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="date-cell">
                                            <i class="fa-regular fa-calendar"></i>
                                            <?php echo date('d/m/Y', strtotime($r['applied_at'])); ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php 
                                endwhile;
                            else: 
                            ?>
                                <tr>
                                    <td colspan="4" class="empty-state">
                                        <i class="fa-solid fa-inbox"></i>
                                        <p>Chưa có đơn nào được duyệt</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
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
    </script>
</body>
</html>