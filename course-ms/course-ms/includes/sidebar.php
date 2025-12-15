<?php
// 1. TỰ ĐỘNG XÁC ĐỊNH ĐƯỜNG DẪN (Root hay Sub-folder)
// Logic: Nếu file connection.php tồn tại ở cùng cấp -> Đang ở Root. Ngược lại -> Đang ở Sub-folder
$path_prefix = file_exists('connection.php') ? '' : '../';

// 2. LẤY THÔNG TIN USER
$role = $_SESSION['role'] ?? 'guest';
$full_name = $_SESSION['full_name'] ?? 'Người dùng';
$current_page = basename($_SERVER['PHP_SELF']);

// 3. HÀM ACTIVE MENU
function isActive($page_name, $current) {
    // So sánh tên file hiện tại với link menu để highlight
    return ($current == $page_name) ? 'bg-honey-50 text-honey-600 shadow-sm border-r-4 border-honey-500' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-50';
}
?>

<link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

<script src="https://unpkg.com/@phosphor-icons/web"></script>

<script src="https://cdn.tailwindcss.com"></script>
<script>
    tailwind.config = {
        theme: {
            extend: {
                fontFamily: { sans: ['"Be Vietnam Pro"', 'sans-serif'] },
                colors: {
                    honey: { 
                        50: '#FFF8E1', 
                        100: '#FFECB3', 
                        400: '#FFCA28',
                        500: '#FFB300', 
                        600: '#FFA000',
                        700: '#B45309'
                    },
                    dark: {
                        900: '#2D3436',
                        800: '#636E72',
                        100: '#F9FAFB'
                    }
                }
            }
        }
    }
</script>

