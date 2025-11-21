<?php
include "connection.php"; 
session_start();
if(!isset($_SESSION['username'])){ header('location:login.php'); }

// Xử lý Thêm mới (Create)
if(isset($_POST["insert"]))
{
    mysqli_query($link,"INSERT INTO exams (exam_title, subject, exam_date) 
                        VALUES ('$_POST[exam_title]', '$_POST[subject]', '$_POST[exam_date]')")
    or die(mysqli_error($link));
    header("Location: ".$_SERVER['PHP_SELF']);
    exit;
}
?>

<html lang="en">
<head>
    <title>Manage Exams</title>
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
            <h1 class="page-title">Manage Exams</h1>

            <div class="crud-container">
                <div class="form-container">
                    <h2 class="section-title">Create New Exam</h2>
                    <form action="" name="form_exam" method="post">
                        <div class="form-group">
                            <label>Exam Title:</label>
                            <input type="text" class="form-control" name="exam_title" required>
                        </div>
                        <div class="form-group">
                            <label>Subject:</label>
                            <input type="text" class="form-control" name="subject" required>
                        </div>
                        <div class="form-group">
                            <label>Exam Date:</label>
                            <input type="date" class="form-control" name="exam_date">
                        </div>
                        <button type="submit" name="insert" class="btn btn-primary">Create Exam</button>
                    </form>
                </div>

                <div class="table-container">
                    <h2 class="section-title">Exam List</h2>
                    <table class="table table-bordered table-striped table-hover">
                        <thead>
                        <tr>
                            <th>#ID</th>
                            <th>Exam Title</th>
                            <th>Subject</th>
                            <th>Exam Date</th>
                            <th>Edit</th>
                            <th>Delete</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $res=mysqli_query($link,"select * from exams");
                        while($row=mysqli_fetch_array($res))
                        {
                            echo "<tr>";
                            echo "<td>"; echo $row["id"]; echo "</td>";
                            echo "<td>"; echo $row["exam_title"]; echo "</td>";
                            echo "<td>"; echo $row["subject"]; echo "</td>";
                            echo "<td>"; echo $row["exam_date"]; echo "</td>";
                            echo "<td><a href='edit_exam.php?id=".$row["id"]."' class='btn btn-success'>Edit</a></td>";
                            echo "<td><a href='delete_exam.php?id=".$row["id"]."' class='btn btn-danger'>Delete</a></td>";
                            echo "</tr>";
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