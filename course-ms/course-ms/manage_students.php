<?php
include "connection.php"; 
include "auth.php"; 
requireRole(['admin']);

if(isset($_POST['add'])){
    $name = trim($_POST['name']); 
    $user = trim($_POST['user']); 
    $pass = md5($_POST['pass']); 
    $code = trim($_POST['code']);
    
    // Check if username or code exists
    $check = mysqli_query($link, "SELECT id FROM users WHERE username='$user'");
    $check_code = mysqli_query($link, "SELECT id FROM students WHERE student_code='$code'");
    
    if(mysqli_num_rows($check) > 0) {
        $error = "Tên đăng nhập đã được sử dụng!";
    } elseif(mysqli_num_rows($check_code) > 0) {
        $error = "Mã sinh viên đã tồn tại!";
    } else {
        mysqli_query($link, "INSERT INTO users (username,password,role,full_name) VALUES ('$user','$pass','student','$name')");
        $uid = mysqli_insert_id($link);
        mysqli_query($link, "INSERT INTO students (user_id,student_code) VALUES ($uid,'$code')");
        header("Location: manage_students.php");
        exit;
    }
}

if(isset($_GET['del'])){
    mysqli_query($link, "DELETE FROM users WHERE id=".intval($_GET['del']));
    header("Location: manage_students.php");
    exit;
}

