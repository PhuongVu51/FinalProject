<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Bee - Nền tảng học tập thông minh</title>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script> tailwind.config = { theme: { extend: { fontFamily: { sans: ['"Be Vietnam Pro"', 'sans-serif'] }, colors: { honey: { 50:'#FFF8E1', 500:'#FFB300', 600:'#FFA000' } } } } } </script>
</head>
<body class="bg-gray-50 font-sans text-gray-900">

    <nav class="bg-white/80 backdrop-blur-md fixed w-full z-50 border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-6 h-20 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <div class="w-10 h-10 bg-honey-500 rounded-xl flex items-center justify-center text-white text-2xl shadow-lg shadow-honey-500/30">
                    <i class="ph-bold ph-student"></i>
                </div>
                <span class="text-xl font-bold text-gray-900">Teacher Bee</span>
            </div>
            <div class="hidden md:flex items-center gap-8 font-medium text-gray-600">
                <a href="#" class="hover:text-honey-600 transition">Giới thiệu</a>
                <a href="#" class="hover:text-honey-600 transition">Tính năng</a>
                <a href="#" class="hover:text-honey-600 transition">Liên hệ</a>
            </div>
            <div class="flex gap-4">
                <a href="login.php" class="px-5 py-2.5 font-bold text-gray-700 hover:text-honey-600 transition">Đăng nhập</a>
                <a href="register.php" class="px-5 py-2.5 bg-honey-500 hover:bg-honey-600 text-white font-bold rounded-xl shadow-lg shadow-honey-500/30 transition transform hover:-translate-y-0.5">Đăng ký ngay</a>
            </div>
        </div>
    </nav>

    <header class="pt-32 pb-20 px-6 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-1/2 h-full bg-honey-50 -z-10 rounded-bl-[100px]"></div>
        <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div>
                <span class="px-3 py-1 bg-honey-100 text-honey-700 rounded-full text-xs font-bold uppercase tracking-wider mb-4 inline-block">Learning Management System</span>
                <h1 class="text-5xl lg:text-6xl font-extrabold leading-tight mb-6">
                    Kết nối tri thức <br>
                    <span class="text-honey-500">Kiến tạo tương lai.</span>
                </h1>
                <p class="text-lg text-gray-500 mb-8 leading-relaxed max-w-lg">
                    Nền tảng quản lý lớp học trực tuyến hàng đầu dành cho giáo viên và học sinh. Tối ưu hóa việc giảng dạy, kiểm tra và đánh giá năng lực.
                </p>
                <div class="flex gap-4">
                    <a href="register.php" class="px-8 py-4 bg-gray-900 text-white font-bold rounded-xl shadow-xl hover:bg-gray-800 transition flex items-center gap-2">
                        Bắt đầu miễn phí <i class="ph-bold ph-arrow-right"></i>
                    </a>
                    <a href="#" class="px-8 py-4 bg-white text-gray-700 border border-gray-200 font-bold rounded-xl hover:bg-gray-50 transition flex items-center gap-2">
                        <i class="ph-fill ph-play-circle text-honey-500 text-xl"></i> Xem Demo
                    </a>
                </div>
            </div>
            <div class="relative">
                <div class="bg-white p-6 rounded-2xl shadow-2xl border border-gray-100 transform rotate-2 hover:rotate-0 transition duration-500">
                    <div class="flex items-center gap-4 mb-4 border-b border-gray-100 pb-4">
                        <div class="w-3 h-3 rounded-full bg-red-400"></div>
                        <div class="w-3 h-3 rounded-full bg-yellow-400"></div>
                        <div class="w-3 h-3 rounded-full bg-green-400"></div>
                        <div class="flex-1 text-center text-xs font-bold text-gray-400">Teacher Bee Dashboard</div>
                    </div>
                    <div class="space-y-4">
                        <div class="h-32 bg-honey-50 rounded-xl w-full flex items-center justify-center text-honey-300"><i class="ph-duotone ph-image text-4xl"></i></div>
                        <div class="grid grid-cols-3 gap-4">
                            <div class="h-20 bg-gray-50 rounded-xl"></div>
                            <div class="h-20 bg-gray-50 rounded-xl"></div>
                            <div class="h-20 bg-gray-50 rounded-xl"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

</body>
</html>