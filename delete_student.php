<?php
include "connection.php";
session_start();
if(!isset($_SESSION['username'])){ header('location:login.php'); }
include "auth.php"; 
requirePermission('delete_data');

$id = isset($_GET["id"]) ? intval($_GET["id"]) : 0;
$res = mysqli_query($link,"SELECT * FROM students WHERE id=$id");
$item = mysqli_fetch_assoc($res);
if(!$item) { header("Location: manage_students.php"); exit; }

if (isset($_GET['confirm']) && $_GET['confirm'] === 'yes') {
    mysqli_query($link, "DELETE FROM students WHERE id=$id");
    header("location:manage_students.php"); exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Confirm Delete</title>
    <link rel="stylesheet" href="https://site-assets.fontawesome.com/releases/v6.4.0/css/all.css">
    <link rel="stylesheet" href="dashboard_style.css">
</head>
<body>
    <?php include "includes/sidebar.php"; ?>
    <div class="main-wrapper">
        <?php include "includes/topbar.php"; ?>
        
        <div class="content-scroll" style="display: flex; justify-content: center; align-items: center;">
            <div class="card" style="text-align: center; max-width: 500px; padding: 40px;">
                <div style="font-size: 60px; color: #EF4444; margin-bottom: 20px;">
                    <i class="fa-solid fa-circle-exclamation"></i>
                </div>
                <h2 style="margin: 0 0 10px 0;">Are you sure?</h2>
                <p style="color: #71717A; margin-bottom: 30px;">
                    Do you really want to delete student <strong><?php echo htmlspecialchars($item['full_name']); ?></strong>? 
                    This process cannot be undone.
                </p>
                <div style="display: flex; gap: 15px; justify-content: center;">
                    <a href="delete_student.php?id=<?php echo $id ?>&confirm=yes" class="btn-primary" style="background:#EF4444; box-shadow:none;">Yes, Delete</a>
                    <a href="manage_students.php" class="btn-secondary">Cancel</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>