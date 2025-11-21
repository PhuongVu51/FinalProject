<?php
include "connection.php"; 
session_start();
if(!isset($_SESSION['username'])){ header('location:login.php'); }

// Determine if classes table exists and load classes for the form
$classes_table_exists = false;
$classes = [];
$tbl_check = mysqli_query($link, "SHOW TABLES LIKE 'classes'");
if($tbl_check && mysqli_num_rows($tbl_check) > 0){
    $classes_table_exists = true;
    $teacher_id = isset($_SESSION['teacher_id']) ? intval($_SESSION['teacher_id']) : 0;
    // load classes for this teacher (if none, array will be empty)
    $stmt = mysqli_prepare($link, "SELECT id, name FROM classes WHERE teacher_id = ?");
    if($stmt){
        mysqli_stmt_bind_param($stmt, 'i', $teacher_id);
        mysqli_stmt_execute($stmt);
        $res_c = mysqli_stmt_get_result($stmt);
        while($r = mysqli_fetch_assoc($res_c)){
            $classes[] = $r;
        }
        mysqli_stmt_close($stmt);
    }
} else {
    // fallback: use distinct class_name values from students
    $res_c = mysqli_query($link, "SELECT DISTINCT class_name FROM students WHERE class_name IS NOT NULL AND class_name <> ''");
    while($r = mysqli_fetch_array($res_c)){
        $classes[] = ['id' => null, 'name' => $r['class_name']];
    }
}

// check whether students table has class_id column (used later when joining)
$has_class_id_col = false;
$col_check = mysqli_query($link, "SHOW COLUMNS FROM students LIKE 'class_id'");
if($col_check && mysqli_num_rows($col_check) > 0){
    $has_class_id_col = true;
}

// Xử lý Thêm mới (Create)
if(isset($_POST["insert"]))
{
    $student_id_code = mysqli_real_escape_string($link, $_POST['student_id_code']);
    $full_name = mysqli_real_escape_string($link, $_POST['full_name']);
    $email = mysqli_real_escape_string($link, $_POST['email']);
    $phone_number = mysqli_real_escape_string($link, $_POST['phone_number']);

    // determine class values based on schema
    $class_id = 0;
    $class_name = '';
    if($classes_table_exists){
        // expect class_id from select
        $class_id = isset($_POST['class_id']) ? intval($_POST['class_id']) : 0;
        if($class_id){
            $r = mysqli_fetch_assoc(mysqli_query($link, "SELECT name FROM classes WHERE id=".intval($class_id)));
            if($r) $class_name = mysqli_real_escape_string($link, $r['name']);
        }
    } else {
        // fallback to class_name input (select filled from distinct values)
        $class_name = mysqli_real_escape_string($link, $_POST['class_name']);
    }

    // check whether students table has class_id column
    $has_class_id_col = false;
    $col_check = mysqli_query($link, "SHOW COLUMNS FROM students LIKE 'class_id'");
    if($col_check && mysqli_num_rows($col_check) > 0){
        $has_class_id_col = true;
    }

    if($has_class_id_col){
        // include class_id and class_name (for compatibility)
        $q = "INSERT INTO students (student_id_code, full_name, email, class_id, class_name) VALUES ('".
             $student_id_code."','".$full_name."','".$email."',".intval($class_id).",'".$class_name."')";
    } else {
        // only class_name exists
        $q = "INSERT INTO students (student_id_code, full_name, email, class_name) VALUES ('".
             $student_id_code."','".$full_name."','".$email."','".$class_name."')";
    }

    mysqli_query($link, $q) or die(mysqli_error($link));
    header("Location: ".$_SERVER['PHP_SELF']);
    exit;
}
?>

<html lang="en">
<head>
    <title>Manage Students</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="dashboard_style.css">
