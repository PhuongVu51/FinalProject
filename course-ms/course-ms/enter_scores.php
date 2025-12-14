<?php
include "connection.php";
include "auth.php";
requireRole(['teacher']);
$eid = intval($_GET['eid']);

// Lấy thông tin bài thi để biết class_id
$ex = mysqli_fetch_assoc(mysqli_query($link, "SELECT * FROM exams WHERE id=$eid"));

if(isset($_POST['save'])){
    foreach($_POST['s'] as $sid=>$val){
        $score=floatval($val);
        mysqli_query($link, "INSERT INTO scores (exam_id,student_id,score) VALUES ($eid,$sid,$score) ON DUPLICATE KEY UPDATE score=$score");
    }
    header("Location: enter_scores.php?eid=$eid");
}
?>
<!DOCTYPE html>
<html>
<link rel="stylesheet" href="https://site-assets.fontawesome.com/releases/v6.4.2/css/all.css">
<head><title>Scores</title><link rel="stylesheet" href="dashboard_style.css"></head>
<body>
<?php include "includes/sidebar.php"; ?>
<div class="main-wrapper"><?php include "includes/topbar.php"; ?>
<div class="content-scroll">
    <div class="card">
        <h3>Nhập Điểm: <?php echo $ex['exam_title']; ?></h3>
        <form method="post">
            <table class="dataTable">
                <tr><th>Mã số</th><th>Học Sinh</th><th>Điểm</th></tr>
                <?php 
                 $st=mysqli_query($link, "SELECT s.id,
                                        s.student_code,
                                        u.full_name,
                                        sc.score
                                    FROM students s
                                    JOIN users u ON s.user_id=u.id
                                    LEFT JOIN scores sc ON s.id=sc.student_id AND sc.exam_id=$eid
                                    WHERE s.class_id=".$ex['class_id']);
                while($r=mysqli_fetch_assoc($st)): ?>
                <tr>
                    <td><?php echo htmlspecialchars($r['student_code']); ?></td>
                    <td><?php echo $r['full_name']; ?></td>
                    <td><input type="number" step="0.1" name="s[<?php echo $r['id']; ?>]" value="<?php echo $r['score']; ?>" class="form-control" style="width:100%; max-width:120px;"></td>
                </tr>
                <?php endwhile; ?>
            </table>
            <button name="save" class="btn-primary" style="margin-top:10px">Lưu Điểm</button>
        </form>
    </div>
</div></div>
</body></html>