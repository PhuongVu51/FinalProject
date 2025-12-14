<?php
include "connection.php";
include "auth.php";
requireRole(['admin']); // Only Admin

// Handle Delete
if(isset($_GET['del'])){
    $id = intval($_GET['del']);
    mysqli_query($link, "DELETE FROM classes WHERE id=$id");
    header("Location: manage_classes.php");
    exit;
}

// Handle Search
$search = "";
$sql_search = "";
if(isset($_GET['q']) && !empty($_GET['q'])){
    $search = mysqli_real_escape_string($link, $_GET['q']);
    $sql_search = " WHERE c.name LIKE '%$search%' OR c.class_code LIKE '%$search%' ";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Quản lý lớp học | Teacher Bee</title>
    <link rel="stylesheet" href="https://site-assets.fontawesome.com/releases/v6.4.2/css/all.css">
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="dashboard_style.css">
    <style>
        body { background-color: #FFFDF7; font-family: 'Be Vietnam Pro', sans-serif; }
        
        /* Page Header Style */
        .page-header {
            display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;
        }
        .page-header h2 { font-size: 24px; font-weight: 700; color: #1E293B; margin: 0; }
        
        /* Back Link */
        .back-link { display: inline-block; margin-bottom: 15px; font-size: 15px; color: #64748B; text-decoration: none; font-weight: 600; }
        .back-link:hover { color: #F59E0B; }

        /* Card Style */
        .white-card {
            background: #FFFFFF;
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.05);
            border: none;
        }

        .card-top-row {
            display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;
        }
        .white-card h3 { font-size: 18px; font-weight: 700; color: #1E293B; margin: 0; }

        /* Search Box Style */
        .search-box { display: flex; gap: 10px; }
        .search-input {
            padding: 8px 15px; border: 1px solid #E2E8F0; border-radius: 8px; outline: none; font-size: 14px; width: 250px;
        }
        .search-btn {
            background: #F59E0B; color: white; border: none; padding: 8px 15px; border-radius: 8px; cursor: pointer;
        }

        /* Table Style */
        .custom-table { width: 100%; border-collapse: collapse; }
        .custom-table th { text-align: left; color: #64748B; font-weight: 600; font-size: 14px; padding: 15px 10px; border-bottom: 1px solid #F1F5F9; }
        .custom-table td { padding: 15px 10px; color: #1E293B; font-size: 14px; font-weight: 600; border-bottom: 1px solid #F1F5F9; vertical-align: middle; }
        .custom-table tr:last-child td { border-bottom: none; }

        /* Code Badge - Mã lớp */
        .code-badge {
            display: inline-block;
            background: #F0F9FF; 
            color: #0369A1; 
            padding: 6px 12px; 
            border-radius: 8px; 
            font-weight: 700; 
            font-size: 13px;
            font-family: 'Courier New', monospace;
            border: 1px solid #BAE6FD;
        }

        .teacher-badge {
            display: inline-flex; align-items: center; gap: 8px;
            background: #F8FAFC; padding: 5px 10px; border-radius: 20px; font-size: 13px; color: #475569; border: 1px solid #E2E8F0;
        }
        .teacher-icon { color: #3B82F6; }

        .limit-badge {
            background: #FFFBEB; color: #D97706; padding: 4px 8px; border-radius: 6px; font-weight: 700; font-size: 12px;
        }

        .action-icon { 
            font-size: 16px; 
            margin-right: 8px; 
            text-decoration: none; 
            transition: 0.2s; 
            cursor: pointer;
            width: 36px;
            height: 36px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
        }
        .edit-icon { 
            color: #3B82F6; 
            background: #EFF6FF;
            border: 1px solid #DBEAFE;
        }
        .delete-icon { 
            color: #EF4444;
            background: #FEF2F2;
            border: 1px solid #FECACA;
        }
        .edit-icon:hover { 
            background: #DBEAFE;
            transform: translateY(-1px);
        }
        .delete-icon:hover { 
            background: #FEE2E2;
            transform: translateY(-1px);
        }

        .btn-create {
            background-color: #F59E0B; color: white; text-decoration: none; padding: 10px 20px; border-radius: 10px; font-weight: 600; font-size: 14px; box-shadow: 0 4px 6px -1px rgba(245, 158, 11, 0.2); transition: 0.2s;
            display: inline-flex; align-items: center; gap: 8px;
        }
        .btn-create:hover { background-color: #D97706; transform: translateY(-1px); }
    </style>
</head>
<body>
    <?php include "includes/sidebar.php"; ?>

    <div class="main-wrapper">
        <?php include "includes/topbar.php"; ?>

        <div class="content-scroll">
            <!-- WRAPPER ĐỂ CĂN GIỮA -->
            <div style="width: 100%; max-width: 1400px; margin: 0 auto;">
            
                <div class="page-header">
                    
                    <a href="add_class.php" class="btn-create">
                        <i class="fa-solid fa-plus"></i> Tạo Lớp Mới
                    </a>
                </div>
                
                <a href="home.php" class="back-link">&lt; Quay lại trang chủ</a>

                <div class="white-card">
                    <div class="card-top-row">
                        <h3>Danh sách lớp</h3>
                        
                        <form method="GET" class="search-box">
                            <input type="text" name="q" class="search-input" placeholder="Tìm kiếm lớp học hoặc mã lớp..." value="<?php echo htmlspecialchars($search); ?>">
                            <button type="submit" class="search-btn"><i class="fa-solid fa-magnifying-glass"></i></button>
                        </form>
                    </div>

                    <table class="custom-table">
                        <thead>
                            <tr>
                                <th>Mã lớp</th>
                                <th>Tên lớp</th>
                                <th>Mô tả</th>
                                <th>Giới hạn</th>
                                <th>Giáo viên</th>
                                <th>Chỉnh sửa</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php 
                        // Query data
                        $sql = "SELECT c.*, u.full_name 
                                FROM classes c 
                                LEFT JOIN teachers t ON c.teacher_id=t.id 
                                LEFT JOIN users u ON t.user_id=u.id
                                $sql_search
                                ORDER BY c.id DESC";
                        $res = mysqli_query($link, $sql);
                        
                        if(mysqli_num_rows($res) > 0):
                            while($row = mysqli_fetch_assoc($res)): ?>
                                <tr>
                                    <!-- Cột Mã lớp -->
                                    <td>
                                        <span class="code-badge">
                                            <?php echo $row['class_code'] ? htmlspecialchars($row['class_code']) : 'N/A'; ?>
                                        </span>
                                    </td>
                                    
                                    <!-- Cột Tên lớp - Click để xem chi tiết -->
                                    <td>
                                        <a href="view_class.php?id=<?php echo $row['id']; ?>" 
                                           style="color: #1E293B; text-decoration: none; font-weight: 700; transition: 0.2s;">
                                            <?php echo htmlspecialchars($row['name']); ?>
                                        </a>
                                    </td>
                                    
                                    <!-- Cột Mô tả -->
                                    <td style="font-weight:400; color:#64748B; font-size:13px;">
                                        <?php 
                                            $desc = isset($row['description']) ? $row['description'] : '';
                                            echo (strlen($desc) > 30) ? substr($desc,0,30)."..." : $desc; 
                                        ?>
                                    </td>

                                    <!-- Cột Giới hạn -->
                                    <td>
                                        <span class="limit-badge">
                                            <?php echo isset($row['student_limit']) ? $row['student_limit'] : '40'; ?>
                                        </span>
                                    </td>
                                    
                                    <!-- Cột Giáo viên -->
                                    <td>
                                        <?php if($row['full_name']): ?>
                                            <div class="teacher-badge">
                                                <i class="fa-solid fa-user-tie teacher-icon"></i>
                                                <?php echo htmlspecialchars($row['full_name']); ?>
                                            </div>
                                        <?php else: ?>
                                            <span style="color:#CBD5E1; font-style:italic;">-- Chưa được chỉ định --</span>
                                        <?php endif; ?>
                                    </td>
                                    
                                    <!-- Cột Chỉnh sửa -->
                                    <td>
                                        <a href="edit_class.php?id=<?php echo $row['id']; ?>" class="action-icon edit-icon" title="Chỉnh sửa">
                                            <i class="fa-solid fa-pen"></i>
                                        </a>
                                        <a href="?del=<?php echo $row['id']; ?>" class="action-icon delete-icon" 
                                           onclick="return confirm('Bạn chắc chắn muốn xóa?');" title="Xóa">
                                            <i class="fa-solid fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; 
                        else: ?>
                            <tr><td colspan="6" style="text-align: center; color: #94a3b8; padding: 30px;">Không tìm thấy lớp.</td></tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            
            </div>
            <!-- KẾT THÚC WRAPPER CĂN GIỮA -->
        </div>
    </div>
</body>
</html>