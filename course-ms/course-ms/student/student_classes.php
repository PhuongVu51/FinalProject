<?php
include "../connection.php"; 
include "../auth.php"; 
requireRole(['student']);

$sid = $_SESSION['student_id'];

// XỬ LÝ ĐĂNG KÝ
if(isset($_GET['reg'])){
    $cid = intval($_GET['reg']);
    // Check spam
    $check = mysqli_query($link, "SELECT id FROM applications WHERE student_id=$sid AND class_id=$cid AND status='pending'");
    // Check đã học chưa
    $check_cur = mysqli_query($link, "SELECT id FROM students WHERE id=$sid AND class_id=$cid");
    
    if(mysqli_num_rows($check) == 0 && mysqli_num_rows($check_cur) == 0){
        mysqli_query($link, "INSERT INTO applications (student_id,class_id,status) VALUES ($sid,$cid,'pending')");
        echo "<script>alert('Đã gửi yêu cầu tham gia lớp!'); window.location='student_classes.php?tab=market';</script>";
    } else {
        echo "<script>alert('Bạn đã ở trong lớp này hoặc đang chờ duyệt!'); window.location='student_classes.php?tab=market';</script>";
    }
}

// XỬ LÝ TÌM KIẾM
$search = isset($_GET['q']) ? mysqli_real_escape_string($link, $_GET['q']) : '';
$search_sql = $search ? "AND (c.name LIKE '%$search%' OR u.full_name LIKE '%$search%')" : "";

