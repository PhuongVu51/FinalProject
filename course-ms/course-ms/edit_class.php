<?php
include "connection.php";
include "auth.php";
requireRole(['admin']); // Chỉ Admin mới được sửa

// 1. Kiểm tra ID lớp hợp lệ
if(isset($_GET['id'])){
    $id = intval($_GET['id']);
    $sql = "SELECT * FROM classes WHERE id=$id";
    $result = mysqli_query($link, $sql);
    $row = mysqli_fetch_assoc($result);
    
    if(!$row){
        header("Location: manage_classes.php");
        exit;
    }
} else {
    header("Location: manage_classes.php");
    exit;
}

// 2. Xử lý khi bấm nút "Lưu Thay Đổi"
if(isset($_POST['save_edit'])){
    $name = mysqli_real_escape_string($link, $_POST['class_name']);
    $desc = mysqli_real_escape_string($link, $_POST['description']);
    $limit = intval($_POST['limit']);
    $teacher_id = intval($_POST['teacher_id']);

    $update_sql = "UPDATE classes SET 
                   name='$name', 
                   description='$desc', 
                   student_limit=$limit, 
                   teacher_id=$teacher_id 
                   WHERE id=$id";

    if(mysqli_query($link, $update_sql)){
        echo "<script>alert('Đã cập nhật lớp học thành công!'); window.location='manage_classes.php';</script>";
    } else {
        echo "<script>alert('Lỗi: " . mysqli_error($link) . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Sửa Lớp: <?php echo htmlspecialchars($row['name']); ?> | Teacher Bee</title>
    
    <link rel="stylesheet" href="dashboard_style.css?v=2">
    <link rel="stylesheet" href="https://site-assets.fontawesome.com/releases/v6.4.2/css/all.css">
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body { background-color: #FFFDF7; font-family: 'Be Vietnam Pro', sans-serif; }
        
        /* Fix Topbar Layout */
        .topbar {
            display: flex !important;
            justify-content: space-between !important;
            align-items: center !important;
        }
        
        /* Topbar Logo & Title */
        .topbar-left {
            display: flex; align-items: center; gap: 12px;
        }
        .topbar-logo {
            font-size: 24px; color: #F59E0B; 
            filter: drop-shadow(0 2px 4px rgba(245, 158, 11, 0.3));
        }
        .topbar-title {
            font-size: 16px; font-weight: 600; color: #1E293B;
            letter-spacing: -0.3px;
        }
        
        /* User Box */
        .user-box {
            display: flex; align-items: center; gap: 15px;
        }
        .user-info {
            text-align: right;
        }
        .user-name {
            margin: 0; font-size: 14px; font-weight: 700;
            color: #1E293B; line-height: 1.3;
        }
        .user-role {
            margin: 0; font-size: 12px; font-weight: 500;
            color: #F59E0B; text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .user-avatar {
            width: 42px; height: 42px; border-radius: 50%;
            background: linear-gradient(135deg, #F59E0B 0%, #FBBF24 100%);
            display: flex; align-items: center; justify-content: center;
            color: white; font-weight: 700; font-size: 16px;
            box-shadow: 0 4px 10px rgba(245, 158, 11, 0.3);
            border: 3px solid white;
        }
        
        /* Page Header Style - giống manage_classes.php */
        .page-header {
            display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;
        }
        .page-header h2 { font-size: 24px; font-weight: 700; color: #1E293B; margin: 0; }
        
        /* Back Link - giống manage_classes.php */
        .back-link { 
            display: inline-block; margin-bottom: 15px; font-size: 15px; 
            color: #64748B; text-decoration: none; font-weight: 600; 
        }
        .back-link:hover { color: #F59E0B; }

        /* Card Style - giống manage_classes.php */
        .white-card {
            background: #FFFFFF;
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.05);
            border: none;
        }

        .card-top-row {
            display: flex; justify-content: space-between; align-items: center; 
            margin-bottom: 25px; padding-bottom: 20px;
            border-bottom: 1px solid #F1F5F9;
        }
        .white-card h3 { font-size: 18px; font-weight: 700; color: #1E293B; margin: 0; }

        /* Form Elements */
        .form-group { margin-bottom: 20px; }
        
        .form-label {
            display: block; font-weight: 600; margin-bottom: 8px; 
            color: #1E293B; font-size: 14px;
        }
        .form-label .required { color: #DC2626; margin-left: 3px; }

        input[type="text"], 
        input[type="number"], 
        select, 
        textarea {
            width: 100%; padding: 12px 15px;
            border: 1px solid #E2E8F0; border-radius: 8px;
            background: #FFFFFF; font-family: 'Be Vietnam Pro', sans-serif;
            font-size: 14px; color: #1E293B; transition: 0.2s;
            outline: none;
        }
        
        input:focus, select:focus, textarea:focus {
            border-color: #F59E0B;
            box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.15);
        }

        textarea {
            resize: vertical; min-height: 100px;
            font-family: 'Be Vietnam Pro', sans-serif;
        }

        .form-row {
            display: grid; grid-template-columns: 1fr 1fr; 
            gap: 20px; margin-bottom: 20px;
        }

        /* Buttons - giống manage_classes.php */
        .btn-create {
            background-color: #F59E0B; color: white; text-decoration: none; 
            padding: 10px 20px; border-radius: 10px; font-weight: 600; 
            font-size: 14px; box-shadow: 0 4px 6px -1px rgba(245, 158, 11, 0.2); 
            transition: 0.2s; border: none; cursor: pointer;
            display: inline-flex; align-items: center; gap: 8px;
        }
        .btn-create:hover { 
            background-color: #D97706; transform: translateY(-1px); 
        }

        .btn-secondary {
            background: #F8FAFC; color: #64748B;
            border: 1px solid #E2E8F0; border-radius: 8px;
            padding: 10px 20px; text-decoration: none; 
            font-weight: 600; font-size: 14px;
            transition: 0.2s; display: inline-block;
        }
        .btn-secondary:hover { background: #E2E8F0; color: #1E293B; }

        .form-actions {
            display: flex; justify-content: flex-end; gap: 10px;
            margin-top: 30px; padding-top: 20px;
            border-top: 1px solid #F1F5F9;
        }
    </style>
</head>
<body>

    <?php include "includes/sidebar.php"; ?>

    <div class="main-wrapper">
        <?php include "includes/topbar.php"; ?>

        <div class="content-scroll" style="padding: 30px;">
            
            <!-- Page Header giống manage_classes.php -->
            <div class="page-header">
                <h2>Chỉnh sửa thông tin lớp</h2>
            </div>
            
            <!-- Back Link giống manage_classes.php -->
            <a href="manage_classes.php" class="back-link">
                &lt; Quay lại danh sách
            </a>

            <!-- White Card giống manage_classes.php -->
            <div class="white-card" style="max-width: 900px;">
                
                <!-- Card Header -->
                <div class="card-top-row">
                    <h3>Thông tin lớp học</h3>
                </div>

                <!-- Form Content -->
                <form method="POST">
                    
                    <!-- Tên Lớp -->
                    <div class="form-group">
                        <label class="form-label">
                            Tên Lớp Học <span class="required">*</span>
                        </label>
                        <input type="text" name="class_name" 
                               value="<?php echo htmlspecialchars($row['name']); ?>" 
                               required placeholder="Nhập tên lớp học">
                    </div>

                    <!-- Mô tả -->
                    <div class="form-group">
                        <label class="form-label">Mô tả chi tiết</label>
                        <textarea name="description" 
                                  placeholder="Nhập mô tả về lớp học..."><?php echo isset($row['description']) ? htmlspecialchars($row['description']) : ''; ?></textarea>
                    </div>

                    <!-- Row: Sĩ số & Giáo viên -->
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">
                                Sĩ số tối đa <span class="required">*</span>
                            </label>
                            <input type="number" name="limit" 
                                   value="<?php echo isset($row['student_limit']) ? $row['student_limit'] : 40; ?>" 
                                   required min="1" placeholder="Ví dụ: 40">
                        </div>

                        <div class="form-group">
                            <label class="form-label">
                                Giáo viên chủ nhiệm <span class="required">*</span>
                            </label>
                            <select name="teacher_id" required>
                                <option value="">-- Chọn Giáo Viên --</option>
                                <?php
                                $t_sql = "SELECT t.id, u.full_name FROM teachers t JOIN users u ON t.user_id = u.id";
                                $t_res = mysqli_query($link, $t_sql);
                                while($t = mysqli_fetch_assoc($t_res)){
                                    $selected = ($t['id'] == $row['teacher_id']) ? 'selected' : '';
                                    echo "<option value='".$t['id']."' $selected>".$t['full_name']."</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="form-actions">
                        <a href="manage_classes.php" class="btn-secondary">Hủy bỏ</a>
                        <button type="submit" name="save_edit" class="btn-create">
                            <i class="fa-solid fa-check"></i> Lưu Thay Đổi
                        </button>
                    </div>

                </form>
            </div>

        </div>
    </div>

</body>
</html>