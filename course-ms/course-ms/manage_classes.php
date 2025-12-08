<?php
include "connection.php";
session_start();
if(!isset($_SESSION['username'])){ header('location:login.php'); }

$teacher_id = isset($_SESSION['teacher_id']) ? intval($_SESSION['teacher_id']) : 0;

if(isset($_POST['create_class'])){
    $name = trim($_POST['class_name']);
    if($name !== ''){
        $safe = mysqli_real_escape_string($link, $name);
        mysqli_query($link, "INSERT INTO classes (name, teacher_id) VALUES ('".$safe."',".$teacher_id.")");
        header('Location: manage_classes.php'); exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Classes | CourseMS Pro</title>
    <link rel="stylesheet" href="https://site-assets.fontawesome.com/releases/v6.4.0/css/all.css">
    <link rel="stylesheet" href="dashboard_style.css">
</head>
<body>

    <?php include "includes/sidebar.php"; ?>

    <div class="main-wrapper">
        <?php include "includes/topbar.php"; ?>

        <div class="content-scroll">
            <div style="display: grid; grid-template-columns: 350px 1fr; gap: 24px;">
                
                <div>
                    <div class="card">
                        <h3 style="margin-top:0; margin-bottom: 20px; font-size: 16px; border-bottom: 1px solid #eee; padding-bottom: 10px;">
                            <i class="fa-solid fa-chalkboard-user" style="color:var(--primary)"></i> Create Class
                        </h3>
                        <form method="post">
                            <div style="margin-bottom: 15px;">
                                <label class="form-label">Class Name</label>
                                <input type="text" name="class_name" class="form-control" required placeholder="e.g. Math 101">
                            </div>
                            <button type="submit" name="create_class" class="btn-primary" style="width:100%">Create Class</button>
                        </form>
                    </div>
                </div>

                <div>
                    <div class="card">
                        <table class="dataTable" style="width:100%">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Class Name</th>
                                    <th>Teacher</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php 
                            $sql = "SELECT c.*, t.full_name FROM classes c LEFT JOIN teachers t ON c.teacher_id=t.id ORDER BY c.id DESC";
                            $res = mysqli_query($link, $sql);
                            while($row = mysqli_fetch_assoc($res)): ?>
                                <tr>
                                    <td><?php echo $row['id']; ?></td>
                                    <td style="font-weight:600"><?php echo htmlspecialchars($row['name']); ?></td>
                                    <td style="color:#64748B"><?php echo htmlspecialchars($row['full_name']); ?></td>
                                    <td>
                                        <a class="action-btn" style="background:#EFF6FF; color:#3B82F6" href="manage_students.php?class_id=<?php echo $row['id']; ?>">
                                            <i class="fa-solid fa-users"></i> Students
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
            <?php include "includes/footer.php"; ?>
        </div>
    </div>
</body>
</html>