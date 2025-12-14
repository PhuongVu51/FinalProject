<?php
include "connection.php";
include "auth.php";
requireRole(['admin']);

$id = intval($_GET['id']);
$class = mysqli_fetch_assoc(mysqli_query($link, "SELECT * FROM classes WHERE id=$id"));
if(!$class) header("Location: manage_classes.php");

if(isset($_POST['update'])){
    $name = mysqli_real_escape_string($link, $_POST['name']);
    $limit = intval($_POST['student_limit']);
    $tid = intval($_POST['teacher_id']);
    $t_sql = ($tid > 0) ? $tid : "NULL";
    mysqli_query($link, "UPDATE classes SET name='$name', student_limit=$limit, teacher_id=$t_sql WHERE id=$id");
    header("Location: manage_classes.php"); exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sửa Lớp | Teacher Bee</title>
    <link rel="stylesheet" href="https://site-assets.fontawesome.com/releases/v6.4.2/css/all.css">
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="dashboard_style.css">
    <style>
        body { 
            background-color: #FFFDF7; 
            font-family: 'Be Vietnam Pro', sans-serif; 
        }
        
        .edit-card {
            background: #FFFFFF;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.05);
            max-width: 600px;
            margin: 0 auto;
        }
        
        .edit-card h3 {
            font-size: 22px;
            font-weight: 700;
            color: #1E293B;
            margin: 0 0 25px 0;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            color: #475569;
            margin-bottom: 8px;
        }
        
        .form-control {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid #E2E8F0;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 500;
            color: #1E293B;
            background: #F8FAFC;
            transition: 0.2s;
        }
        
        .form-control:focus {
            border-color: #F59E0B;
            background: white;
            box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.1);
            outline: none;
        }
        
        .button-group {
            display: flex;
            gap: 12px;
            margin-top: 30px;
        }
        
        .btn-update {
            background: #F59E0B;
            color: white;
            padding: 12px 28px;
            border-radius: 10px;
            border: none;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn-update:hover {
            background: #D97706;
            transform: translateY(-1px);
        }
        
        .btn-cancel {
            background: #F1F5F9;
            color: #64748B;
            padding: 12px 28px;
            border-radius: 10px;
            border: none;
            font-size: 15px;
            font-weight: 600;
            text-decoration: none;
            transition: 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn-cancel:hover {
            background: #E2E8F0;
            color: #475569;
        }
    </style>
</head>
<body>
    <?php include "includes/sidebar.php"; ?>
    
    <div class="main-wrapper">
        <?php include "includes/topbar.php"; ?>
        
        <div class="content-scroll">
            <!-- WRAPPER CĂN GIỮA -->
            <div style="width: 100%; max-width: 600px; margin: 0 auto; padding: 40px 0;">
                
                <div class="edit-card">
                    <h3>
                        <i class="fa-solid fa-pen-to-square" style="color: #F59E0B;"></i>
                        Sửa Lớp: <?php echo htmlspecialchars($class['name']); ?>
                    </h3>
                    
                    <form method="post">
                        <div class="form-group">
                            <label>Tên Lớp <span style="color: #EF4444;">*</span></label>
                            <input type="text" name="name" value="<?php echo htmlspecialchars($class['name']); ?>" class="form-control" required>
                        </div>
                        
                        <div class="form-group">
                            <label>Giới hạn học sinh</label>
                            <input type="number" name="student_limit" value="<?php echo isset($class['student_limit']) ? $class['student_limit'] : 40; ?>" class="form-control" min="1">
                        </div>
                        
                        <div class="form-group">
                            <label>Giáo viên chủ nhiệm (GVCN)</label>
                            <select name="teacher_id" class="form-control">
                                <option value="0">-- Không gán --</option>
                                <?php 
                                $res = mysqli_query($link, "SELECT t.id, u.full_name FROM teachers t JOIN users u ON t.user_id=u.id ORDER BY u.full_name ASC");
                                while($r = mysqli_fetch_assoc($res)): ?>
                                    <option value="<?php echo $r['id']; ?>" <?php if($class['teacher_id']==$r['id']) echo 'selected'; ?>>
                                        <?php echo htmlspecialchars($r['full_name']); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        
                        <div class="button-group">
                            <button type="submit" name="update" class="btn-update">
                                <i class="fa-solid fa-check"></i>
                                Cập nhật
                            </button>
                            <a href="manage_classes.php" class="btn-cancel">
                                <i class="fa-solid fa-xmark"></i>
                                Hủy
                            </a>
                        </div>
                    </form>
                </div>
                
            </div>
            <!-- KẾT THÚC WRAPPER -->
        </div>
    </div>
</body>
</html>