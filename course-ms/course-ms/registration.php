<?php
// Bật hiển thị lỗi để dễ sửa nếu có sự cố
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

// --- THÔNG TIN KẾT NỐI INFINITYFREE (ĐÃ SỬA) ---
$host = "127.0.0.1";
$user = "root";
$pass = ""; 
$dbname = "teacher_bee_db"; 

$con = mysqli_connect($host, $user, $pass, $dbname);

// Kiểm tra kết nối
if (!$con) {
    die("Lỗi kết nối Database: " . mysqli_connect_error());
}

/* Lấy dữ liệu từ Form */
// Lưu ý: Đảm bảo tên trong $_POST['...'] khớp với name="" bên file HTML
$full_name = mysqli_real_escape_string($con, $_POST['full_name']);
$email = mysqli_real_escape_string($con, $_POST['email']);
$pass = md5($_POST['password']); // Mã hóa mật khẩu
$dob = mysqli_real_escape_string($con, $_POST['dob']);
$gender = mysqli_real_escape_string($con, $_POST['gender']);
$subjects = mysqli_real_escape_string($con, $_POST['subjects']);

/* Bước 1: Kiểm tra xem email đã tồn tại chưa */
$s = "SELECT * FROM teachers WHERE email='$email'";
$result = mysqli_query($con, $s);

if (!$result) {
    die("Lỗi truy vấn kiểm tra email: " . mysqli_error($con));
}

$num = mysqli_num_rows($result);

if ($num == 1) {
    // Email đã có -> Báo lỗi
    echo "Email '$email' đã tồn tại. Vui lòng chọn email khác.";
    // Đợi 3 giây rồi quay lại trang đăng ký
    header("refresh:3;url=register.php"); 
} else {
    /* Bước 2: Thêm giáo viên mới */
    $reg = "INSERT INTO teachers (full_name, email, password, dob, gender, subjects) 
            VALUES ('$full_name', '$email', '$pass', '$dob', '$gender', '$subjects')";
    
    if (mysqli_query($con, $reg)) {
        echo "Đăng ký thành công! Đang chuyển đến trang đăng nhập...";
        // Đợi 2 giây rồi chuyển sang trang login
        header("refresh:2;url=login.php");
    } else {
        // In ra lỗi cụ thể (Ví dụ: Sai tên cột, sai kiểu dữ liệu...)
        echo "Lỗi khi lưu dữ liệu: " . mysqli_error($con);
    }
}

mysqli_close($con);
?>