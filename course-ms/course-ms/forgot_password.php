<?php
include "connection.php";
$message = "";

if (isset($_POST['reset'])) {
    $email = mysqli_real_escape_string($link, $_POST['email']);
    $check = mysqli_query($link, "SELECT id FROM users WHERE username='$email'");

    if (mysqli_num_rows($check) > 0) {
        // GIẢ LẬP: Thông báo thành công để demo quy trình
        $message = '<div class="bg-green-100 border border-green-200 text-green-700 px-4 py-3 rounded-xl mb-4 text-sm">
                        Hệ thống đã gửi hướng dẫn đặt lại mật khẩu vào email của bạn. Vui lòng kiểm tra hộp thư!
                    </div>';
    } else {
        $message = '<div class="bg-red-100 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-4 text-sm">
                        Email này không tồn tại trong hệ thống!
                    </div>';
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <title>Quên mật khẩu | TeacherBee</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <style> body { font-family: 'Be Vietnam Pro', sans-serif; } </style>
</head>
<body class="bg-gray-50 flex items-center justify-center min-h-screen">
    <div class="bg-white p-8 rounded-3xl shadow-xl w-full max-w-md border border-gray-100">
        <div class="text-center mb-8">
            <div class="w-16 h-16 bg-yellow-100 text-yellow-600 rounded-2xl flex items-center justify-center mx-auto mb-4 text-3xl">
                <i class="ph-bold ph-lock-key"></i>
            </div>
            <h1 class="text-2xl font-bold text-gray-800">Quên mật khẩu?</h1>
            <p class="text-gray-500 text-sm mt-2">Nhập email tài khoản của bạn để nhận hướng dẫn khôi phục.</p>
        </div>

        <?php echo $message; ?>

        <form method="POST" class="space-y-6">
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Email đăng nhập</label>
                <input type="email" name="email" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:border-yellow-500 outline-none transition" placeholder="example@bee.com" required>
            </div>
            <button name="reset" class="w-full py-3 bg-yellow-500 text-white font-bold rounded-xl hover:bg-yellow-600 transition-all shadow-lg shadow-yellow-500/20">
                Gửi yêu cầu
            </button>
        </form>

        <div class="mt-8 text-center">
            <a href="login.php" class="text-sm font-bold text-gray-400 hover:text-yellow-500 transition">
                <i class="ph-bold ph-arrow-left"></i> Quay lại Đăng nhập
            </a>
        </div>
    </div>
</body>
</html>