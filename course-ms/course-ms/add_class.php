<?php
include "connection.php";
include "auth.php";
requireRole(['admin']);

// Lấy danh sách giáo viên từ DB để hiển thị trong Dropdown
$teachers = [];
// Join teachers với users để lấy tên thật
$q_t = mysqli_query($link, "SELECT t.id, u.full_name FROM teachers t JOIN users u ON t.user_id=u.id ORDER BY u.full_name ASC");
while($r = mysqli_fetch_assoc($q_t)) {
    $teachers[] = $r;
}

$error = "";

if(isset($_POST['create_class'])){
    $name = mysqli_real_escape_string($link, $_POST['class_name']);
    $limit = intval($_POST['student_limit']);
    $tid = intval($_POST['teacher_id']);
    
    // Nếu chọn "-- Select --" (value=0) thì lưu là NULL
    $teacher_sql = ($tid > 0) ? $tid : "NULL";

    if($name == ''){
        $error = "Class Name is required!";
    } else {
        // === KIỂM TRA TÊN LỚP ĐÃ TỒN TẠI CHƯA ===
        $check_sql = "SELECT id FROM classes WHERE name = '$name'";
        $check_result = mysqli_query($link, $check_sql);
        
        if(mysqli_num_rows($check_result) > 0){
            $error = "Tên lớp '$name' đã tồn tại! Vui lòng chọn tên khác.";
        } else {
            // === TỰ ĐỘNG TẠO MÃ LỚP ===
            // Lấy ID lớn nhất hiện tại
            $result = mysqli_query($link, "SELECT MAX(id) as max_id FROM classes");
            $row = mysqli_fetch_assoc($result);
            $next_id = ($row['max_id'] ? $row['max_id'] : 0) + 1;
            
            // Tạo mã lớp: CLS0001, CLS0002, CLS0003...
            $class_code = 'CLS' . str_pad($next_id, 4, '0', STR_PAD_LEFT);
            
            // Insert với mã lớp tự động (bỏ description)
            $sql = "INSERT INTO classes (class_code, name, student_limit, teacher_id) 
                    VALUES ('$class_code', '$name', $limit, $teacher_sql)";
            
            if(mysqli_query($link, $sql)){
                echo "<script>alert('Tạo lớp thành công! Mã lớp: $class_code'); window.location='manage_classes.php';</script>";
                exit;
            } else {
                $error = "Database Error: " . mysqli_error($link);
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Tạo lớp | Teacher Bee</title>
    <link rel="stylesheet" href="https://site-assets.fontawesome.com/releases/v6.4.2/css/all.css">
    <link rel="stylesheet" href="dashboard_style.css">
    <style>
        body { background-color: #FFFDF7; font-family: 'Be Vietnam Pro', sans-serif; }
        .white-card { background: #FFFFFF; border-radius: 20px; padding: 40px; box-shadow: 0 4px 20px -2px rgba(0,0,0,0.05); max-width: 600px; margin: 0 auto; }
        .form-label { font-weight: 600; color: #1E293B; margin-bottom: 8px; display: block; font-size: 14px; }
        .form-control { width: 100%; padding: 12px; border: 1px solid #E2E8F0; border-radius: 10px; font-size: 14px; outline: none; transition: 0.2s; }
        .form-control:focus { border-color: #F59E0B; box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.1); }
        .btn-submit { background-color: #F59E0B; color: white; padding: 12px 25px; border-radius: 10px; font-weight: 700; border: none; cursor: pointer; }
        .btn-cancel { background-color: #F1F5F9; color: #64748B; padding: 12px 25px; border-radius: 10px; font-weight: 600; text-decoration: none; margin-right: 10px; }
        
        /* Info Box - Hiển thị mã lớp sẽ được tạo */
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
        .info-box-text { color: #0C4A6E; font-size: 14px; font-weight: 600; }
        .code-preview {
            background: white;
            padding: 5px 12px;
            border-radius: 6px;
            font-family: 'Courier New', monospace;
            font-weight: 700;
            color: #0369A1;
            border: 1px solid #BAE6FD;
        }
    </style>
</head>
<body>
    <?php include "includes/sidebar.php"; ?>
    <div class="main-wrapper">
        <?php include "includes/topbar.php"; ?>
        
        <div class="content-scroll" style="padding: 40px;">
            <div class="white-card">
                <h3 style="margin-top:0; color: #F59E0B; margin-bottom: 20px; font-size: 22px;">
                    <i class="fa-solid fa-circle-plus"></i> Tạo lớp mới
                </h3>
                
                <!-- Thông báo mã lớp tự động -->
                <div class="info-box">
                    <i class="fa-solid fa-circle-info"></i>
                    <div class="info-box-text">
                        Mã lớp sẽ được tạo tự động: 
                        <span class="code-preview">
                            <?php 
                                // Preview mã lớp sẽ được tạo
                                $result = mysqli_query($link, "SELECT MAX(id) as max_id FROM classes");
                                $row = mysqli_fetch_assoc($result);
                                $next_id = ($row['max_id'] ? $row['max_id'] : 0) + 1;
                                echo 'CLS' . str_pad($next_id, 4, '0', STR_PAD_LEFT);
                            ?>
                        </span>
                    </div>
                </div>
                
                <?php if($error): ?>
                    <div style="background:#FEF2F2; color:#DC2626; padding:10px; border-radius:8px; margin-bottom:15px; font-size:14px;">
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>

                <form method="post">
                    <div style="margin-bottom: 20px;">
                        <label class="form-label">Tên lớp <span style="color:red">*</span></label>
                        <input type="text" name="class_name" class="form-control" required placeholder="Ví dụ: Toán Học 101">
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        <div style="margin-bottom: 20px;">
                            <label class="form-label">Giới hạn học sinh </label>
                            <input type="number" name="student_limit" class="form-control" value="40" min="1">
                        </div>

                        <div style="margin-bottom: 20px;">
                            <label class="form-label">Chọn Giáo viên </label>
                            <select name="teacher_id" class="form-control">
                                <option value="0">-- Chọn --</option>
                                <?php foreach($teachers as $t): ?>
                                    <option value="<?php echo $t['id']; ?>">
                                        <?php echo htmlspecialchars($t['full_name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div style="margin-top: 30px; text-align: right;">
                        <a href="manage_classes.php" class="btn-cancel">Hủy</a>
                        <button type="submit" name="create_class" class="btn-submit">
                            <i class="fa-solid fa-check"></i> Lưu lớp học
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>