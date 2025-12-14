<?php
include "connection.php";
include "auth.php";
requireRole(['admin']);

// Handle Delete
if(isset($_GET['del'])){
    $id = intval($_GET['del']);
    mysqli_query($link, "DELETE FROM users WHERE id=$id");
    header("Location: manage_teachers.php");
    exit;
}

// Handle Search
$search = "";
$sql_search = "";
if(isset($_GET['q']) && !empty($_GET['q'])){
    $search = mysqli_real_escape_string($link, $_GET['q']);
    $sql_search = " WHERE u.full_name LIKE '%$search%' OR t.teacher_code LIKE '%$search%' OR t.email LIKE '%$search%' ";
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý Giáo Viên | Teacher Bee</title>
    <link rel="stylesheet" href="https://site-assets.fontawesome.com/releases/v6.4.2/css/all.css">
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="dashboard_style.css">
    <style>
        body { background-color: #FFFDF7; font-family: 'Be Vietnam Pro', sans-serif; }
        
        /* Page Header */
        .page-header {
            display: flex; justify-content: space-between; align-items: center; 
            margin-bottom: 20px;
        }
        .page-header h2 { font-size: 24px; font-weight: 700; color: #1E293B; margin: 0; }
        
        /* Back Link */
        .back-link { 
            display: inline-block; margin-bottom: 15px; font-size: 15px; 
            color: #64748B; text-decoration: none; font-weight: 600; 
        }
        .back-link:hover { color: #F59E0B; }

        /* Content Container - Căn giữa */
        .content-container {
            max-width: 1400px;
            margin: 0 auto;
        }

        /* Card Style */
        .white-card {
            background: #FFFFFF;
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.05);
            border: none;
        }

        .card-top-row {
            display: flex; justify-content: space-between; align-items: center; 
            margin-bottom: 25px;
        }
        .white-card h3 { 
            font-size: 18px; font-weight: 700; color: #1E293B; margin: 0; 
        }

        /* Search Box Style */
        .search-box { display: flex; gap: 10px; }
        .search-input {
            padding: 8px 15px; border: 1px solid #E2E8F0; border-radius: 8px; 
            outline: none; font-size: 14px; width: 250px;
        }
        .search-btn {
            background: #F59E0B; color: white; border: none; 
            padding: 8px 15px; border-radius: 8px; cursor: pointer;
        }

        /* Table Style */
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

        /* Code Badge - Mã giáo viên */
        .code-badge {
            display: inline-block;
            background: #FEF3C7; 
            color: #D97706; 
            padding: 6px 12px; 
            border-radius: 8px; 
            font-weight: 700; 
            font-size: 13px;
            font-family: 'Courier New', monospace;
            border: 1px solid #FDE68A;
        }

        /* Subject Badge */
        .subject-badge {
            display: inline-flex; align-items: center; gap: 8px;
            background: #F0FDF4; padding: 5px 12px; border-radius: 20px; 
            font-size: 13px; color: #166534; border: 1px solid #BBF7D0;
            font-weight: 600;
        }

        /* Class Count Badge */
        .class-count-badge {
            display: inline-flex; align-items: center; gap: 8px;
            background: #EFF6FF; padding: 5px 12px; border-radius: 20px; 
            font-size: 13px; color: #1E40AF; border: 1px solid #DBEAFE;
            font-weight: 600;
        }

        /* Teacher Avatar */
        .teacher-avatar {
            width: 40px;
            height: 40px;
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
        
        .teacher-info {
            display: inline-block;
            vertical-align: middle;
        }
        .teacher-name {
            font-weight: 700;
            color: #1E293B;
            display: block;
        }
        .teacher-email {
            font-size: 12px;
            color: #64748B;
            font-weight: 500;
        }

        .action-icon { 
            font-size: 16px; margin-right: 12px; text-decoration: none; 
            transition: 0.2s; cursor: pointer; 
        }
        .edit-icon { color: #3B82F6; }
        .delete-icon { color: #1E293B; }
        .edit-icon:hover { color: #2563EB; }
        .delete-icon:hover { color: #DC2626; }

        .btn-create {
            background-color: #F59E0B; color: white; text-decoration: none; 
            padding: 10px 20px; border-radius: 10px; font-weight: 600; 
            font-size: 14px; box-shadow: 0 4px 6px -1px rgba(245, 158, 11, 0.2); 
            transition: 0.2s; display: inline-flex; align-items: center; gap: 8px;
        }
        .btn-create:hover { 
            background-color: #D97706; transform: translateY(-1px); 
        }
    </style>
</head>
<body>
    <?php include "includes/sidebar.php"; ?>

    <div class="main-wrapper">
        <?php include "includes/topbar.php"; ?>

        <div class="content-scroll" style="padding: 30px;">
            
            <!-- Content Container - Căn giữa -->
            <div class="content-container">
            
                <div class="page-header">
                    <h2>Quản lý Giáo Viên</h2>
                    
                    <a href="add_teacher.php" class="btn-create">
                        <i class="fa-solid fa-user-plus"></i> Thêm Giáo Viên
                    </a>
                </div>
                
                <a href="home.php" class="back-link">&lt; Quay lại trang chủ</a>

                <div class="white-card">
                    <div class="card-top-row">
                        <h3>Danh sách giáo viên</h3>
                        
                        <form method="GET" class="search-box">
                            <input type="text" name="q" class="search-input" 
                                   placeholder="Tìm kiếm giáo viên..." 
                                   value="<?php echo htmlspecialchars($search); ?>">
                            <button type="submit" class="search-btn">
                                <i class="fa-solid fa-magnifying-glass"></i>
                            </button>
                        </form>
                    </div>

                    <table class="custom-table">
                        <thead>
                            <tr>
                                <th>Mã GV</th>
                                <th>Họ và Tên</th>
                                <th>Email</th>
                                <th>Môn học</th>
                                <th>Số lớp dạy</th>
                                <th>Xóa</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php 
                        $sql = "SELECT t.*, u.full_name, u.id as user_id, s.name as subject_name,
                                (SELECT COUNT(*) FROM classes c WHERE c.teacher_id = t.id) as class_count
                                FROM teachers t 
                                JOIN users u ON t.user_id=u.id 
                                LEFT JOIN subjects s ON t.subject_id=s.id
                                $sql_search
                                ORDER BY t.id DESC";
                        $res = mysqli_query($link, $sql);
                        
                        if(mysqli_num_rows($res) > 0):
                            while($row = mysqli_fetch_assoc($res)): 
                                $initial = strtoupper(substr($row['full_name'], 0, 1));
                        ?>
                            <tr>
                                <!-- Cột Mã GV -->
                                <td>
                                    <span class="code-badge">
                                        <?php echo $row['teacher_code'] ? htmlspecialchars($row['teacher_code']) : 'N/A'; ?>
                                    </span>
                                </td>
                                
                                <!-- Cột Họ và Tên -->
                                <td>
                                    <div class="teacher-avatar"><?php echo $initial; ?></div>
                                    <div class="teacher-info">
                                        <span class="teacher-name">
                                            <?php echo htmlspecialchars($row['full_name']); ?>
                                        </span>
                                    </div>
                                </td>
                                
                                <!-- Cột Email -->
                                <td>
                                    <span class="teacher-email">
                                        <?php echo htmlspecialchars($row['email']); ?>
                                    </span>
                                </td>
                                
                                <!-- Cột Môn học -->
                                <td>
                                    <?php if($row['subject_name']): ?>
                                        <span class="subject-badge">
                                            <i class="fa-solid fa-book"></i>
                                            <?php echo htmlspecialchars($row['subject_name']); ?>
                                        </span>
                                    <?php else: ?>
                                        <span style="color:#CBD5E1; font-style:italic;">-- Chưa có --</span>
                                    <?php endif; ?>
                                </td>
                                
                                <!-- Cột Số lớp dạy -->
                                <td>
                                    <span class="class-count-badge">
                                        <i class="fa-solid fa-chalkboard-user"></i>
                                        <?php echo $row['class_count']; ?> lớp
                                    </span>
                                </td>
                                
                                <!-- Cột Xóa -->
                                <td style="text-align: center;">
                                    <a href="?del=<?php echo $row['user_id']; ?>" 
                                       class="action-icon delete-icon" 
                                       onclick="return confirm('Bạn chắc chắn muốn xóa giáo viên này?');" 
                                       title="Xóa">
                                        <i class="fa-solid fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; 
                        else: ?>
                            <tr><td colspan="6" style="text-align: center; color: #94a3b8; padding: 30px;">Không tìm thấy giáo viên.</td></tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            
            </div><!-- End content-container -->
        </div>
    </div>
</body>
</html>