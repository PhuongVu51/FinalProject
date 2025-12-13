<?php
include "connection.php";
session_start();
if(!isset($_SESSION['username'])){ header('location:login.php'); }

$id = isset($_GET["id"]) ? intval($_GET["id"]) : 0;

// Lấy thông tin học sinh
$res = mysqli_query($link,"SELECT * FROM students WHERE id=$id");
$std = mysqli_fetch_assoc($res);
if(!$std) { header("Location: manage_students.php"); exit; }

// Lấy danh sách lớp
$classes = [];
$q_class = mysqli_query($link, "SELECT * FROM classes");
if($q_class) { while($c = mysqli_fetch_assoc($q_class)) $classes[] = $c; }

// Xử lý cập nhật
if(isset($_POST["update"])) {
    $code = mysqli_real_escape_string($link, $_POST['student_id_code']);
    $name = mysqli_real_escape_string($link, $_POST['full_name']);
    $email = mysqli_real_escape_string($link, $_POST['email']);
    $class_id = isset($_POST['class_id']) ? intval($_POST['class_id']) : 0;
    
    $class_name = "";
    if ($class_id > 0) {
        $res_c = mysqli_query($link, "SELECT name FROM classes WHERE id=$class_id");
        if ($row_c = mysqli_fetch_assoc($res_c)) $class_name = $row_c['name'];
    }

    $sql = "UPDATE students SET 
            student_id_code='$code', 
            full_name='$name', 
            email='$email', 
            class_id=$class_id, 
            class_name='$class_name' 
            WHERE id=$id";

    if(mysqli_query($link, $sql)) { header("Location: manage_students.php"); exit; }
    else { die("Lỗi SQL: " . mysqli_error($link)); }
}
?>

<!DOCTYPE html>
<html lang="en">
<link rel="stylesheet" href="https://site-assets.fontawesome.com/releases/v6.4.2/css/all.css">
<head>
    <title>Edit Student | CourseMS Pro</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="dashboard_style.css">
</head>
<body>
    <div class="app-container"> <?php include "includes/sidebar.php"; ?>

        <div class="main-content">
            <?php include "includes/topbar.php"; ?>

            <div class="content-body">
                <div class="card" style="max-width: 600px; margin: 0 auto;">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fa-solid fa-user-pen" style="color:var(--primary)"></i> Edit Student
                        </h3>
                        <a href="manage_students.php" class="btn-primary" style="background:#f1f5f9; color:#475569; padding:5px 10px; font-size:12px;">Back</a>
                    </div>
                    
                    <form action="" method="post">
                         <div class="form-group">
                            <label class="form-label">Student ID</label>
                            <input type="text" class="form-control" name="student_id_code" value="<?php echo htmlspecialchars($std['student_id_code']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Full Name</label>
                            <input type="text" class="form-control" name="full_name" value="<?php echo htmlspecialchars($std['full_name']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($std['email']); ?>">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Class</label>
                            <select name="class_id" class="form-control">
                                <option value="0">-- Select Class --</option>
                                <?php foreach($classes as $c): ?>
                                    <option value="<?php echo $c['id']; ?>" <?php if($std['class_id'] == $c['id']) echo 'selected'; ?>>
                                        <?php echo htmlspecialchars($c['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div style="margin-top: 20px; text-align: right;">
                            <button type="submit" name="update" class="btn-primary">Update Student</button>
                        </div>
                    </form>
                </div>
            </div>

            <?php include "includes/footer.php"; ?>
        </div>
    </div>
</body>
</html>