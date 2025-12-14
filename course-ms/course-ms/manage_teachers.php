<?php
include "connection.php"; 
include "auth.php"; 
requireRole(['admin']);

if(isset($_POST['add'])) {
    $name = mysqli_real_escape_string($link, $_POST['name']); 
    $email = mysqli_real_escape_string($link, $_POST['email']); 
    $pass = md5($_POST['pass']);
    
    // Check if email exists
    $check = mysqli_query($link, "SELECT id FROM users WHERE username='$email'");
    if(mysqli_num_rows($check) > 0) {
        $error = "Email này đã được sử dụng!";
    } else {
        mysqli_query($link, "INSERT INTO users (username,password,role,full_name) VALUES ('$email','$pass','teacher','$name')");
        $uid = mysqli_insert_id($link);
        mysqli_query($link, "INSERT INTO teachers (user_id,email) VALUES ($uid,'$email')");
        header("Location: manage_teachers.php");
        exit;
    }
}

if(isset($_GET['del'])){
    mysqli_query($link, "DELETE FROM users WHERE id=".intval($_GET['del']));
    header("Location: manage_teachers.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <title>Quản Lý Giáo Viên | Teacher Bee</title>
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
        }
        
        .teacher-name-cell {
            display: flex;
            align-items: center;
        }
        
        .teacher-name {
            font-weight: 700;
            color: #1E293B;
        }
        
        .teacher-email {
            color: #64748B;
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
    </style>
</head>
<body>
    <?php include "includes/sidebar.php"; ?>
    
    <div class="main-wrapper">
        <?php include "includes/topbar.php"; ?>
        
        <div class="content-scroll">
            <div class="page-header">
                <div>
                    <h2 class="page-title">Quản Lý Giáo Viên</h2>
                    <p class="page-subtitle">
                        <a href="home.php" style="color: #94A3B8; text-decoration: none;">
                            &lt; Quay lại trang chủ
                        </a>
                    </p>
                </div>
            </div>
            
            <div class="two-column-layout">
                <!-- Add Teacher Form -->
                <div class="form-card">
                    <div class="form-card-header">
                        <h3>
                            <i class="fa-solid fa-user-plus" style="color: #F59E0B;"></i>
                            Thêm Giáo Viên
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
                                <label class="form-label">Họ và Tên</label>
                                <input type="text" name="name" class="form-control" 
                                       placeholder="Nhập tên giáo viên..." required>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Email Đăng Nhập</label>
                                <input type="email" name="email" class="form-control" 
                                       placeholder="example@bee.com" required>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Mật Khẩu</label>
                                <input type="password" name="pass" class="form-control" 
                                       placeholder="••••••" required>
                            </div>
                            
                            <button name="add" class="btn-submit">
                                <i class="fa-solid fa-check"></i> Lưu Lại
                            </button>
                        </form>
                    </div>
                </div>
                
                <!-- Teachers List -->
                <div class="white-card">
                    <div class="card-top-row">
                        <h3 class="card-title">Danh Sách Giáo Viên</h3>
                    </div>
                    
                    <table class="modern-table">
                        <thead>
                            <tr>
                                <th>HỌ TÊN</th>
                                <th>EMAIL</th>
                                <th width="100" class="text-center">HÀNH ĐỘNG</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $res = mysqli_query($link, "SELECT t.*, u.full_name, u.id as uid FROM teachers t JOIN users u ON t.user_id=u.id ORDER BY u.id DESC");
                            
                            if(mysqli_num_rows($res) > 0):
                                while($r = mysqli_fetch_assoc($res)): 
                                    $initial = strtoupper(substr($r['full_name'], 0, 1));
                            ?>
                                <tr>
                                    <td>
                                        <div class="teacher-name-cell">
                                            <div class="teacher-avatar"><?php echo $initial; ?></div>
                                            <div>
                                                <div class="teacher-name">
                                                    <?php echo htmlspecialchars($r['full_name']); ?>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="teacher-email">
                                            <?php echo htmlspecialchars($r['email']); ?>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <a href="?del=<?php echo $r['uid']; ?>" 
                                           onclick="return confirm('Xóa giáo viên này?')" 
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
                                    <td colspan="3" class="empty-state">
                                        <i class="fa-solid fa-user-slash"></i>
                                        <p>Chưa có giáo viên nào</p>
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