// Handle Search
$search = "";
$sql_search = "";
if(isset($_GET['q']) && !empty($_GET['q'])){
    $search = mysqli_real_escape_string($link, $_GET['q']);
    $sql_search = " WHERE u.full_name LIKE '%$search%' OR s.student_code LIKE '%$search%' OR c.name LIKE '%$search%' ";
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <title>Quản Lý Học Sinh | Teacher Bee</title>
    <link rel="stylesheet" href="https://site-assets.fontawesome.com/releases/v6.4.2/css/all.css">
    <link rel="stylesheet" href="dashboard_style.css">
    <link rel="stylesheet" href="admin_tables.css">
    <style>
        .two-column-layout {
            display: grid;
            grid-template-columns: 400px 1fr;
            gap: 24px;
            align-items: start;
        }
        
        .form-card {
            background: white;
            border-radius: 16px;
            border: 1px solid #E2E8F0;
            overflow: hidden;
            position: sticky;
            top: 20px;
        }
        
        .form-card-header {
            padding: 20px 24px;
            border-bottom: 1px solid #F1F5F9;
            background: #FAFBFC;
        }
        
        .form-card-header h3 {
            margin: 0;
            font-size: 18px;
            font-weight: 700;
            color: #1E293B;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .form-card-body {
            padding: 24px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #475569;
            margin-bottom: 8px;
        }
        
        .form-control {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid #E2E8F0;
            border-radius: 8px;
            background: #F8FAFC;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.2s;
            box-sizing: border-box;
        }
        
        .form-control:focus {
            background: white;
            border-color: #F59E0B;
            outline: none;
        }
        
        .btn-submit {
            width: 100%;
            padding: 12px;
            background: #F59E0B;
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        
        .btn-submit:hover {
            background: #D97706;
        }
        
        .student-code {
            font-family: 'Courier New', monospace;
            font-weight: 700;
            color: #3B82F6;
            background: #EFF6FF;
            padding: 4px 10px;
            border-radius: 6px;
            display: inline-block;
        }
        
        .student-name {
            font-weight: 700;
            color: #1E293B;
            font-size: 15px;
        }
        
        .class-badge {
            background: #D1FAE5;
            color: #065F46;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
            display: inline-block;
        }
        
        .no-class {
            color: #CBD5E1;
            font-style: italic;
            font-size: 13px;
        }
        
        .error-message {
            background: #FEF2F2;
            color: #DC2626;
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        /* Search Box Styles */
        .search-box {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }
        
        .search-input {
            flex: 1;
            padding: 10px 16px;
            border: 1px solid #E2E8F0;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            outline: none;
            transition: all 0.2s;
        }
        
        .search-input:focus {
            border-color: #F59E0B;
            box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.1);
        }
        
        .search-btn {
            background: #F59E0B;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .search-btn:hover {
            background: #D97706;
        }
        
        .clear-search-btn {
            background: #F1F5F9;
            color: #64748B;
            border: none;
            padding: 10px 16px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.2s;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .clear-search-btn:hover {
            background: #E2E8F0;
        }
        
        .search-result-info {
            background: #F0F9FF;
            color: #0369A1;
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
            font-weight: 600;
            display: flex;
            align-items: center;
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
                    <p class="page-subtitle">
                        <a href="home.php" style="color: #94A3B8; text-decoration: none;">
                            &lt; Quay lại trang chủ
                        </a>
                    </p>
                </div>
            </div>
            
            <div class="two-column-layout">
                <!-- Add Student Form -->
                <div class="form-card">
                    <div class="form-card-header">
                        <h3>
                            <i class="fa-solid fa-user-plus" style="color: #F59E0B;"></i>
                            Thêm Mới
                        </h3>
                    </div>
                    <div class="form-card-body">
                        <?php if(isset($error)): ?>
                            <div class="error-message">
                                <i class="fa-solid fa-circle-exclamation"></i>
                                <?php echo $error; ?>
                            </div>
                        <?php endif; ?>
                        
                        <form method="post">
                            <div class="form-group">
                                <label class="form-label">Mã Sinh Viên</label>
                                <input type="text" name="code" class="form-control" 
                                       placeholder="VD: SV001" required>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Họ Tên</label>
                                <input type="text" name="name" class="form-control" 
                                       placeholder="Nguyễn Văn A" required>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Tên Đăng Nhập</label>
                                <input type="text" name="user" class="form-control" 
                                       placeholder="Tên đăng nhập" required>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Mật Khẩu</label>
                                <input type="password" name="pass" class="form-control" 
                                       placeholder="••••••" required>
                            </div>
                            
                            <button name="add" class="btn-submit">
                                <i class="fa-solid fa-check"></i> Thêm Học Sinh
                            </button>
                        </form>
                    </div>
                </div>
                
                <!-- Students List -->
                <div class="white-card">
                    <div class="card-top-row">
                        <h3 class="card-title">Danh Sách Học Sinh</h3>
                    </div>
                    
                    <!-- Search Box -->
                    <form method="GET" class="search-box">
                        <input type="text" name="q" class="search-input" 
                               placeholder="Tìm kiếm theo tên, mã sinh viên, hoặc lớp..." 
                               value="<?php echo htmlspecialchars($search); ?>">
                        <button type="submit" class="search-btn">
                            <i class="fa-solid fa-magnifying-glass"></i>
                            Tìm kiếm
                        </button>
                        <?php if(!empty($search)): ?>
                            <a href="manage_students.php" class="clear-search-btn">
                                <i class="fa-solid fa-xmark"></i>
                                Xóa
                            </a>
                        <?php endif; ?>
                    </form>
                    
                    <?php if(!empty($search)): ?>
                        <div class="search-result-info">
                            <i class="fa-solid fa-circle-info"></i>
                            Kết quả tìm kiếm cho: "<strong><?php echo htmlspecialchars($search); ?></strong>"
                        </div>
                    <?php endif; ?>
                    
                    <table class="modern-table">
                        <thead>
                            <tr>
                                <th width="120">MÃ SV</th>
                                <th>HỌ TÊN</th>
                                <th>LỚP</th>
                                <th width="80" class="text-center">XÓA</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $q = "SELECT s.*, u.full_name, c.name as cname, u.id as uid 
                                  FROM students s 
                                  JOIN users u ON s.user_id=u.id 
                                  LEFT JOIN classes c ON s.class_id=c.id 
                                  $sql_search
                                  ORDER BY s.id DESC";
                            $rs = mysqli_query($link, $q);
                            
                            if(mysqli_num_rows($rs) > 0):
                                while($r = mysqli_fetch_assoc($rs)): 
                            ?>
                                <tr>
                                    <td>
                                        <span class="student-code">
                                            #<?php echo htmlspecialchars($r['student_code']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="student-name">
                                            <?php echo htmlspecialchars($r['full_name']); ?>
                                        </div>
                                    </td>
                                    <td>
                                        <?php if($r['cname']): ?>
                                            <span class="class-badge">
                                                <?php echo htmlspecialchars($r['cname']); ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="no-class">Chưa xếp lớp</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <a href="?del=<?php echo $r['uid']; ?>" 
                                           onclick="return confirm('Xóa học sinh này?')" 
                                           class="action-btn delete-btn" 
                                           title="Xóa">
                                            <i class="fa-solid fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php 
                                endwhile;
                            else: 
                            ?>
                                <tr>
                                    <td colspan="4" class="empty-state">
                                        <i class="fa-solid fa-user-slash"></i>
                                        <p><?php echo !empty($search) ? 'Không tìm thấy học sinh nào' : 'Chưa có học sinh nào'; ?></p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>