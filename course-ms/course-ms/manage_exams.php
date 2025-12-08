<?php
ini_set('display_errors', 1); error_reporting(E_ALL);
include "connection.php"; 
session_start();
if(!isset($_SESSION['username'])){ header('location:login.php'); }

// Lấy danh sách lớp an toàn
$classes = [];
$check_table = mysqli_query($link, "SHOW TABLES LIKE 'classes'");
if(mysqli_num_rows($check_table) > 0) {
    $q_class = mysqli_query($link, "SELECT * FROM classes");
    if($q_class) { while($c = mysqli_fetch_assoc($q_class)) $classes[] = $c; }
}

if(isset($_POST["insert"])) {
    $title = mysqli_real_escape_string($link, $_POST['exam_title']);
    $subject = mysqli_real_escape_string($link, $_POST['subject']);
    $date = $_POST['exam_date'];
    $class_id = isset($_POST['class_id']) ? intval($_POST['class_id']) : 0;

    $sql = "INSERT INTO exams (exam_title, subject, exam_date, class_id) VALUES ('$title', '$subject', '$date', $class_id)";
    
    if(mysqli_query($link, $sql)) { header("Location: manage_exams.php"); exit; }
    else { die("Lỗi SQL: " . mysqli_error($link)); }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Exams | CourseMS Pro</title>
    <link rel="stylesheet" href="https://site-assets.fontawesome.com/releases/v6.4.0/css/all.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
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
                            <i class="fa-solid fa-file-circle-plus" style="color:var(--primary)"></i> Create Exam
                        </h3>
                        <form action="" method="post">
                            <div style="margin-bottom: 15px;">
                                <label class="form-label">Exam Title</label>
                                <input type="text" class="form-control" name="exam_title" required placeholder="e.g. Midterm Test">
                            </div>
                            <div style="margin-bottom: 15px;">
                                <label class="form-label">Subject</label>
                                <input type="text" class="form-control" name="subject" required placeholder="e.g. English">
                            </div>
                            <div style="margin-bottom: 15px;">
                                <label class="form-label">Date</label>
                                <input type="date" class="form-control" name="exam_date">
                            </div>
                            <div style="margin-bottom: 20px;">
                                <label class="form-label">Assign to Class</label>
                                <select name="class_id" class="form-control" required>
                                    <option value="">-- Select Class --</option>
                                    <?php foreach($classes as $c): ?>
                                        <option value="<?php echo $c['id']; ?>"><?php echo htmlspecialchars($c['name']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <button type="submit" name="insert" class="btn-primary" style="width:100%">Create Exam</button>
                        </form>
                    </div>
                </div>

                <div>
                    <div class="card">
                        <table id="examTable" class="display" style="width:100%">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Title</th>
                                    <th>Class</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $q = "SELECT * FROM exams ORDER BY id DESC";
                                // Thử join nếu có bảng classes
                                if(mysqli_num_rows($check_table) > 0) {
                                    $q = "SELECT e.*, c.name as class_name FROM exams e LEFT JOIN classes c ON e.class_id = c.id ORDER BY e.id DESC";
                                }

                                $res = mysqli_query($link, $q);
                                if($res) {
                                    while($row = mysqli_fetch_assoc($res)) {
                                        $c_name = isset($row['class_name']) ? $row['class_name'] : '-';
                                        echo "<tr>
                                            <td><span style='font-weight:600;'>{$row['id']}</span></td>
                                            <td>{$row['exam_title']} <br><small style='color:#94A3B8'>{$row['subject']}</small></td>
                                            <td><span style='background:#F1F5F9; padding:4px 8px; border-radius:4px; font-size:12px;'>$c_name</span></td>
                                            <td>{$row['exam_date']}</td>
                                            <td>
                                                <a href='enter_scores.php?exam_id={$row['id']}' class='action-btn btn-edit' style='background:#FFFBEB; color:#D97706'>
                                                    <i class='fa-solid fa-star'></i> Score
                                                </a>
                                                <a href='edit_exam.php?id={$row['id']}' class='action-btn btn-edit'><i class='fa-solid fa-pen'></i></a>
                                                <a href='delete_exam.php?id={$row['id']}' class='action-btn btn-delete' onclick=\"return confirm('Delete this exam?')\"><i class='fa-solid fa-trash'></i></a>
                                            </td>
                                        </tr>";
                                    }
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
            <?php include "includes/footer.php"; ?>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#examTable').DataTable({ "pageLength": 10, "lengthChange": false });
        });
    </script>
</body>
</html>