<aside class="w-[260px] bg-white border-r border-gray-200 flex flex-col fixed h-full z-30 font-sans transition-all duration-300 left-0 top-0">
    
    <div class="h-20 flex items-center gap-3 px-6 border-b border-gray-50 shrink-0">
        <div class="w-10 h-10 bg-gradient-to-br from-honey-400 to-honey-600 rounded-xl flex items-center justify-center text-white shadow-lg shadow-honey-500/30">
            <i class="ph-fill ph-student text-2xl"></i>
        </div>
        <div>
            <h1 class="text-xl font-extrabold text-gray-800 tracking-tight">TeacherBee</h1>
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Education</p>
        </div>
    </div>

    <nav class="flex-1 overflow-y-auto py-6 px-4 space-y-1 custom-scrollbar">
        
        <?php if($role == 'teacher'): ?>
            <div class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3 px-4">Giảng dạy</div>
            
            <a href="<?php echo $path_prefix; ?>teacher/teacher_dashboard.php" class="flex items-center gap-3 px-4 py-3 rounded-xl font-bold transition-all <?php echo isActive('teacher_dashboard.php', $current_page); ?>">
                <i class="ph-duotone ph-squares-four text-xl"></i> Trang chủ
            </a>
            <a href="<?php echo $path_prefix; ?>teacher/teacher_classes.php" class="flex items-center gap-3 px-4 py-3 rounded-xl font-bold transition-all <?php echo isActive('teacher_classes.php', $current_page) . isActive('class_detail.php', $current_page); ?>">
                <i class="ph-duotone ph-chalkboard-teacher text-xl"></i> Lớp học
            </a>
            <a href="<?php echo $path_prefix; ?>teacher/manage_exams.php" class="flex items-center gap-3 px-4 py-3 rounded-xl font-bold transition-all <?php echo isActive('manage_exams.php', $current_page) . isActive('create_exam.php', $current_page) . isActive('grading.php', $current_page) . isActive('edit_exam.php', $current_page); ?>">
                <i class="ph-duotone ph-file-text text-xl"></i> Bài thi & Điểm
            </a>

            <?php elseif($role == 'admin'): ?>
            <div class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3 px-4">Quản trị hệ thống</div>
            <a href="<?php echo $path_prefix; ?>admin/home.php" class="flex items-center gap-3 px-4 py-3 rounded-xl font-bold transition-all <?php echo isActive('home.php', $current_page); ?>">
                <i class="ph-duotone ph-chart-pie-slice text-xl"></i> Tổng quan
            </a>
            <a href="<?php echo $path_prefix; ?>admin/manage_classes.php" class="flex items-center gap-3 px-4 py-3 rounded-xl font-bold transition-all <?php echo isActive('manage_classes.php', $current_page); ?>">
                <i class="ph-duotone ph-chalkboard text-xl"></i> Quản lý Lớp
            </a>
            <a href="<?php echo $path_prefix; ?>admin/manage_teachers.php" class="flex items-center gap-3 px-4 py-3 rounded-xl font-bold transition-all <?php echo isActive('manage_teachers.php', $current_page); ?>">
                <i class="ph-duotone ph-chalkboard-teacher text-xl"></i> Giáo viên
            </a>
            <a href="<?php echo $path_prefix; ?>admin/manage_students.php" class="flex items-center gap-3 px-4 py-3 rounded-xl font-bold transition-all <?php echo isActive('manage_students.php', $current_page); ?>">
                <i class="ph-duotone ph-student text-xl"></i> Học sinh
            </a>
            <a href="<?php echo $path_prefix; ?>admin/manage_applications.php" class="flex items-center gap-3 px-4 py-3 rounded-xl font-bold transition-all <?php echo isActive('manage_applications.php', $current_page); ?>">
                <i class="ph-duotone ph-files text-xl"></i> Duyệt đơn
            </a>
             <a href="<?php echo $path_prefix; ?>admin/manage_news.php" class="flex items-center gap-3 px-4 py-3 rounded-xl font-bold transition-all <?php echo isActive('manage_news.php', $current_page); ?>">
                <i class="ph-duotone ph-newspaper text-xl"></i> Tin tức
            </a>

        <?php else: ?>
            <div class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3 px-4">Học tập</div>
            <a href="<?php echo $path_prefix; ?>student/student_home.php" class="flex items-center gap-3 px-4 py-3 rounded-xl font-bold transition-all <?php echo isActive('student_home.php', $current_page); ?>">
                <i class="ph-duotone ph-house text-xl"></i> Trang chủ
            </a>
            <a href="<?php echo $path_prefix; ?>student/student_classes.php" class="flex items-center gap-3 px-4 py-3 rounded-xl font-bold transition-all <?php echo isActive('student_classes.php', $current_page); ?>">
                <i class="ph-duotone ph-books text-xl"></i> Lớp học
            </a>
            <a href="<?php echo $path_prefix; ?>student/student_dashboard.php" class="flex items-center gap-3 px-4 py-3 rounded-xl font-bold transition-all <?php echo isActive('student_dashboard.php', $current_page); ?>">
                <i class="ph-duotone ph-chart-bar text-xl"></i> Bảng điểm
            </a>
        <?php endif; ?>

        <div class="mt-8 text-xs font-bold text-gray-400 uppercase tracking-wider mb-3 px-4">Thông tin</div>
        <a href="<?php echo $path_prefix; ?>notifications.php" class="flex items-center gap-3 px-4 py-3 rounded-xl font-bold transition-all <?php echo isActive('notifications.php', $current_page); ?>">
            <i class="ph-duotone ph-bell text-xl"></i> Thông báo
        </a>
        <a href="<?php echo $path_prefix; ?>news.php" class="flex items-center gap-3 px-4 py-3 rounded-xl font-bold transition-all <?php echo isActive('news.php', $current_page) . isActive('news_detail.php', $current_page); ?>">
            <i class="ph-duotone ph-newspaper text-xl"></i> Tin tức
        </a>

    </nav>

    <div class="p-4 border-t border-gray-100 bg-gray-50/50 shrink-0">
        <div class="flex items-center gap-3 mb-3 p-2 rounded-lg hover:bg-white transition cursor-default group">
            <div class="w-10 h-10 rounded-full bg-white border border-gray-200 flex items-center justify-center text-honey-600 font-bold text-lg shadow-sm group-hover:scale-105 transition">
                <?php echo substr($full_name, 0, 1); ?>
            </div>
            <div class="flex-1 overflow-hidden">
                <div class="text-sm font-bold text-gray-800 truncate" title="<?php echo $full_name; ?>"><?php echo $full_name; ?></div>
                <div class="text-[10px] font-bold uppercase text-gray-400 tracking-wide"><?php echo ucfirst($role); ?></div>
            </div>
        </div>
        
        <a href="<?php echo $path_prefix; ?>logout.php" class="flex items-center justify-center gap-2 w-full py-2 bg-white border border-gray-200 text-red-500 rounded-lg text-xs font-bold hover:bg-red-50 hover:border-red-200 transition shadow-sm">
            <i class="ph-bold ph-sign-out"></i> Đăng xuất
        </a>
    </div>

</aside>