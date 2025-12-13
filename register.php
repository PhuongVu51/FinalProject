<?php
session_start();
include "connection.php";

$error = "";
if (isset($_POST['register'])) {
    $role = $_POST['role_selector']; // Lấy role từ hidden input
    $name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $pass = $_POST['password'];
    $repass = $_POST['repassword'];

    if(empty($name) || empty($email) || empty($pass)) {
        $error = "Vui lòng nhập đủ thông tin.";
    } elseif ($pass !== $repass) {
        $error = "Mật khẩu nhập lại không khớp.";
    } else {
        $clean_email = mysqli_real_escape_string($link, $email);
        $check = mysqli_query($link, "SELECT id FROM users WHERE username='$clean_email'");
        
        if(mysqli_num_rows($check) > 0) {
            $error = "Email này đã được sử dụng.";
        } else {
            $pass_hash = md5($pass);
            $clean_name = mysqli_real_escape_string($link, $name);

            // Insert User
            $sql = "INSERT INTO users (username, password, role, full_name) VALUES ('$clean_email', '$pass_hash', '$role', '$clean_name')";
            
            if(mysqli_query($link, $sql)){
                $uid = mysqli_insert_id($link);
                // Insert bảng phụ
                if($role == 'teacher') {
                    mysqli_query($link, "INSERT INTO teachers (user_id, email) VALUES ($uid, '$clean_email')");
                } else {
                    // Tạo mã SV tự động
                    $scode = "SV" . str_pad($uid, 4, "0", STR_PAD_LEFT);
                    mysqli_query($link, "INSERT INTO students (user_id, student_code) VALUES ($uid, '$scode')");
                }
                echo "<script>alert('Đăng ký thành công!'); window.location='login.php';</script>";
            } else {
                $error = "Lỗi hệ thống: " . mysqli_error($link);
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Bee - Đăng ký</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['"Be Vietnam Pro"', 'sans-serif'], },
                    colors: {
                        honey: { 50: '#FFF8E1', 100: '#FFECB3', 400: '#FFCA28', 500: '#FFB300', 600: '#FFA000', },
                        dark: { 900: '#2D3436', 500: '#636E72', 100: '#F9FAFB', }
                    },
                    boxShadow: {
                        'soft': '0 4px 20px -2px rgba(0, 0, 0, 0.05)',
                        'honey-glow': '0 4px 14px 0 rgba(255, 179, 0, 0.39)',
                    }
                }
            }
        }
    </script>
    <style>
        .input-group:focus-within svg { color: #FFB300; }
        .custom-scrollbar::-webkit-scrollbar { width: 6px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background-color: #E5E7EB; border-radius: 20px; }
    </style>
</head>
<body class="bg-dark-100 min-h-screen flex items-center justify-center font-sans text-dark-900">

    <div class="w-full h-screen flex overflow-hidden bg-white shadow-2xl rounded-none lg:rounded-2xl lg:h-[90vh] lg:w-[90vw] xl:w-[1200px] lg:m-8">
        
        <div class="hidden lg:flex lg:w-5/12 bg-honey-50 relative flex-col justify-between p-12 overflow-hidden">
            <div class="absolute top-[-50px] right-[-50px] w-64 h-64 bg-honey-400 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob"></div>
            <div class="absolute bottom-10 left-10 w-48 h-48 bg-honey-500 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob animation-delay-2000"></div>

            <div class="z-10 flex items-center gap-3">
                <div class="w-10 h-10 bg-honey-500 rounded-xl flex items-center justify-center text-white shadow-honey-glow">
                    <i class="ph-bold ph-student text-2xl"></i>
                </div>
                <span class="text-2xl font-bold tracking-tight text-dark-900">Teacher Bee</span>
            </div>

            <div class="z-10 flex flex-col justify-center h-full">
                <h2 class="text-3xl font-bold leading-tight mb-4">Tham gia cộng đồng<br>giáo dục hàng đầu.</h2>
                <ul class="space-y-4 text-dark-500 mb-8">
                    <li class="flex items-center gap-3"><div class="w-8 h-8 rounded-full bg-white flex items-center justify-center text-honey-500 shadow-sm"><i class="ph-fill ph-check"></i></div> <span>Quản lý lớp học dễ dàng</span></li>
                    <li class="flex items-center gap-3"><div class="w-8 h-8 rounded-full bg-white flex items-center justify-center text-honey-500 shadow-sm"><i class="ph-fill ph-check"></i></div> <span>Tương tác trực quan</span></li>
                </ul>
            </div>
            <div class="z-10 text-sm text-dark-500 font-medium">Đã có hơn 5,000+ giáo viên tin dùng.</div>
        </div>

        <div class="w-full lg:w-7/12 bg-white flex flex-col relative overflow-hidden">
            <div class="flex-1 overflow-y-auto custom-scrollbar p-8 md:p-12">
                <div class="max-w-xl mx-auto">
                    
                    <div class="mb-8 text-center lg:text-left">
                        <h1 class="text-3xl font-bold mb-2 text-dark-900">Tạo tài khoản mới 🚀</h1>
                        <p class="text-dark-500">Bắt đầu hành trình học tập của bạn ngay hôm nay.</p>
                    </div>

                    <?php if($error): ?>
                        <div class="bg-red-50 text-red-500 p-3 rounded-lg mb-4 text-sm font-medium flex items-center gap-2">
                            <i class="ph-bold ph-warning-circle text-lg"></i> <?php echo $error; ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" class="space-y-5">
                        <input type="hidden" name="role_selector" id="role_selector" value="teacher">

                        <div class="bg-gray-100 p-1.5 rounded-xl flex mb-8">
                            <button type="button" onclick="setRegisterRole('teacher')" id="tab-teacher" class="flex-1 py-2.5 rounded-lg text-sm font-bold bg-white text-honey-600 shadow-sm flex items-center justify-center gap-2 transition-all">
                                <i class="ph-bold ph-chalkboard-teacher"></i> Giáo viên
                            </button>
                            <button type="button" onclick="setRegisterRole('student')" id="tab-student" class="flex-1 py-2.5 rounded-lg text-sm font-medium text-gray-500 hover:text-dark-900 flex items-center justify-center gap-2 transition-all">
                                <i class="ph-bold ph-student"></i> Học sinh
                            </button>
                        </div>

                        <div class="space-y-1.5">
                            <label class="text-sm font-semibold text-dark-900">Họ và tên <span class="text-red-500">*</span></label>
                            <input type="text" name="full_name" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-honey-500/20 focus:border-honey-500 transition-all" placeholder="Nguyễn Minh Thuý" required>
                        </div>

                        <div class="space-y-1.5">
                            <label class="text-sm font-semibold text-dark-900">Email <span class="text-red-500">*</span></label>
                            <input type="email" name="email" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-honey-500/20 focus:border-honey-500 transition-all" placeholder="name@email.com" required>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div class="space-y-1.5">
                                <label class="text-sm font-semibold text-dark-900">Mật khẩu <span class="text-red-500">*</span></label>
                                <input type="password" name="password" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-honey-500/20 focus:border-honey-500 transition-all" placeholder="••••••••" required>
                            </div>
                            <div class="space-y-1.5">
                                <label class="text-sm font-semibold text-dark-900">Nhập lại <span class="text-red-500">*</span></label>
                                <input type="password" name="repassword" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-honey-500/20 focus:border-honey-500 transition-all" placeholder="••••••••" required>
                            </div>
                        </div>

                        <div id="teacher-fields" class="space-y-1.5 transition-all duration-300">
                            <label class="text-sm font-semibold text-dark-900">Chuyên môn giảng dạy</label>
                            <div class="relative">
                                <select class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-honey-500/20 focus:border-honey-500 transition-all appearance-none cursor-pointer">
                                    <option value="" disabled selected>Chọn môn học...</option>
                                    <option value="math">Toán học</option>
                                    <option value="eng">Tiếng Anh</option>
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center px-4 pointer-events-none text-gray-500"><i class="ph-bold ph-caret-down"></i></div>
                            </div>
                        </div>

                        <label class="flex items-start gap-3 cursor-pointer pt-2">
                            <input type="checkbox" required class="mt-1 w-4 h-4 rounded border-gray-300 text-honey-600 focus:ring-honey-500">
                            <span class="text-sm text-dark-500">Tôi đồng ý với <a href="#" class="text-honey-600 hover:underline">Điều khoản sử dụng</a>.</span>
                        </label>

                        <button type="submit" name="register" class="w-full py-3.5 px-4 bg-honey-500 hover:bg-honey-600 text-white font-bold rounded-xl shadow-honey-glow transition-all transform hover:-translate-y-0.5 active:translate-y-0 focus:outline-none focus:ring-4 focus:ring-honey-500/30">
                            Tạo tài khoản
                        </button>
                    </form>

                    <div class="mt-8 text-center text-sm text-dark-500">
                        Đã có tài khoản? <a href="login.php" class="font-bold text-honey-600 hover:text-honey-700 hover:underline">Đăng nhập</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function setRegisterRole(role) {
            document.getElementById('role_selector').value = role;
            const btnTeacher = document.getElementById('tab-teacher');
            const btnStudent = document.getElementById('tab-student');
            const teacherFields = document.getElementById('teacher-fields');

            const activeClass = "flex-1 py-2.5 rounded-lg text-sm font-bold bg-white text-honey-600 shadow-sm transition-all duration-300 ring-1 ring-gray-200 flex items-center justify-center gap-2";
            const inactiveClass = "flex-1 py-2.5 rounded-lg text-sm font-medium text-gray-500 hover:text-dark-900 transition-all duration-300 flex items-center justify-center gap-2";

            if (role === 'teacher') {
                btnTeacher.className = activeClass;
                btnStudent.className = inactiveClass;
                teacherFields.style.display = 'block';
            } else {
                btnStudent.className = activeClass;
                btnTeacher.className = inactiveClass;
                teacherFields.style.display = 'none';
            }
        }
        setRegisterRole('teacher'); // Default
    </script>
</body>
</html>