<?php
include "connection.php";
session_start();
if(!isset($_SESSION['username'])){ header('location:login.php'); }

$id = isset($_GET["id"]) ? intval($_GET["id"]) : 0;

// Lấy thông tin học sinh
$res = mysqli_query($link,"SELECT * FROM students WHERE id=$id");
$std = mysqli_fetch_assoc($res);
if (!$std) { header("Location: manage_students.php"); exit; }

// Lấy danh sách lớp để hiển thị dropdown
$classes = [];
$q_class = mysqli_query($link, "SELECT * FROM classes");
if ($q_class) {
    while($c = mysqli_fetch_assoc($q_class)){
        $classes[] = $c;
    }
}

// Xử lý Cập nhật
if(isset($_POST["update"]))
{
    $code = mysqli_real_escape_string($link, $_POST['student_id_code']);
    $name = mysqli_real_escape_string($link, $_POST['full_name']);
    $email = mysqli_real_escape_string($link, $_POST['email']);
    $class_id = isset($_POST['class_id']) ? intval($_POST['class_id']) : 0;
    
    // Cập nhật cả class_id và class_name (để tương thích ngược)
    $class_name = "";
    if($class_id > 0) {
        $r_c = mysqli_fetch_assoc(mysqli_query($link, "SELECT name FROM classes WHERE id=$class_id"));
        if($r_c) $class_name = $r_c['name'];
    }

    $sql = "UPDATE students SET 
            student_id_code='$code', 
            full_name='$name', 
            email='$email', 
            class_id=$class_id,
            class_name='$class_name'
            WHERE id=$id";

    mysqli_query($link, $sql) or die(mysqli_error($link));
    header("Location: manage_students.php"); 
    exit;
}
?>

<html lang="en">
<head>
    <title>Edit Student</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="dashboard_style.css">
</head>
<body>
<div class="main-container">
    <div class="content-box" style="max-width: 500px; margin: 0 auto;">
        <h2 class="section-title">Edit Student</h2>
        <form action="" method="post">
             <div class="form-group">
                <label>Student ID:</label>
                <input type="text" class="form-control" name="student_id_code" value="<?php echo $std['student_id_code']; ?>" required>
            </div>
            <div class="form-group">
                <label>Full Name:</label>
                <input type="text" class="form-control" name="full_name" value="<?php echo $std['full_name']; ?>" required>
            </div>
            <div class="form-group">
                <label>Email:</label>
                <input type="email" class="form-control" name="email" value="<?php echo $std['email']; ?>">
            </div>
            <div class="form-group">
                <label>Class:</label>
                <select name="class_id" class="form-control">
                    <option value="0">-- Select Class --</option>
                    <?php foreach($classes as $c): ?>
                        <option value="<?php echo $c['id']; ?>" <?php if($std['class_id'] == $c['id']) echo 'selected'; ?>>
                            <?php echo htmlspecialchars($c['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <br>
            <button type="submit" name="update" class="btn btn-primary">Update Student</button>
            <a href="manage_students.php" class="btn btn-default">Cancel</a>
        </form>
    </div>
</div>
</body>
</html>