<?php
include "connection.php";
session_start();
if(!isset($_SESSION['username'])){ header('location:login.php'); }

$id = isset($_GET["id"]) ? intval($_GET["id"]) : 0;

// Lấy thông tin cũ
$res = mysqli_query($link,"SELECT * FROM exams WHERE id=$id");
$exam = mysqli_fetch_assoc($res);
if(!$exam) { header("Location: manage_exams.php"); exit; }

// Lấy danh sách lớp
$classes = [];
$q_class = mysqli_query($link, "SELECT * FROM classes");
if($q_class) { while($c = mysqli_fetch_assoc($q_class)) $classes[] = $c; }

// Xử lý Cập nhật
if(isset($_POST["update"]))
{
    $title = mysqli_real_escape_string($link, $_POST['exam_title']);
    $subject = mysqli_real_escape_string($link, $_POST['subject']);
    $date = $_POST['exam_date'];
    $class_id = isset($_POST['class_id']) ? intval($_POST['class_id']) : 0;
    $sql = "UPDATE exams SET exam_title='$title', subject='$subject', exam_date='$date', class_id=$class_id WHERE id=$id";
    if(mysqli_query($link, $sql)) { header("Location: manage_exams.php"); exit; } 
    else { die("Lỗi SQL: " . mysqli_error($link)); }
}
?>

<!DOCTYPE html>
<html lang="en">
<link rel="stylesheet" href="https://site-assets.fontawesome.com/releases/v6.4.2/css/all.css">
<head>
    <title>Edit Exam</title>
    <link rel="stylesheet" href="dashboard_style.css">
    <style>
        body { background-color: #F8FAFC; font-family: 'Segoe UI', sans-serif; }
        .card { background: white; border-radius: 16px; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); border: 1px solid #E2E8F0; padding:0; overflow:hidden; }
        .card-header { background: #FFF; padding: 20px 30px; border-bottom: 1px solid #F1F5F9; }
        .card-title { margin:0; font-size: 18px; color: #1E293B; font-weight: 700; display:flex; align-items:center; gap:10px; }
        .form-content { padding: 30px; }
        .form-label { font-weight: 600; color: #475569; margin-bottom: 8px; display: block; font-size: 14px; }
        .form-control { width: 100%; padding: 12px; border: 1px solid #CBD5E1; border-radius: 8px; background: #F8FAFC; margin-bottom: 20px; box-sizing: border-box; }
        .form-control:focus { border-color: #F59E0B; background: #FFF; outline: none; }
        .btn-primary { padding: 12px 24px; background: #F59E0B; color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; transition: 0.2s; }
        .btn-primary:hover { background: #D97706; }
        .btn-secondary { padding: 12px 24px; background: #F1F5F9; color: #64748B; text-decoration: none; border-radius: 8px; font-weight: 600; transition: 0.2s; display: inline-block; }
        .btn-secondary:hover { background: #E2E8F0; color: #475569; }
    </style>
</head>
<body>

    <?php include "includes/sidebar.php"; ?>

    <div class="main-wrapper">
        <?php include "includes/topbar.php"; ?>

        <div class="content-scroll">
            <div class="card" style="max-width: 550px; margin: 40px auto;">
                <div class="card-header">
                    <h3 class="card-title"><i class="fa-solid fa-pen-to-square" style="color:#F59E0B"></i> Cập nhật Bài kiểm tra</h3>
                </div>
                
                <div class="form-content">
                    <form action="" method="post">
                        <div>
                            <label class="form-label">Tên bài kiểm tra</label>
                            <input type="text" class="form-control" name="exam_title" value="<?php echo htmlspecialchars($exam['exam_title']); ?>" required>
                        </div>
                        <div>
                            <label class="form-label">Môn học</label>
                            <input type="text" class="form-control" name="subject" value="<?php echo htmlspecialchars($exam['subject']); ?>" required>
                        </div>
                        <div>
                            <label class="form-label">Ngày thi</label>
                            <input type="date" class="form-control" name="exam_date" value="<?php echo $exam['exam_date']; ?>">
                        </div>
                        <div>
                            <label class="form-label">Lớp học</label>
                            <select name="class_id" class="form-control">
                                <option value="0">-- Chọn lớp --</option>
                                <?php foreach($classes as $c): ?>
                                    <option value="<?php echo $c['id']; ?>" <?php if($exam['class_id'] == $c['id']) echo 'selected'; ?>>
                                        <?php echo htmlspecialchars($c['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div style="display:flex; gap:12px; margin-top:10px;">
                            <button type="submit" name="update" class="btn-primary">Lưu thay đổi</button>
                            <a href="manage_exams.php" class="btn-secondary">Hủy bỏ</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>