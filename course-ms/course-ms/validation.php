<?php
// THÊM 3 DÒNG NÀY ĐỂ BÁO LỖI (Cần thiết trong quá trình phát triển)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

// --- CẬP NHẬT THÔNG TIN KẾT NỐI CHO INFINITYFREE ---
$host = "sql100.infinityfree.com";
$user = "if0_40573259";
$pass = "Mavuong515"; // KIỂM TRA LẠI MẬT KHẨU NẾU LỖI KẾT NỐI
$dbname = "if0_40573259_course_ms"; 

// Lệnh kết nối (Sử dụng 4 tham số)
$con = mysqli_connect($host, $user, $pass, $dbname);

// Báo lỗi nếu kết nối thất bại
if (!$con) {
    die("LỖI KẾT NỐI DATABASE: Vui lòng kiểm tra lại Hostname, Username, Password.");
}

/* Lấy dữ liệu */
$email = $_POST['email'];
$pass = md5($_POST['password']);

/* Kiểm tra 'email' và 'password' trong bảng 'teachers' */
// Đã thay '&&' thành 'AND' chuẩn SQL
$s = "select * from teachers where email='$email' AND password='$pass'";

$result = mysqli_query($con, $s);

// Kiểm tra lỗi truy vấn SQL (Ví dụ: tên bảng sai)
if (!$result) {
    die("LỖI TRUY VẤN SQL: " . mysqli_error($con));
}

$num = mysqli_num_rows($result);

if($num == 1){
    $user_data = mysqli_fetch_assoc($result);

    /* Lưu thông tin vào session */
    $_SESSION['username'] = $user_data['full_name'];
    $_SESSION['email'] = $user_data['email'];
    $_SESSION['teacher_id'] = $user_data['id'];

    // Chuyển hướng đến trang Home
    header('location:home.php');
} else {
    // Sai thông tin, quay lại trang login
    header('location:login.php?error=1');
}
?>