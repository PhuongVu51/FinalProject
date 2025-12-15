<?php
// 1. KẾT NỐI & AUTH
include "../connection.php"; 
include "../auth.php"; 
requireRole(['admin']);

// 2. HÀM HELPER (Chống lỗi 500)
function runQuery($link, $sql) {
    $res = mysqli_query($link, $sql);
    if(!$res) die("Lỗi hệ thống (SQL): " . mysqli_error($link));
    return $res;
}

// 3. LẤY SỐ LIỆU THỐNG KÊ
$counts = [
    'student' => mysqli_fetch_assoc(runQuery($link, "SELECT COUNT(*) as c FROM users WHERE role='student'"))['c'],
    'teacher' => mysqli_fetch_assoc(runQuery($link, "SELECT COUNT(*) as c FROM users WHERE role='teacher'"))['c'],
    'classes' => mysqli_fetch_assoc(runQuery($link, "SELECT COUNT(*) as c FROM classes"))['c'],
    'exams'   => mysqli_fetch_assoc(runQuery($link, "SELECT COUNT(*) as c FROM exams"))['c']
];

// 4. LẤY NGƯỜI DÙNG MỚI NHẤT (5 người)
$new_users = runQuery($link, "SELECT full_name, role, created_at FROM users ORDER BY created_at DESC LIMIT 5");

