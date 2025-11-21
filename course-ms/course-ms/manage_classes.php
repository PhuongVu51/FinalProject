<?php
include "connection.php";
session_start();
if(!isset($_SESSION['username'])){ header('location:login.php'); }

$teacher_id = isset($_SESSION['teacher_id']) ? intval($_SESSION['teacher_id']) : 0;

// Handle create class
if(isset($_POST['create_class'])){
    $name = trim($_POST['class_name']);
    if($name !== ''){
        $safe = mysqli_real_escape_string($link, $name);
        mysqli_query($link, "INSERT INTO classes (name, teacher_id) VALUES ('".$safe."',".$teacher_id.")") or die(mysqli_error($link));
        header('Location: manage_classes.php');
        exit;
    }
}

// Load classes for this teacher (include teacher name via join)
$classes = [];
$tbl_check = mysqli_query($link, "SHOW TABLES LIKE 'classes'");
if($tbl_check && mysqli_num_rows($tbl_check) > 0){
    $sql = "SELECT c.id, c.name, c.teacher_id, t.full_name AS teacher_name
            FROM classes c
            LEFT JOIN teachers t ON c.teacher_id = t.id
            WHERE c.teacher_id = ?";
    $stmt = mysqli_prepare($link, $sql);
    if($stmt){
        mysqli_stmt_bind_param($stmt, 'i', $teacher_id);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        while($r = mysqli_fetch_assoc($res)){
            $classes[] = $r;
        }
        mysqli_stmt_close($stmt);
    }
}
?>

<html lang="en">
<head>
    <title>Manage Classes</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="dashboard_style.css">
</head>
<body>

    <nav class="header-nav">
        <a href="home.php" class="logo">CourseMS🐝</a>
        <a href="logout.php" class="logout-btn">Log out</a>
    </nav>

    <div class="main-container">
        <div class="content-box">
            <a href="home.php" class="btn btn-info" style="margin-bottom: 20px;">⬅️ Back to Dashboard</a>
            <h1 class="page-title">Manage Classes</h1>

            <div class="crud-container">
                <div class="form-container">
                    <h2 class="section-title">Create New Class</h2>
                    <form method="post" action="">
                        <div class="form-group">
                            <label>Class Name</label>
                            <input type="text" name="class_name" class="form-control" required>
                        </div>
                        <button type="submit" name="create_class" class="btn btn-primary">Create Class</button>
                    </form>
                </div>

                <div class="table-container">
                    <h2 class="section-title">Your Classes</h2>
                    <?php if(empty($classes)): ?>
                        <p>No classes found. Create one using the form at left.</p>
                    <?php else: ?>
                        <table class="table table-bordered">
                            <thead><tr><th>Class ID</th><th>Class Name</th><th>Teacher</th><th>Actions</th></tr></thead>
                            <tbody>
                            <?php foreach($classes as $c): ?>
                                <tr>
                                    <td><?php echo intval($c['id']); ?></td>
                                    <td><?php echo htmlspecialchars($c['name']); ?></td>
                                    <td><?php echo htmlspecialchars(!empty($c['teacher_name']) ? $c['teacher_name'] : '—'); ?></td>
                                    <td>
                                        <a class="btn btn-primary" href="manage_students.php?class_id=<?php echo intval($c['id']); ?>">Student List</a>
                                        <a class="btn btn-warning" href="manage_exams.php?class_id=<?php echo intval($c['id']); ?>">Manage Exams</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
