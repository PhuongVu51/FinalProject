<?php
// Kiểm tra đúng cổng MySQL của bạn (3306 mặc định)
$link = mysqli_connect("127.0.0.1", "root", "", "teacher_bee_db", 3306);
if (!$link) { die("Không thể kết nối Database: " . mysqli_connect_error()); }
?>