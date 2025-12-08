<?php
include "connection.php"; 
session_start();
if(!isset($_SESSION['username'])){ header('location:login.php'); }

// --- HÀM TỰ ĐỘNG TẠO MÃ SINH VIÊN (SỐ THỨ TỰ 1, 2, 3...) ---
function generateStudentID($link) {
    // Lấy giá trị số lớn nhất hiện tại trong cột student_id_code
    // CAST(... AS UNSIGNED) giúp chuyển đổi chuỗi thành số để so sánh đúng (ví dụ "10" > "2")
    $query = "SELECT MAX(CAST(student_id_code AS UNSIGNED)) as max_id FROM students";
    $result = mysqli_query($link, $query);
    $row = mysqli_fetch_assoc($result);
    
    $max_id = $row['max_id'];
    
    // Nếu chưa có học sinh nào, bắt đầu từ 1. Nếu có rồi thì +1
    if ($max_id) {
        return $max_id + 1;
    } else {
        return 1;
    }
}

// --- LẤY DANH SÁCH LỚP HỌC ---
$classes = [];
$check_table = mysqli_query($link, "SHOW TABLES LIKE 'classes'");
if (mysqli_num_rows($check_table) > 0) {
    $q_class = mysqli_query($link, "SELECT * FROM classes");
    while($c = mysqli_fetch_assoc($q_class)){
        $classes[] = $c;
    }
}

// Xử lý Thêm mới
if(isset($_POST["insert"]))
{
    // Nếu ô ID bị ẩn/trống thì tự tạo, nếu có dữ liệu thì lấy dữ liệu đó
    $code = !empty($_POST['student_id_code']) ? mysqli_real_escape_string($link, $_POST['student_id_code']) : generateStudentID($link);
    $name = mysqli_real_escape_string($link, $_POST['full_name']);
    $email = mysqli_real_escape_string($link, $_POST['email']);
    
    $class_id = isset($_POST['class_id']) ? intval($_POST['class_id']) : 0;
    $class_name = "";
    
    if ($class_id > 0) {
        $res_c = mysqli_query($link, "SELECT name FROM classes WHERE id=$class_id");
        if ($row_c = mysqli_fetch_assoc($res_c)) {
            $class_name = $row_c['name'];
        }
    }

    // Kiểm tra cấu trúc bảng để insert đúng
    $check_col = mysqli_query($link, "SHOW COLUMNS FROM students LIKE 'class_id'");
    if (mysqli_num_rows($check_col) > 0) {
        $sql = "INSERT INTO students (student_id_code, full_name, email, class_id, class_name) 
                VALUES ('$code', '$name', '$email', $class_id, '$class_name')";
    } else {
        $sql = "INSERT INTO students (student_id_code, full_name, email, class_name) 
                VALUES ('$code', '$name', '$email', '$class_name')";
    }

    if(mysqli_query($link, $sql)) {
        header("Location: manage_students.php");
        exit;
    } else {
        die("Lỗi: " . mysqli_error($link));
    }
}
?>

<html lang="en">
<head>
    <title>Manage Students</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="dashboard_style.css">
</head>
<body>
    <nav class="header-nav"><a href="home.php" class="logo">Teacher Bee 🐝</a><a href="logout.php" class="logout-btn">Logout</a></nav>
    <div class="main-container"><div class="content-box">
        <a href="home.php" class="btn btn-info" style="margin-bottom: 20px;">⬅️ Back to Dashboard</a>
        <h1 class="page-title">Manage Students</h1>
        <div class="crud-container">
            <div class="form-container">
                <h2 class="section-title">Add New Student</h2>
                <form action="" method="post">
                    <div class="form-group">
                        <label>Student ID (Auto):</label>
                        <input type="text" class="form-control" name="student_id_code" 
                               value="<?php echo generateStudentID($link); ?>" readonly 
                               style="background-color: #eee; font-weight:bold;">
                    </div>
                    <div class="form-group">
                        <label>Full Name:</label>
                        <input type="text" class="form-control" name="full_name" required>
                    </div>
                    <div class="form-group">
                        <label>Email:</label>
                        <input type="email" class="form-control" name="email" required>
                    </div>
                    <div class="form-group">
                        <label>Class:</label>
                        <?php if(!empty($classes)): ?>
                            <select name="class_id" class="form-control" required>
                                <option value="">-- Select Class --</option>
                                <?php foreach($classes as $c): ?>
                                    <option value="<?php echo $c['id']; ?>"><?php echo htmlspecialchars($c['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        <?php else: ?>
                            <input type="text" name="class_name" class="form-control" placeholder="Enter class name">
                        <?php endif; ?>
                    </div>
                    <button type="submit" name="insert" class="btn btn-primary">Add Student</button>
                </form>
            </div>
            <div class="table-container">
                <h2 class="section-title">Student List</h2>
                <table class="table table-bordered table-striped">
                    <thead><tr><th>ID</th><th>Name</th><th>Email</th><th>Class</th><th>Action</th></tr></thead>
                    <tbody>
                    <?php
                    $q = "SELECT s.*, c.name as c_name FROM students s LEFT JOIN classes c ON s.class_id=c.id ORDER BY CAST(s.student_id_code AS UNSIGNED) DESC";
                    $res = mysqli_query($link, $q);
                    if($res) {
                        while($row=mysqli_fetch_assoc($res)){
                            $cls = !empty($row['c_name']) ? $row['c_name'] : $row['class_name'];
                            echo "<tr>
                                <td><b>{$row['student_id_code']}</b></td>
                                <td>{$row['full_name']}</td>
                                <td>{$row['email']}</td>
                                <td>$cls</td>
                                <td>
                                    <a href='edit_student.php?id={$row['id']}' class='btn btn-success btn-sm'>Edit</a>
                                    <a href='delete_student.php?id={$row['id']}' class='btn btn-danger btn-sm'>Delete</a>
                                </td>
                            </tr>";
                        }
                    }
                    ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div></div>
</body>
</html>