// 5. LẤY TIN TỨC MỚI NHẤT
$news = runQuery($link, "SELECT * FROM news ORDER BY created_at DESC LIMIT 4");
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <title>Admin Dashboard | TeacherBee</title>
    <?php include "../includes/header_config.php"; ?>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-50 flex font-sans text-gray-900">
    
    <?php include "../includes/sidebar.php"; ?>

    <div class="flex-1 p-8 ml-[260px]">
        
        <header class="flex justify-between items-center mb-10">
            <div>
                <h1 class="text-3xl font-extrabold text-gray-800 tracking-tight">Tổng quan hệ thống</h1>
                <p class="text-gray-500 font-medium">Xin chào, <?php echo $_SESSION['full_name']; ?>! Hệ thống đang hoạt động ổn định.</p>
            </div>
            <div class="flex items-center gap-3 bg-white pl-4 pr-2 py-2 rounded-full shadow-sm border border-gray-200">

                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-honey-400 to-honey-600 text-white flex items-center justify-center font-bold text-lg shadow-md">
                    <?php echo substr($_SESSION['full_name'], 0, 1); ?>
                </div>
            </div>
        </header>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
            <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 flex items-center gap-4 hover:shadow-md transition group">
                <div class="w-16 h-16 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-3xl group-hover:scale-110 transition">
                    <i class="ph-duotone ph-student"></i>
                </div>
                <div>
                    <h3 class="text-4xl font-black text-gray-800"><?php echo $counts['student']; ?></h3>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Học sinh</p>
                </div>
            </div>
            
            <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 flex items-center gap-4 hover:shadow-md transition group">
                <div class="w-16 h-16 rounded-2xl bg-green-50 text-green-600 flex items-center justify-center text-3xl group-hover:scale-110 transition">
                    <i class="ph-duotone ph-chalkboard-teacher"></i>
                </div>
                <div>
                    <h3 class="text-4xl font-black text-gray-800"><?php echo $counts['teacher']; ?></h3>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Giáo viên</p>
                </div>
            </div>

            <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 flex items-center gap-4 hover:shadow-md transition group">
                <div class="w-16 h-16 rounded-2xl bg-yellow-50 text-yellow-600 flex items-center justify-center text-3xl group-hover:scale-110 transition">
                    <i class="ph-duotone ph-chalkboard"></i>
                </div>
                <div>
                    <h3 class="text-4xl font-black text-gray-800"><?php echo $counts['classes']; ?></h3>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Lớp học</p>
                </div>
            </div>

            <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 flex items-center gap-4 hover:shadow-md transition group">
                <div class="w-16 h-16 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center text-3xl group-hover:scale-110 transition">
                    <i class="ph-duotone ph-file-text"></i>
                </div>
                <div>
                    <h3 class="text-4xl font-black text-gray-800"><?php echo $counts['exams']; ?></h3>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Bài thi</p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <div class="lg:col-span-2 space-y-8">
                
                <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100 flex flex-col md:flex-row gap-8 items-center">
                    <div class="flex-1">
                        <h3 class="text-xl font-bold text-gray-800 mb-2">Tỉ lệ người dùng</h3>
                        <p class="text-gray-500 text-sm mb-6">So sánh số lượng tài khoản Học sinh và Giáo viên trong hệ thống.</p>
                        <div class="flex gap-4 text-sm">
                            <div class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-blue-500"></span> Học sinh: <b><?php echo $counts['student']; ?></b></div>
                            <div class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-green-500"></span> Giáo viên: <b><?php echo $counts['teacher']; ?></b></div>
                        </div>
                    </div>
                    <div class="w-48 h-48 relative">
                        <canvas id="userChart"></canvas>
                    </div>
                </div>

                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
                        <h3 class="font-bold text-gray-800 flex items-center gap-2"><i class="ph-duotone ph-user-plus text-honey-500"></i> Thành viên mới</h3>
                        <a href="manage_students.php" class="text-xs font-bold text-blue-600 hover:underline">Quản lý</a>
                    </div>
                    <table class="w-full text-left text-sm">
                        <tbody class="divide-y divide-gray-50">
                            <?php while($u = mysqli_fetch_assoc($new_users)): 
                                $role_badge = ($u['role']=='teacher') 
                                    ? "<span class='bg-green-100 text-green-700 px-2 py-1 rounded-md text-[10px] font-bold uppercase'>Giáo viên</span>" 
                                    : "<span class='bg-blue-100 text-blue-700 px-2 py-1 rounded-md text-[10px] font-bold uppercase'>Học sinh</span>";
                            ?>
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 font-bold text-gray-700"><?php echo $u['full_name']; ?></td>
                                <td class="px-6 py-4 text-right"><?php echo $role_badge; ?></td>
                                <td class="px-6 py-4 text-right text-gray-400 text-xs"><?php echo date('d/m/Y', strtotime($u['created_at'])); ?></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="space-y-8">
                
                <div class="bg-gradient-to-br from-honey-500 to-amber-600 rounded-3xl p-6 text-white shadow-lg shadow-honey-500/30">
                    <h3 class="font-bold text-lg mb-4 flex items-center gap-2"><i class="ph-bold ph-lightning"></i> Thao tác nhanh</h3>
                    <div class="space-y-3">
                        <a href="manage_news.php" class="block bg-white/20 hover:bg-white/30 backdrop-blur-sm px-4 py-3 rounded-xl text-sm font-bold transition flex justify-between items-center">
                            <span>Đăng thông báo mới</span> <i class="ph-bold ph-arrow-right"></i>
                        </a>
                        <a href="manage_applications.php" class="block bg-white/20 hover:bg-white/30 backdrop-blur-sm px-4 py-3 rounded-xl text-sm font-bold transition flex justify-between items-center">
                            <span>Duyệt đơn vào lớp</span> <i class="ph-bold ph-arrow-right"></i>
                        </a>
                    </div>
                </div>

                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center">
                        <h3 class="font-bold text-gray-800">Tin tức mới</h3>
                        <a href="manage_news.php" class="text-xs font-bold text-gray-400 hover:text-honey-500">Xem tất cả</a>
                    </div>
                    <div class="divide-y divide-gray-50">
                        <?php while($r = mysqli_fetch_assoc($news)): ?>
                        <div class="p-4 hover:bg-gray-50 transition group cursor-pointer" onclick="window.location='../news_detail.php?id=<?php echo $r['id']; ?>'">
                            <div class="text-[10px] font-bold text-honey-600 mb-1 flex items-center gap-1">
                                <i class="ph-fill ph-calendar"></i> <?php echo date('d/m/Y', strtotime($r['created_at'])); ?>
                            </div>
                            <h4 class="font-bold text-gray-900 text-sm line-clamp-2 group-hover:text-honey-600 transition"><?php echo $r['title']; ?></h4>
                        </div>
                        <?php endwhile; ?>
                    </div>
                </div>

            </div>
        </div>

    </div>

    <script>
        const ctx = document.getElementById('userChart').getContext('2d');
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Học sinh', 'Giáo viên'],
                datasets: [{
                    data: [<?php echo $counts['student']; ?>, <?php echo $counts['teacher']; ?>],
                    backgroundColor: ['#3B82F6', '#10B981'], // Blue-500, Green-500
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                cutout: '75%' // Độ dày vòng tròn
            }
        });
    </script>
</body>
</html>