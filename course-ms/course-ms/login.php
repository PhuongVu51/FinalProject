<?php
session_start();
include "connection.php";
include "auth.php";

// Tự động kiểm tra Cookie
checkLogin($link);

if(isset($_SESSION['user_id'])) {
    $redirect = ($_SESSION['role'] == 'student') ? 'student_home.php' : 'home.php';
    header("Location: $redirect"); exit;
}

$error = "";
if(isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $selected_role = $_POST['role_selector']; // Lấy role từ nút bấm

    if(empty($username) || empty($password)) {
        $error = "Vui lòng nhập đầy đủ thông tin.";
    } else {
        $clean_user = mysqli_real_escape_string($link, $username);
        $hash_pass = md5($password);
        
        // Map selected role to role_id
        $role_id_map = ['admin' => 1, 'teacher' => 2, 'student' => 3];
        $expected_role_id = $role_id_map[$selected_role];
        
        // Kiểm tra User với role_id
        $sql = "SELECT * FROM users WHERE username='$clean_user' AND password='$hash_pass' AND role_id=$expected_role_id";
        $res = mysqli_query($link, $sql);

        if(mysqli_num_rows($res) == 1) {
            $user = mysqli_fetch_assoc($res);
            
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['full_name'] = $user['full_name'];
            
            // Map role_id to role string
            if ($user['role_id'] == 1) $_SESSION['role'] = 'admin';
            elseif ($user['role_id'] == 2) $_SESSION['role'] = 'teacher';
            elseif ($user['role_id'] == 3) $_SESSION['role'] = 'student';
            
            loadSubId($link, $user);

            if(isset($_POST['remember'])) {
                $token = bin2hex(random_bytes(16));
                mysqli_query($link, "UPDATE users SET remember_token='$token' WHERE id=".$user['id']);
                setcookie('remember_token', $token, time() + (86400 * 30), "/");
            }

            $redirect = ($user['role_id'] == 3) ? 'student_home.php' : 'home.php';
            header("Location: $redirect"); exit;
        } else {
            $error = "Email, mật khẩu hoặc vai trò không đúng.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Bee - Đăng nhập</title>
    
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
                        honey: { 50: '#FFF8E1', 100: '#FFECB3', 400: '#FFCA28', 500: '#FFB300', 600: '#FFA000', 700: '#FF8F00', },
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
    <style> .input-group:focus-within svg { color: #FFB300; } </style>
</head>
<body class="bg-dark-100 min-h-screen flex items-center justify-center font-sans text-dark-900">

    <div class="w-full h-screen flex overflow-hidden bg-white shadow-2xl rounded-none lg:rounded-2xl lg:h-[85vh] lg:w-[90vw] xl:w-[1200px] lg:m-8">
        
        <div class="hidden lg:flex lg:w-1/2 bg-honey-50 relative flex-col justify-between p-12 overflow-hidden">
            <div class="absolute top-[-50px] left-[-50px] w-64 h-64 bg-honey-400 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob"></div>
            <div class="absolute bottom-[-50px] right-[-50px] w-64 h-64 bg-honey-500 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob animation-delay-2000"></div>

            <div class="z-10 flex items-center gap-3">
                <div class="w-10 h-10 bg-honey-500 rounded-xl flex items-center justify-center text-white shadow-honey-glow">
                    <i class="ph-bold ph-student text-2xl"></i>
                </div>
                <span class="text-2xl font-bold tracking-tight text-dark-900">Teacher Bee</span>
            </div>

            <div class="z-10 flex flex-col justify-center h-full">
                <h2 class="text-4xl font-bold leading-tight mb-4">
                    Kết nối tri thức,<br>
                    <span class="text-honey-600">Kiến tạo tương lai.</span>
                </h2>
                <p class="text-dark-500 text-lg mb-8 max-w-md">
                    Hệ thống quản lý lớp học trực tuyến tối ưu dành cho Giáo viên và Học sinh.
                </p>
                <div class="w-full h-64 bg-white/60 backdrop-blur-sm rounded-2xl border-2 border-white/50 flex items-center justify-center shadow-soft">
                    <div class="text-center">
                        <i class="ph-duotone ph-chalkboard-teacher text-honey-500 text-6xl mb-2"></i>
                        <p class="text-sm text-gray-400 font-medium">Illustration: Teacher & Students</p>
                    </div>
                </div>
            </div>
            <div class="z-10 text-sm text-dark-500">&copy; 2025 Teacher Bee Inc.</div>
        </div>

        <div class="w-full lg:w-1/2 bg-white p-8 md:p-12 lg:p-16 flex flex-col justify-center relative">
            
            <div class="lg:hidden flex items-center gap-2 mb-8 justify-center">
                <div class="w-8 h-8 bg-honey-500 rounded-lg flex items-center justify-center text-white">
                    <i class="ph-bold ph-student text-xl"></i>
                </div>
                <span class="text-xl font-bold text-dark-900">Teacher Bee</span>
            </div>

            <div class="mb-8 text-center lg:text-left">
                <h1 class="text-3xl font-bold mb-2 text-dark-900">Chào mừng trở lại! 👋</h1>
                <p class="text-dark-500">Vui lòng chọn vai trò và đăng nhập.</p>
            </div>

            <?php if($error): ?>
                <div class="bg-red-50 text-red-500 p-3 rounded-lg mb-4 text-sm font-medium flex items-center gap-2">
                    <i class="ph-bold ph-warning-circle text-lg"></i> <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="space-y-6">
                <input type="hidden" name="role_selector" id="role_selector" value="teacher">

                <div class="bg-gray-100 p-1.5 rounded-xl flex mb-8 relative">
                    <button type="button" onclick="switchRole('admin')" id="btn-admin" class="flex-1 py-2.5 rounded-lg text-sm font-medium text-gray-500 transition-all duration-300 hover:text-dark-900 focus:outline-none">Admin</button>
                    <button type="button" onclick="switchRole('teacher')" id="btn-teacher" class="flex-1 py-2.5 rounded-lg text-sm font-bold bg-white text-honey-600 shadow-sm transition-all duration-300 ring-1 ring-gray-200">Teacher</button>
                    <button type="button" onclick="switchRole('student')" id="btn-student" class="flex-1 py-2.5 rounded-lg text-sm font-medium text-gray-500 transition-all duration-300 hover:text-dark-900 focus:outline-none">Student</button>
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-semibold text-dark-900">Email hoặc Username</label>
                    <div class="input-group relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="ph ph-envelope-simple text-gray-400 text-xl transition-colors"></i>
                        </div>
                        <input type="text" name="username" class="w-full pl-11 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-dark-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-honey-500/20 focus:border-honey-500 transition-all" placeholder="Nhập tài khoản" required>
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-semibold text-dark-900">Mật khẩu</label>
                    <div class="input-group relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="ph ph-lock-key text-gray-400 text-xl transition-colors"></i>
                        </div>
                        <input type="password" name="password" id="password" class="w-full pl-11 pr-12 py-3 bg-gray-50 border border-gray-200 rounded-xl text-dark-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-honey-500/20 focus:border-honey-500 transition-all" placeholder="••••••••" required>
                        <button type="button" onclick="togglePassword()" class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-dark-900 focus:outline-none">
                            <i id="eye-icon" class="ph ph-eye-slash text-xl"></i>
                        </button>
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="remember" class="w-4 h-4 rounded border-gray-300 text-honey-600 focus:ring-honey-500">
                        <span class="text-sm text-dark-500 select-none">Ghi nhớ đăng nhập</span>
                    </label>
                    <a href="#" class="text-sm font-semibold text-honey-600 hover:text-honey-700 hover:underline">Quên mật khẩu?</a>
                </div>

                <button type="submit" name="login" class="w-full py-3.5 px-4 bg-honey-500 hover:bg-honey-600 text-white font-bold rounded-xl shadow-honey-glow transition-all transform hover:-translate-y-0.5 active:translate-y-0 focus:outline-none focus:ring-4 focus:ring-honey-500/30">
                    Đăng nhập
                </button>
            </form>

            <div class="mt-8 text-center text-sm text-dark-500">
                Chưa có tài khoản? 
                <a href="register.php" class="font-bold text-honey-600 hover:text-honey-700 hover:underline">Đăng ký ngay</a>
            </div>
        </div>
    </div>

    <script>
        const roles = ['admin', 'teacher', 'student'];
        
        function switchRole(role) {
            document.getElementById('role_selector').value = role;

            roles.forEach(r => {
                const btn = document.getElementById(`btn-${r}`);
                if (r === role) {
                    btn.className = "flex-1 py-2.5 rounded-lg text-sm font-bold bg-white text-honey-600 shadow-sm transition-all duration-300 ring-1 ring-gray-200";
                } else {
                    btn.className = "flex-1 py-2.5 rounded-lg text-sm font-medium text-gray-500 hover:text-dark-900 transition-all duration-300 focus:outline-none";
                }
            });
        }

        function togglePassword() {
            const passInput = document.getElementById('password');
            const icon = document.getElementById('eye-icon');
            if (passInput.type === 'password') {
                passInput.type = 'text';
                icon.classList.replace('ph-eye-slash', 'ph-eye');
                icon.classList.add('text-honey-600');
            } else {
                passInput.type = 'password';
                icon.classList.replace('ph-eye', 'ph-eye-slash');
                icon.classList.remove('text-honey-600');
            }
        }
    </script>
</body>
</html>