<?php
include "connection.php";
include "auth.php";
requireRole(['admin']);

$id = intval($_GET['id']);
$class = mysqli_fetch_assoc(mysqli_query($link, "SELECT * FROM classes WHERE id=$id"));
if(!$class) header("Location: manage_classes.php");

if(isset($_POST['update'])){
    $name = mysqli_real_escape_string($link, $_POST['name']);
    $tid = intval($_POST['teacher_id']);
    $t_sql = ($tid > 0) ? $tid : "NULL";
    mysqli_query($link, "UPDATE classes SET name='$name', teacher_id=$t_sql WHERE id=$id");
    header("Location: manage_classes.php"); exit;
}
?>
<!DOCTYPE html>
<html>
<link rel="stylesheet" href="https://site-assets.fontawesome.com/releases/v6.4.2/css/all.css">
<head><title>Sửa Lớp</title><link rel="stylesheet" href="dashboard_style.css"></head>
<body>
<?php include "includes/sidebar.php"; ?>
<div class="main-wrapper"><?php include "includes/topbar.php"; ?>
<div class="content-scroll">
    <div class="card" style="max-width:500px; margin:0 auto">
        <h3>Sửa Lớp: <?php echo $class['name']; ?></h3>
        <form method="post">
            <div class="form-group"><label>Tên Lớp</label><input type="text" name="name" value="<?php echo $class['name']; ?>" class="form-control" required></div>
            <div class="form-group"><label>GVCN</label>
                <select name="teacher_id" class="form-control">
                    <option value="0">-- Không gán --</option>
                    <?php 
                    $res = mysqli_query($link, "SELECT t.id, u.full_name FROM teachers t JOIN users u ON t.user_id=u.id");
                    while($r = mysqli_fetch_assoc($res)): ?>
                        <option value="<?php echo $r['id']; ?>" <?php if($class['teacher_id']==$r['id']) echo 'selected'; ?>>
                            <?php echo $r['full_name']; ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <button type="submit" name="update" class="btn-primary">Cập nhật</button>
            <a href="manage_classes.php" class="btn-secondary">Hủy</a>
        </form>
    </div>
</div></div>
</body></html>