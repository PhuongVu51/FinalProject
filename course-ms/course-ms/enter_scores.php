<?php
ini_set('display_errors', 1); error_reporting(E_ALL);
include "connection.php";
session_start();
if(!isset($_SESSION['username'])){ header('location:login.php'); }

$exam_id = isset($_GET['exam_id']) ? intval($_GET['exam_id']) : 0;

// Lấy thông tin bài thi
$exam_q = mysqli_query($link, "SELECT * FROM exams WHERE id=$exam_id");
$exam = mysqli_fetch_assoc($exam_q);

if(!$exam) die("Không tìm thấy bài thi!");

$class_id = isset($exam['class_id']) ? intval($exam['class_id']) : 0;

// Xử lý lưu điểm
if(isset($_POST['save_scores'])) {
    foreach($_POST['scores'] as $student_id => $score) {
        $score = floatval($score);
        $student_id = intval($student_id);
        $comment = isset($_POST['comments'][$student_id]) ? mysqli_real_escape_string($link, $_POST['comments'][$student_id]) : '';
        
        $sql = "INSERT INTO scores (exam_id, student_id, score, comments) 
                VALUES ($exam_id, $student_id, '$score', '$comment')
                ON DUPLICATE KEY UPDATE score='$score', comments='$comment'";
        mysqli_query($link, $sql);
    }
    $msg = "Scores saved successfully!";
}

// Lấy danh sách học sinh để nhập điểm
$students = mysqli_query($link, "SELECT s.id, s.full_name, s.student_id_code, sc.score, sc.comments FROM students s LEFT JOIN scores sc ON s.id = sc.student_id AND sc.exam_id = $exam_id WHERE s.class_id = $class_id");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Enter Scores | CourseMS Pro</title>
    <link rel="stylesheet" href="https://site-assets.fontawesome.com/releases/v6.4.0/css/all.css">
    <link rel="stylesheet" href="dashboard_style.css">
</head>
<body>

    <?php include "includes/sidebar.php"; ?>

    <div class="main-wrapper">
        <?php include "includes/topbar.php"; ?>

        <div class="content-scroll">
            <div class="card">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
                    <div>
                        <h2 style="margin:0; font-size:20px;">Grading: <span style="color:var(--primary)"><?php echo htmlspecialchars($exam['exam_title']); ?></span></h2>
                        <p style="margin:5px 0 0; color:#64748B; font-size:14px;">Enter scores for students in this class.</p>
                    </div>
                    <a href="manage_exams.php" class="action-btn" style="background:#F1F5F9; color:#64748B"><i class="fa-solid fa-arrow-left"></i> Back</a>
                </div>

                <?php if(isset($msg)) echo "<div style='background:#DCFCE7; color:#166534; padding:10px; border-radius:8px; margin-bottom:15px;'>$msg</div>"; ?>

                <form method="post">
                    <table class="dataTable" style="width:100%">
                        <thead>
                            <tr>
                                <th>Student ID</th>
                                <th>Full Name</th>
                                <th width="150">Score (0-10)</th>
                                <th>Comments</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php 
                        if(mysqli_num_rows($students) > 0) {
                            while($st = mysqli_fetch_assoc($students)): 
                        ?>
                            <tr>
                                <td><b><?php echo $st['student_id_code']; ?></b></td>
                                <td><?php echo htmlspecialchars($st['full_name']); ?></td>
                                <td>
                                    <input type="number" step="0.1" min="0" max="10" class="form-control" 
                                           name="scores[<?php echo $st['id']; ?>]" 
                                           value="<?php echo $st['score']; ?>" required style="text-align:center; font-weight:bold;">
                                </td>
                                <td>
                                    <input type="text" class="form-control" 
                                           name="comments[<?php echo $st['id']; ?>]" 
                                           value="<?php echo $st['comments']; ?>">
                                </td>
                            </tr>
                        <?php 
                            endwhile; 
                        } else {
                            echo "<tr><td colspan='4' style='text-align:center; padding:30px;'>No students found in this class. Please <a href='manage_students.php'>add students</a> to this class first.</td></tr>";
                        }
                        ?>
                        </tbody>
                    </table>
                    
                    <?php if(mysqli_num_rows($students) > 0): ?>
                    <div style="margin-top:20px; text-align:right;">
                        <button type="submit" name="save_scores" class="btn-primary"><i class="fa-solid fa-floppy-disk"></i> Save All Scores</button>
                    </div>
                    <?php endif; ?>
                </form>
            </div>
            <?php include "includes/footer.php"; ?>
        </div>
    </div>
</body>
</html>