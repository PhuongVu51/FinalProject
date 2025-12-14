<?php
include "connection.php";
include "auth.php";
requireRole(['admin']);

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
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý lớp học | Teacher Bee</title>
    <link rel="stylesheet" href="https://site-assets.fontawesome.com/releases/v6.4.2/css/all.css">
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="dashboard_style.css">
    <link rel="stylesheet" href="admin_tables.css">
</head>
<body>
    <?php include "includes/sidebar.php"; ?>

    <div class="main-wrapper">
        <?php include "includes/topbar.php"; ?>

        <div class="content-scroll">
            
            <div class="page-header">
                <div>
                    <p class="page-subtitle">
                        <a href="home.php" style="color: #94A3B8; text-decoration: none;">
                            &lt; Quay lại trang chủ
                        </a>
                    </p>
                </div>
            </div>

            <div class="white-card">
                <div class="card-top-row">
                    <h3 class="card-title">Danh sách lớp</h3>
                    
                    <div class="actions-row">
                        <form method="GET" class="search-box">
                            <input type="text" name="q" class="search-input" 
                                   placeholder="Tìm kiếm lớp học hoặc mã..." 
                                   value="<?php echo htmlspecialchars($search); ?>">
                            <button type="submit" class="search-btn">
                                <i class="fa-solid fa-magnifying-glass"></i>
                            </button>
                        </form>
                        
                        <a href="add_class.php" class="btn-create">
                            <i class="fa-solid fa-plus"></i> Tạo Lớp Mới
                        </a>
                    </div>
                </div>

                <table class="modern-table">
                    <thead>
                        <tr>
                            <th width="120">MÃ LỚP</th>
                            <th>TÊN LỚP</th>
                            <th>MÔ TẢ</th>
                            <th width="100">GIỚI HẠN</th>
                            <th>GIÁO VIÊN</th>
                            <th width="100" class="text-center">CHỈNH SỬA</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php 
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
                                <td>
                                    <span class="code-badge">
                                        <?php echo $row['class_code'] ? htmlspecialchars($row['class_code']) : 'N/A'; ?>
                                    </span>
                                </td>
                                
                                <td>
                                    <span class="class-name-link">
                                        <?php echo htmlspecialchars($row['name']); ?>
                                    </span>
                                </td>
                                
                                <td class="desc-cell">
                                    <?php 
                                        $desc = isset($row['description']) ? $row['description'] : '';
                                        echo (strlen($desc) > 50) ? substr($desc,0,50)."..." : $desc; 
                                    ?>
                                </td>

                                <td>
                                    <span class="limit-badge">
                                        <?php echo isset($row['student_limit']) ? $row['student_limit'] : '40'; ?>
                                    </span>
                                </td>
                                
                                <td>
                                    <?php if($row['full_name']): ?>
                                        <div class="teacher-info">
                                            <i class="fa-solid fa-user-tie"></i>
                                            <?php echo htmlspecialchars($row['full_name']); ?>
                                        </div>
                                    <?php else: ?>
                                        <span class="not-assigned">Chưa xếp lớp</span>
                                    <?php endif; ?>
                                </td>
                                
                                <td class="text-center">
                                    <div class="action-buttons">
                                        <a href="edit_class.php?id=<?php echo $row['id']; ?>" 
                                           class="action-btn edit-btn" title="Chỉnh sửa">
                                            <i class="fa-solid fa-pen"></i>
                                        </a>
                                        <a href="?del=<?php echo $row['id']; ?>" 
                                           class="action-btn delete-btn" 
                                           onclick="return confirm('Bạn chắc chắn muốn xóa?');" 
                                           title="Xóa">
                                            <i class="fa-solid fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; 
                    else: ?>
                        <tr>
                            <td colspan="6" class="empty-state">
                                <i class="fa-solid fa-inbox"></i>
                                <p>Không tìm thấy lớp học</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>