// 1. LẤY DANH SÁCH LỚP ĐANG HỌC (MY CLASSES)
$my_classes = mysqli_query($link, "
    SELECT c.*, u.full_name as teacher_name, 
    (SELECT COUNT(*) FROM students WHERE class_id=c.id) as std_count
    FROM students s 
    JOIN classes c ON s.class_id = c.id 
    LEFT JOIN teachers t ON c.teacher_id=t.id 
    LEFT JOIN users u ON t.user_id=u.id 
    WHERE s.id = $sid
");

// 2. LẤY DANH SÁCH LỚP CÓ THỂ ĐĂNG KÝ (COURSE CATALOG)
// Lấy tất cả lớp TRỪ những lớp đã học
$market_classes = mysqli_query($link, "
    SELECT c.*, u.full_name as teacher_name,
    (SELECT status FROM applications WHERE student_id=$sid AND class_id=c.id) as app_status
    FROM classes c 
    LEFT JOIN teachers t ON c.teacher_id=t.id 
    LEFT JOIN users u ON t.user_id=u.id 
    WHERE c.id NOT IN (SELECT class_id FROM students WHERE id=$sid AND class_id IS NOT NULL)
    $search_sql
    ORDER BY c.id DESC
");

// Xác định Tab hiện tại
$active_tab = isset($_GET['tab']) && $_GET['tab'] == 'market' ? 'market' : 'my';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <title>Lớp học | Student</title>
    <?php include "../includes/header_config.php"; ?>
    <style>
        .tab-btn.active { @apply bg-honey-500 text-white shadow-md; }
        .tab-btn { @apply bg-white text-gray-500 hover:bg-gray-50; }
    </style>
</head>
<body class="bg-gray-50 flex font-sans text-gray-900">
    
    <?php include "../includes/sidebar.php"; ?>

    <div class="flex-1 p-8 ml-[260px]">
        
        <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4">
            <h1 class="text-2xl font-bold flex items-center gap-3">
                <i class="ph-duotone ph-chalkboard-teacher text-honey-500"></i> Quản lý Lớp học
            </h1>
            
            <div class="bg-gray-200 p-1 rounded-xl flex">
                <button onclick="switchTab('my')" id="btn-my" class="tab-btn px-6 py-2 rounded-lg text-sm font-bold transition-all <?php echo $active_tab=='my'?'active':''; ?>">Lớp của tôi</button>
                <button onclick="switchTab('market')" id="btn-market" class="tab-btn px-6 py-2 rounded-lg text-sm font-bold transition-all <?php echo $active_tab=='market'?'active':''; ?>">Đăng ký lớp mới</button>
            </div>
        </div>

        <div id="tab-my" class="<?php echo $active_tab=='my'?'':'hidden'; ?>">
            <?php if(mysqli_num_rows($my_classes) == 0): ?>
                <div class="text-center py-16 bg-white rounded-3xl border border-dashed border-gray-300">
                    <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 text-gray-400 text-4xl"><i class="ph-duotone ph-books"></i></div>
                    <p class="text-gray-500 mb-4">Bạn chưa tham gia lớp học nào.</p>
                    <button onclick="switchTab('market')" class="text-honey-600 font-bold hover:underline">Tìm lớp ngay</button>
                </div>
            <?php else: ?>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php while($c = mysqli_fetch_assoc($my_classes)): ?>
                    <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm hover:shadow-lg hover:-translate-y-1 transition duration-300 group relative overflow-hidden">
                        <div class="absolute top-0 right-0 w-24 h-24 bg-honey-50 rounded-bl-full -mr-4 -mt-4 opacity-50 group-hover:scale-110 transition"></div>
                        
                        <div class="flex justify-between items-start mb-4 relative z-10">
                            <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center text-white font-bold text-xl shadow-md">
                                <?php echo substr($c['name'], 0, 2); ?>
                            </div>
                            <span class="bg-green-100 text-green-700 text-[10px] font-bold px-2 py-1 rounded-full uppercase tracking-wide">Đang học</span>
                        </div>
                        
                        <h3 class="font-bold text-lg text-gray-900 mb-1 group-hover:text-honey-600 transition"><?php echo $c['name']; ?></h3>
                        <p class="text-sm text-gray-500 mb-4 flex items-center gap-1"><i class="ph-bold ph-user"></i> GV: <?php echo $c['teacher_name']; ?></p>
                        
                        <div class="border-t border-gray-100 pt-4 flex justify-between items-center">
                            <div class="text-xs text-gray-400 font-bold flex items-center gap-1">
                                <i class="ph-bold ph-users"></i> <?php echo $c['std_count']; ?> Học viên
                            </div>
                            <a href="student_class_detail.php?id=<?php echo $c['id']; ?>" class="w-8 h-8 rounded-full bg-honey-100 text-honey-600 flex items-center justify-center hover:bg-honey-500 hover:text-white transition shadow-sm">
                                <i class="ph-bold ph-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>
            <?php endif; ?>
        </div>

        <div id="tab-market" class="<?php echo $active_tab=='market'?'':'hidden'; ?>">
            
            <form method="GET" class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 mb-6 flex gap-4">
                <input type="hidden" name="tab" value="market">
                <div class="relative flex-1">
                    <i class="ph-bold ph-magnifying-glass absolute left-4 top-3.5 text-gray-400"></i>
                    <input type="text" name="q" value="<?php echo htmlspecialchars($search); ?>" class="w-full pl-12 pr-4 py-3 bg-gray-50 border-none rounded-xl focus:ring-2 focus:ring-honey-500 outline-none transition" placeholder="Tìm theo tên lớp hoặc tên giáo viên...">
                </div>
                <button type="submit" class="bg-dark-900 text-white px-6 py-3 rounded-xl font-bold hover:bg-gray-800 transition">Tìm kiếm</button>
            </form>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <table class="w-full text-left text-sm">
                    <thead class="bg-gray-50 border-b text-gray-500 uppercase font-bold text-xs">
                        <tr>
                            <th class="px-6 py-4">Tên lớp</th>
                            <th class="px-6 py-4">Giáo viên</th>
                            <th class="px-6 py-4 text-right">Hành động</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php if(mysqli_num_rows($market_classes) == 0): ?>
                            <tr><td colspan="4" class="px-6 py-8 text-center text-gray-400">Không tìm thấy lớp học nào phù hợp.</td></tr>
                        <?php else: while($r = mysqli_fetch_assoc($market_classes)): ?>
                        <tr class="hover:bg-honey-50/20 transition">
                            <td class="px-6 py-4 font-bold text-gray-800 text-base"><?php echo $r['name']; ?></td>
                            <td class="px-6 py-4 text-gray-600"><?php echo $r['teacher_name']; ?></td>
                            <td class="px-6 py-4 text-right">
                                <?php if($r['app_status'] == 'pending'): ?>
                                    <span class="inline-flex items-center gap-1 px-4 py-2 bg-yellow-100 text-yellow-700 rounded-lg text-xs font-bold cursor-default">
                                        <i class="ph-bold ph-clock"></i> Chờ duyệt
                                    </span>
                                <?php else: ?>
                                    <a href="?reg=<?php echo $r['id']; ?>&tab=market" onclick="return confirm('Gửi yêu cầu tham gia lớp này?')" class="inline-flex items-center gap-1 px-4 py-2 bg-white border border-honey-200 text-honey-600 hover:bg-honey-500 hover:text-white hover:border-honey-500 rounded-lg text-xs font-bold transition shadow-sm">
                                        Đăng ký ngay
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <script>
        function switchTab(tab) {
            // Ẩn hiện nội dung
            document.getElementById('tab-my').classList.add('hidden');
            document.getElementById('tab-market').classList.add('hidden');
            document.getElementById('tab-' + tab).classList.remove('hidden');

            // Đổi style nút
            document.getElementById('btn-my').className = "tab-btn px-6 py-2 rounded-lg text-sm font-bold transition-all";
            document.getElementById('btn-market').className = "tab-btn px-6 py-2 rounded-lg text-sm font-bold transition-all";
            document.getElementById('btn-' + tab).classList.add('bg-honey-500', 'text-white', 'shadow-md');
            document.getElementById('btn-' + tab).classList.remove('bg-white', 'text-gray-500');
        }
    </script>
</body>
</html>