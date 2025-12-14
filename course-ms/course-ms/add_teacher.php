<?php
include "connection.php";
include "auth.php";
requireRole(['admin']);

// Lấy danh sách môn học
$subjects = [];
$q_subjects = mysqli_query($link, "SELECT * FROM subjects ORDER BY name ASC");
while($s = mysqli_fetch_assoc($q_subjects)) {
    $subjects[] = $s;
}

$error = "";

if(isset($_POST['add_teacher'])){
    $name = mysqli_real_escape_string($link, $_POST['full_name']);
    $email = mysqli_real_escape_string($link, $_POST['email']);
    $pass = md5($_POST['password']);
    $subject_id = intval($_POST['subject_id']);
    
    // Kiểm tra email đã tồn tại chưa
    $check = mysqli_query($link, "SELECT id FROM users WHERE username='$email'");
    if(mysqli_num_rows($check) > 0) {
        $error = "Email này đã được sử dụng!";
    } else {
        // Tạo user account
        $user_sql = "INSERT INTO users (username, password, role, full_name) 
                     VALUES ('$email', '$pass', 'teacher', '$name')";
        
        if(mysqli_query($link, $user_sql)){
            $user_id = mysqli_insert_id($link);
            
            // Tạo mã giáo viên tự động
            $result = mysqli_query($link, "SELECT MAX(id) as max_id FROM teachers");
            $row = mysqli_fetch_assoc($result);
            $next_id = ($row['max_id'] ? $row['max_id'] : 0) + 1;
            $teacher_code = 'TEA' . str_pad($next_id, 4, '0', STR_PAD_LEFT);
            
            // Insert vào bảng teachers
            $teacher_sql = "INSERT INTO teachers (teacher_code, user_id, email, subject_id) 
                           VALUES ('$teacher_code', $user_id, '$email', $subject_id)";
            
            if(mysqli_query($link, $teacher_sql)){
                echo "<script>alert('Thêm giáo viên thành công! Mã GV: $teacher_code'); window.location='manage_teachers.php';</script>";
                exit;
            } else {
                $error = "Lỗi khi tạo giáo viên: " . mysqli_error($link);
            }
        } else {
            $error = "Lỗi khi tạo tài khoản: " . mysqli_error($link);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thêm Giáo Viên | Teacher Bee</title>
    <link rel="stylesheet" href="https://site-assets.fontawesome.com/releases/v6.4.2/css/all.css">
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="dashboard_style.css">
    <style>
        body { background-color: #FFFDF7; font-family: 'Be Vietnam Pro', sans-serif; }
        
        .page-header {
            display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;
        }
        .page-header h2 { font-size: 24px; font-weight: 600; color: #1E293B; margin: 0; }
        
        .back-link { 
            display: inline-block; margin-bottom: 15px; font-size: 15px; 
            color: #64748B; text-decoration: none; font-weight: 500; 
        }
        .back-link:hover { color: #F59E0B; }

        .white-card { 
            background: #FFFFFF; border-radius: 20px; padding: 40px; 
            box-shadow: 0 4px 20px -2px rgba(0,0,0,0.05); 
            max-width: 700px; margin: 0 auto; 
        }
        
        .info-box {
            background: #F0F9FF; 
            border: 1px solid #BAE6FD; 
            border-radius: 10px; 
            padding: 15px; 
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .info-box i { color: #0369A1; font-size: 18px; }
        .info-box-text { color: #0C4A6E; font-size: 14px; font-weight: 500; }
        .code-preview {
            background: white;
            padding: 5px 12px;
            border-radius: 6px;
            font-family: 'Courier New', monospace;
            font-weight: 600;
            color: #0369A1;
            border: 1px solid #BAE6FD;
        }
        
        .form-label { 
            font-weight: 600; color: #1E293B; margin-bottom: 8px; 
            display: block; font-size: 14px; 
        }
        .form-control { 
            width: 100%; padding: 12px; border: 1px solid #E2E8F0; 
            border-radius: 10px; font-size: 14px; outline: none; 
            transition: 0.2s; 
        }
        .form-control:focus { 
            border-color: #F59E0B; 
            box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.1); 
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .btn-submit { 
            background-color: #F59E0B; color: white; padding: 12px 25px; 
            border-radius: 10px; font-weight: 600; border: none; cursor: pointer; 
        }
        .btn-cancel { 
            background-color: #F1F5F9; color: #64748B; padding: 12px 25px; 
            border-radius: 10px; font-weight: 500; text-decoration: none; 
            margin-right: 10px; 
        }
        
        .error-alert {
            background: #FEF2F2; color: #DC2626; padding: 12px 16px;
            border-radius: 8px; margin-bottom: 20px; font-size: 14px;
        }
    </style>
</head>
<body>
    <?php include "includes/sidebar.php"; ?>
    
    <div class="main-wrapper">
        <?php include "includes/topbar.php"; ?>
        
        <div class="content-scroll" style="padding: 30px;">
            
            <div class="page-header">
                <h2 style="font-weight: 600;">Thêm Giáo Viên Mới</h2>
            </div>
            
            <a href="manage_teachers.php" class="back-link">
                &lt; Quay lại danh sách
            </a>

            <div class="white-card">
                <h3 style="margin-top:0; color: #F59E0B; margin-bottom: 20px; font-size: 22px;">
                    <i class="fa-solid fa-user-plus"></i> Thông tin giáo viên
                </h3>
                
                <!-- Thông báo mã giáo viên tự động -->
                <div class="info-box">
                    <i class="fa-solid fa-circle-info"></i>
                    <div class="info-box-text">
                        Mã giáo viên sẽ được tạo tự động: 
                        <span class="code-preview">
                            <?php 
                                $result = mysqli_query($link, "SELECT MAX(id) as max_id FROM teachers");
                                $row = mysqli_fetch_assoc($result);
                                $next_id = ($row['max_id'] ? $row['max_id'] : 0) + 1;
                                echo 'TEA' . str_pad($next_id, 4, '0', STR_PAD_LEFT);
                            ?>
                        </span>
                    </div>
                </div>
                
                <?php if($error): ?>
                    <div class="error-alert">
                        <i class="fa-solid fa-circle-exclamation"></i> <?php echo $error; ?>
                    </div>
                <?php endif; ?>

                <form method="post">
                    <div style="margin-bottom: 20px;">
                        <label class="form-label">Họ và Tên <span style="color:red">*</span></label>
                        <input type="text" name="full_name" class="form-control" 
                               required placeholder="Nguyễn Văn A">
                    </div>

                    <div class="form-row">
                        <div>
                            <label class="form-label">Email <span style="color:red">*</span></label>
                            <input type="email" name="email" class="form-control" 
                                   required placeholder="teacher@school.com">
                        </div>
                        
                        <div>
                            <label class="form-label">Mật khẩu <span style="color:red">*</span></label>
                            <input type="password" name="password" class="form-control" 
                                   required placeholder="••••••">
                        </div>
                    </div>

                    <div style="margin-bottom: 20px;">
                        <label class="form-label">Môn học <span style="color:red">*</span></label>
                        <select name="subject_id" class="form-control" required>
                            <option value="">-- Chọn môn học --</option>
                            <?php foreach($subjects as $subj): ?>
                                <option value="<?php echo $subj['id']; ?>">
                                    <?php echo htmlspecialchars($subj['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div style="margin-top: 30px; text-align: right;">
                        <a href="manage_teachers.php" class="btn-cancel">Hủy</a>
                        <button type="submit" name="add_teacher" class="btn-submit">
                            <i class="fa-solid fa-check"></i> Lưu giáo viên
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</body>
</html>