<?php
include "connection.php";
include "auth.php";
requireRole(['teacher']);

$tid = $_SESSION['teacher_id'];
// Lấy danh sách lớp và đếm sĩ số
$sql = "SELECT c.*, (SELECT COUNT(*) FROM students s WHERE s.class_id = c.id) as student_count FROM classes c WHERE teacher_id = $tid";
$classes_res = mysqli_query($link, $sql);

// Mảng màu gradient để hiển thị cho đẹp giống mẫu
$gradients = [
    'from-honey-500 to-yellow-400',
    'from-blue-500 to-cyan-400',
    'from-purple-500 to-pink-400',
    'from-green-500 to-emerald-400'
];
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Bee - Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['"Be Vietnam Pro"', 'sans-serif'] },
                    colors: {
                        honey: { 50:'#FFF8E1', 100:'#FFECB3', 500:'#FFB300', 600:'#FFA000' },
                        dark: { 900:'#2D3436', 800:'#636E72', 100:'#F9FAFB' }
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-dark-100 min-h-screen font-sans text-dark-900 flex">

    <aside class="w-64 bg-white border-r border-gray-200 hidden md:flex flex-col fixed h-full z-10">
        <div class="h-16 flex items-center gap-3 px-6 border-b border-gray-100">
            <div class="w-8 h-8 bg-honey-500 rounded-lg flex items-center justify-center text-white">
                <i class="ph-bold ph-student text-lg"></i>
            </div>
            <span class="text-lg font-bold">Teacher Bee</span>
        </div>

        <nav class="p-4 space-y-1 flex-1">
            <a href="teacher_dashboard.php" class="flex items-center gap-3 px-4 py-3 bg-honey-50 text-honey-600 rounded-xl font-bold transition-colors">
                <i class="ph-bold ph-squares-four text-xl"></i> Dashboard
            </a>
            </nav>

        <div class="p-4 border-t border-gray-100">
            <div class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-50 cursor-pointer">
                <div class="w-10 h-10 rounded-full bg-honey-100 flex items-center justify-center text-honey-600 font-bold">
                    <?php echo substr($_SESSION['full_name'], 0, 1); ?>
                </div>
                <div class="overflow-hidden">
                    <p class="text-sm font-bold text-dark-900 truncate"><?php echo $_SESSION['full_name']; ?></p>
                    <a href="logout.php" class="text-xs text-red-500 hover:underline">Đăng xuất</a>
                </div>
            </div>
        </div>
    </aside>

    <main class="flex-1 md:ml-64 p-8">
        <header class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-2xl font-bold">Lớp học của tôi 📚</h1>
                <p class="text-gray-500">Quản lý các lớp học và tiến độ giảng dạy.</p>
            </div>
            </header>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            
            <?php 
            $i = 0;
            while($c = mysqli_fetch_assoc($classes_res)): 
                $grad = $gradients[$i % count($gradients)]; $i++;
            ?>
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm hover:shadow-md hover:-translate-y-1 transition-all duration-300 overflow-hidden group">
                <div class="h-32 bg-gradient-to-r <?php echo $grad; ?> relative p-6 flex flex-col justify-between">
                    <div class="bg-white/20 backdrop-blur-md w-max px-3 py-1 rounded-full text-xs text-white font-bold border border-white/30">
                        HK1 - 2025
                    </div>
                    <h3 class="text-white text-xl font-bold drop-shadow-md truncate"><?php echo $c['name']; ?></h3>
                </div>
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4 text-sm">
                        <span class="text-gray-500 flex items-center gap-1"><i class="ph-bold ph-users"></i> <?php echo $c['student_count']; ?> HS</span>
                        <span class="text-green-600 font-bold bg-green-50 px-2 py-1 rounded text-xs">Đang hoạt động</span>
                    </div>
                    <p class="text-gray-500 text-sm mb-6 line-clamp-2 h-10">Lớp học môn <?php echo $c['name']; ?>.</p>
                    <div class="flex gap-3 pt-4 border-t border-gray-100">
                        <a href="class_detail.php?id=<?php echo $c['id']; ?>" class="flex-1 bg-honey-50 text-honey-600 hover:bg-honey-500 hover:text-white py-2 rounded-lg font-bold text-sm transition-colors text-center border border-honey-100 hover:border-honey-500">
                            Vào lớp
                        </a>
                        <button class="w-10 flex items-center justify-center text-gray-400 hover:text-dark-900 bg-gray-50 rounded-lg hover:bg-gray-200 transition">
                            <i class="ph-bold ph-gear"></i>
                        </button>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>

            <div class="bg-white rounded-2xl border-2 border-dashed border-gray-200 hover:border-honey-400 flex flex-col items-center justify-center p-8 transition-colors cursor-pointer group h-full min-h-[300px]">
                <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-4 group-hover:bg-honey-50 transition-colors">
                    <i class="ph-bold ph-plus text-2xl text-gray-400 group-hover:text-honey-500"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-500 group-hover:text-honey-600">Tạo lớp học mới</h3>
            </div>

        </div>
    </main>
</body>
</html>