</head>
<body>

    <nav class="header-nav">
        <a href="home.php" class="logo">Teacher Bee 🐝</a>
        <a href="logout.php" class="logout-btn">Logout</a>
    </nav>

    <div class="main-container">
        <div class="content-box">
            <a href="home.php" class="btn btn-info" style="margin-bottom: 20px;">⬅️ Back to Dashboard</a>
            <h1 class="page-title">Manage Students</h1>
            
            <div class="crud-container">
                <div class="form-container">
                    <h2 class="section-title">Add New Student</h2>
                    <form action="" name="form_student" method="post">
                        <div class="form-group">
                            <label>Student ID Code:</label>
                            <input type="text" class="form-control" name="student_id_code" required>
                        </div>
                        <div class="form-group">
                            <label>Full Name:</label>
                            <input type="text" class="form-control" name="full_name" required>
                        </div>
                        <div class="form-group">
                            <label>Email:</label>
                            <input type="email" class="form-control" name="email">
                        </div>
                        <div class="form-group">
                            <label>Phone Number:</label>
                            <input type="text" class="form-control" name="phone_number">
                        </div>
                        <div class="form-group">
                            <label>Class:</label>
                            <?php if(!empty($classes) && count($classes) > 0): ?>
                                <select name="<?php echo ($classes_table_exists ? 'class_id' : 'class_name'); ?>" class="form-control" required>
                                    <option value="">-- Select class --</option>
                                    <?php foreach($classes as $c): ?>
                                        <option value="<?php echo !empty($c['id']) ? intval($c['id']) : htmlspecialchars($c['name']); ?>"><?php echo htmlspecialchars($c['name']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            <?php else: ?>
                                <p class="text-danger">No classes available. Please <a href="manage_classes.php">create a class</a> first.</p>
                            <?php endif; ?>
                        </div>
                        <button type="submit" name="insert" class="btn btn-primary" <?php echo (empty($classes) ? 'disabled' : ''); ?>>Add Student</button>
                    </form>
                </div>

                <div class="table-container">
                    <h2 class="section-title">Student List</h2>
                    <table class="table table-bordered table-striped table-hover">
                        <thead>
                        <tr>
                            <th>Student ID</th>
                            <th>Full Name</th>
                            <th>Email</th>
                            <th>Phone Number</th>
                            <th>Class</th>
                            <th>Edit</th>
                            <th>Delete</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        // Fetch students; join to classes only if both classes table and students.class_id exist
                        if($classes_table_exists && $has_class_id_col){
                            $res = mysqli_query($link, "SELECT s.*, c.name AS class_display FROM students s LEFT JOIN classes c ON s.class_id = c.id");
                        } else {
                            $res = mysqli_query($link, "SELECT * FROM students");
                        }
                        if(!$res){
                            echo "<tr><td colspan=6>No students found or query error: " . htmlspecialchars(mysqli_error($link)) . "</td></tr>";
                        } else {
                            while($row=mysqli_fetch_array($res))
                            {
                            $class_display = '';
                            if(isset($row['class_display']) && $row['class_display']) $class_display = $row['class_display'];
                            else if(isset($row['class_name'])) $class_display = $row['class_name'];

                            echo "<tr>";
                            // Use null-coalescing to avoid undefined array key and passing null to htmlspecialchars
                            echo "<td>".htmlspecialchars($row["student_id_code"] ?? '')."</td>"; //neu cot k co hoac null thi se in rong
                            echo "<td>".htmlspecialchars($row["full_name"] ?? '')."</td>";
                            echo "<td>".htmlspecialchars($row["email"] ?? '')."</td>";
                            echo "<td>".htmlspecialchars($row["phone_number"] ?? '')."</td>";
                            echo "<td>".htmlspecialchars($class_display ?? '')."</td>";
                            echo "<td><a href='edit_student.php?id=".urlencode($row["id"])."' class='btn btn-success'>Edit</a></td>";
                            echo "<td><a href='delete_student.php?id=".urlencode($row["id"])."' class='btn btn-danger'>Delete</a></td>";
                            echo "</tr>";
                            }
                        }
                        ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>