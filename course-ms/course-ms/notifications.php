<?php
include "connection.php";
include "auth.php";
requireRole(['admin','teacher','student']);

$role = $_SESSION['role'];
$teacherId = $_SESSION['teacher_id'] ?? 0;
$studentId = $_SESSION['student_id'] ?? 0;

$col1_data = []; 
$col1_title = "Thông báo";
$col1_desc = "Cập nhật mới nhất về hoạt động của bạn.";

// --- LOGIC LẤY DỮ LIỆU ---

// 1. ADMIN
if($role === 'admin'){
    $col1_title = "Cần xử lý ngay";
    $col1_desc = "Các yêu cầu từ giáo viên và học sinh đang chờ duyệt.";
    
    // GV mới (Bảng users thường có created_at)
    $newTeachersRes = mysqli_query($link, "SELECT full_name, created_at FROM users WHERE role='teacher' ORDER BY created_at DESC LIMIT 5");
    if($newTeachersRes){
        while($r = mysqli_fetch_assoc($newTeachersRes)) {
            $col1_data[] = [
                'icon' => 'ph-user-plus', 'bg' => 'bg-blue-100', 'text_color' => 'text-blue-600',
                'title' => 'Giáo viên mới',
                'text' => "<b>{$r['full_name']}</b> vừa đăng ký tài khoản.",
                'time' => $r['created_at'],
                'link' => 'admin/manage_teachers.php',
                'btn' => 'Xem danh sách'
            ];
        }
    }
    
    // Đơn xin lớp
    $appRes = mysqli_query($link, "SELECT a.status, a.applied_at, COALESCE(u.full_name, '') AS student_name, c.name AS class_name 
                                   FROM applications a 
                                   JOIN students s ON a.student_id = s.id 
                                   LEFT JOIN users u ON s.user_id = u.id 
                                   JOIN classes c ON a.class_id = c.id 
                                   WHERE a.status = 'pending'
                                   ORDER BY a.applied_at DESC LIMIT 6");
    if($appRes){
        while($r = mysqli_fetch_assoc($appRes)) {
            $col1_data[] = [
                'icon' => 'ph-file-text', 'bg' => 'bg-honey-100', 'text_color' => 'text-honey-600',
                'title' => 'Đơn xin vào lớp',
                'text' => "HS <b>{$r['student_name']}</b> xin vào lớp <span class='font-bold text-gray-800'>{$r['class_name']}</span>.",
                'time' => $r['applied_at'],
                'link' => 'admin/manage_applications.php',
                'btn' => 'Duyệt đơn'
            ];
        }
    }
}

// 2. TEACHER (SỬA LỖI TẠI ĐÂY: Bỏ cột created_at trong SELECT)
if($role === 'teacher'){
    $col1_title = "Lớp học & Phân công";
    $col1_desc = "Thông tin về các lớp bạn được phân công giảng dạy.";
    
    // Chỉ lấy id và name, không lấy created_at để tránh lỗi nếu cột không tồn tại
    $clsRes = mysqli_query($link, "SELECT id, name FROM classes WHERE teacher_id=$teacherId ORDER BY id DESC LIMIT 5");
    
    if($clsRes){
        while($r = mysqli_fetch_assoc($clsRes)) {
            $col1_data[] = [
                'icon' => 'ph-chalkboard-teacher', 'bg' => 'bg-purple-100', 'text_color' => 'text-purple-600',
                'title' => 'Phân công mới',
                'text' => "Bạn đã được phân công phụ trách lớp <b>{$r['name']}</b>.",
                'time' => date('Y-m-d H:i'), // Dùng giờ hiện tại vì bảng classes không có created_at
                'link' => "teacher/class_detail.php?id={$r['id']}",
                'btn' => 'Vào lớp ngay'
            ];
        }
    }
}

// 3. STUDENT
if($role === 'student'){
    $col1_title = "Kết quả đăng ký";
    $col1_desc = "Theo dõi trạng thái các lớp bạn đã đăng ký.";
    
    $appRes = mysqli_query($link, "SELECT a.status, a.applied_at, c.name AS class_name 
                                   FROM applications a 
                                   JOIN classes c ON a.class_id = c.id 
                                   WHERE a.student_id = $studentId 
                                   ORDER BY a.applied_at DESC LIMIT 6");
    if($appRes){
        while($r = mysqli_fetch_assoc($appRes)){
            if($r['status'] == 'approved') {
                $icon = 'ph-check-circle'; $bg = 'bg-green-100'; $txt = 'text-green-600'; $title = 'Đăng ký thành công';
                $msg = "Yêu cầu vào lớp <b>{$r['class_name']}</b> đã được chấp nhận.";
            } elseif($r['status'] == 'rejected') {
                $icon = 'ph-x-circle'; $bg = 'bg-red-100'; $txt = 'text-red-600'; $title = 'Đăng ký bị từ chối';
                $msg = "Yêu cầu vào lớp <b>{$r['class_name']}</b> không được duyệt.";
            } else {
                $icon = 'ph-clock'; $bg = 'bg-yellow-100'; $txt = 'text-yellow-600'; $title = 'Đang chờ duyệt';
                $msg = "Đơn xin vào lớp <b>{$r['class_name']}</b> đang chờ giáo viên xem xét.";
            }
            
            $col1_data[] = [
                'icon' => $icon, 'bg' => $bg, 'text_color' => $txt,
                'title' => $title,
                'text' => $msg,
                'time' => $r['applied_at'],
                'link' => 'student/student_classes.php',
                'btn' => 'Xem chi tiết'
            ];
        }
    }
}

// LẤY TIN TỨC
$latestNews = [];
$newsRes = mysqli_query($link, "SELECT id, title, created_at FROM news ORDER BY created_at DESC LIMIT 4");
if($newsRes){
    while($r = mysqli_fetch_assoc($newsRes)) $latestNews[] = $r;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <title>Trung tâm thông báo | Teacher Bee</title>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['"Be Vietnam Pro"', 'sans-serif'] },
                    colors: { honey: { 50:'#FFF8E1', 100:'#FFECB3', 500:'#FFB300', 600:'#FFA000' } }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50 flex font-sans text-gray-900">
    
    <?php include "includes/sidebar.php"; ?>

    <div class="flex-1 p-8 ml-[260px]">
        
        <div class="mb-10">
            <h1 class="text-3xl font-extrabold text-gray-900 flex items-center gap-3">
                <i class="ph-duotone ph-bell-ringing text-honey-500"></i> Trung tâm thông báo
            </h1>
            <p class="text-gray-500 mt-2 text-lg">Chào <b><?php echo $_SESSION['full_name']; ?></b>, dưới đây là các cập nhật mới dành cho bạn.</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <div class="lg:col-span-2">
                <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
                    <div class="p-6 border-b border-gray-50 bg-gray-50/50 flex justify-between items-center">
                        <div>
                            <h2 class="font-bold text-lg text-gray-800"><?php echo $col1_title; ?></h2>
                            <p class="text-xs text-gray-500 mt-1"><?php echo $col1_desc; ?></p>
                        </div>
                        <span class="bg-honey-100 text-honey-700 px-3 py-1 rounded-full text-xs font-bold"><?php echo count($col1_data); ?> mới</span>
                    </div>

                    <div class="divide-y divide-gray-50 p-2">
                        <?php if(empty($col1_data)): ?>
                            <div class="py-16 text-center">
                                <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 text-gray-300 text-4xl">
                                    <i class="ph-duotone ph-bell-slash"></i>
                                </div>
                                <p class="text-gray-500 font-medium">Hiện không có thông báo nào.</p>
                            </div>
                        <?php else: foreach($col1_data as $item): ?>
                            <div class="p-4 hover:bg-gray-50 transition rounded-xl flex gap-4 group">
                                <div class="w-12 h-12 <?php echo $item['bg']; ?> <?php echo $item['text_color']; ?> rounded-xl flex items-center justify-center text-2xl shrink-0">
                                    <i class="ph-duotone <?php echo $item['icon']; ?>"></i>
                                </div>
                                
                                <div class="flex-1">
                                    <div class="flex justify-between items-start">
                                        <h4 class="font-bold text-gray-800 text-sm mb-1"><?php echo $item['title']; ?></h4>
                                        <span class="text-xs text-gray-400 font-medium whitespace-nowrap ml-2">
                                            <?php echo date('H:i d/m', strtotime($item['time'])); ?>
                                        </span>
                                    </div>
                                    <p class="text-sm text-gray-600 leading-relaxed mb-3"><?php echo $item['text']; ?></p>
                                    
                                    <?php if(!empty($item['link'])): ?>
                                    <a href="<?php echo $item['link']; ?>" class="inline-flex items-center gap-1 text-xs font-bold text-gray-500 bg-white border border-gray-200 px-3 py-1.5 rounded-lg hover:text-honey-600 hover:border-honey-500 transition shadow-sm">
                                        <?php echo $item['btn']; ?> <i class="ph-bold ph-arrow-right"></i>
                                    </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; endif; ?>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                
                <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-6">
                    <h3 class="font-bold text-lg text-gray-800 mb-6 flex items-center gap-2">
                        <i class="ph-duotone ph-newspaper text-green-500"></i> Bảng tin nhà trường
                    </h3>
                    
                    <div class="space-y-5">
                        <?php if(empty($latestNews)): ?>
                            <p class="text-gray-400 text-sm text-center">Chưa có tin tức.</p>
                        <?php else: foreach($latestNews as $n): ?>
                        <a href="news_detail.php?id=<?php echo $n['id']; ?>" class="block group">
                            <div class="flex gap-3">
                                <div class="w-10 h-10 rounded-lg bg-gray-50 flex items-center justify-center text-gray-400 shrink-0 group-hover:bg-honey-100 group-hover:text-honey-600 transition">
                                    <i class="ph-bold ph-article"></i>
                                </div>
                                <div>
                                    <h4 class="text-sm font-bold text-gray-800 line-clamp-2 leading-snug group-hover:text-honey-600 transition">
                                        <?php echo $n['title']; ?>
                                    </h4>
                                    <span class="text-[10px] font-bold text-gray-400 uppercase mt-1 block">
                                        <?php echo date('d/m/Y', strtotime($n['created_at'])); ?>
                                    </span>
                                </div>
                            </div>
                        </a>
                        <?php endforeach; endif; ?>
                    </div>

                    <a href="news.php" class="block w-full text-center mt-6 py-2.5 border border-gray-200 rounded-xl text-xs font-bold text-gray-500 hover:bg-gray-50 hover:text-dark-900 transition">
                        Xem tất cả tin tức
                    </a>
                </div>

                <div class="bg-gradient-to-br from-honey-500 to-amber-500 rounded-3xl p-6 text-white text-center shadow-lg shadow-honey-500/20">
                    <i class="ph-duotone ph-lightbulb text-4xl mb-3 text-white/90"></i>
                    <h4 class="font-bold text-lg mb-2">Mẹo nhỏ</h4>
                    <p class="text-sm text-white/80 leading-relaxed">Hãy thường xuyên kiểm tra thông báo để không bỏ lỡ lịch thi quan trọng nhé!</p>
                </div>

            </div>

        </div>
    </div>
</body>
</html>