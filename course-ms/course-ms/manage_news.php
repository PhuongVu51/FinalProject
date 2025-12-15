<?php
// 1. SỬA ĐƯỜNG DẪN & KẾT NỐI
// KHẮC PHỤC LỖI ĐƯỜNG DẪN
$rootPath = dirname(__DIR__); 
include $rootPath . "/connection.php"; 
include $rootPath . "/auth.php"; 
requireRole(['admin']);

// Hàm bắt lỗi SQL
function runQuery($link, $sql) {
    $res = mysqli_query($link, $sql);
    if(!$res) die("Lỗi SQL: " . mysqli_error($link));
    return $res;
}

// 2. XỬ LÝ ĐĂNG BÀI
if(isset($_POST['add'])){
    $t = mysqli_real_escape_string($link, $_POST['title']);
    $c = mysqli_real_escape_string($link, $_POST['content']);
    runQuery($link, "INSERT INTO news (title, content) VALUES ('$t','$c')");
    header("Location: manage_news.php"); exit;
}

// 3. XỬ LÝ XÓA
if(isset($_GET['del'])){
    $id = intval($_GET['del']);
    runQuery($link, "DELETE FROM news WHERE id=$id");
    header("Location: manage_news.php"); exit;
}

// 4. LẤY THỐNG KÊ
$total_news = mysqli_fetch_assoc(runQuery($link, "SELECT COUNT(*) as c FROM news"))['c'];
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <title>Quản lý tin tức | Admin</title>
</head>
<body class="bg-gray-50 flex font-sans text-gray-900">
    
    <?php include $rootPath . "/includes/sidebar.php"; ?>
    
    <div class="flex-1 p-8 ml-[260px]">
        
        <div class="flex justify-between items-end mb-8">
            <div>
                <h1 class="text-2xl font-bold flex items-center gap-3">
                    <i class="ph-duotone ph-newspaper text-honey-500 text-3xl"></i> Bảng tin Nhà trường
                </h1>
                <p class="text-gray-500 text-sm mt-1">Quản lý và đăng tải các thông báo chính thức của trường.</p>
            </div>
            <div class="text-right">
                <span class="text-sm font-medium text-gray-500">Tổng bài viết: **<?php echo $total_news; ?>**</span>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            
            <div class="lg:col-span-4 h-fit sticky top-8">
                <div class="bg-white p-6 rounded-2xl shadow-lg border border-gray-100 relative overflow-hidden">
                    <div class="absolute -top-10 -right-10 w-32 h-32 bg-honey-50 rounded-full blur-2xl opacity-60"></div>
                    
                    <h3 class="font-bold text-lg mb-6 text-gray-800 flex items-center gap-2 relative z-10">
                        <span class="w-8 h-8 rounded-lg bg-honey-100 text-honey-600 flex items-center justify-center text-sm">
                            <i class="ph-bold ph-pencil-simple"></i>
                        </span>
                        Đăng bài viết mới
                    </h3>
                    
                    <form method="post" class="space-y-5 relative z-10">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Tiêu đề <span class="text-red-500">*</span></label>
                            <input type="text" name="title" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:border-honey-500 focus:ring-2 focus:ring-honey-100 outline-none transition font-medium" placeholder="VD: Thông báo nghỉ lễ..." required>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Nội dung <span class="text-red-500">*</span></label>
                            <textarea name="content" rows="6" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:border-honey-500 focus:ring-2 focus:ring-honey-100 outline-none transition resize-none" placeholder="Nhập nội dung..."></textarea>
                        </div>
                        <div class="pt-2">
                            <button name="add" class="w-full py-3.5 bg-honey-500 text-white font-bold rounded-xl hover:bg-honey-600 hover:-translate-y-1 transition-all shadow-lg shadow-honey-500/20 flex items-center justify-center gap-2">
                                <i class="ph-bold ph-paper-plane-right"></i> Đăng bài
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="lg:col-span-8">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    
                    <div class="p-5 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-gray-50/50">
                        <h3 class="font-bold text-gray-800 whitespace-nowrap">Danh sách Bài viết</h3>
                        
                        <div class="relative flex-1 sm:w-64">
                            <i class="ph-bold ph-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                            <input type="text" placeholder="Tìm tiêu đề..." class="w-full pl-9 pr-4 py-2 border border-gray-200 rounded-lg text-sm focus:border-honey-500 outline-none">
                        </div>
                    </div>

                    <div class="divide-y divide-gray-100">
                        <?php 
                        $res = runQuery($link, "SELECT * FROM news ORDER BY created_at DESC");
                        
                        if(mysqli_num_rows($res) == 0):
                            echo "<div class='p-12 text-center text-gray-400'>
                                <i class='ph-duotone ph-newspaper text-4xl text-gray-300'></i>
                                <p class='mt-2'>Chưa có bài viết nào được đăng.</p>
                            </div>";
                        else: while($r = mysqli_fetch_assoc($res)): ?>
                        <div class="p-5 hover:bg-honey-50/10 transition group flex justify-between items-start gap-4">
                            
                            <div class="flex-1">
                                <a href="edit_news.php?id=<?php echo $r['id']; ?>" class="font-bold text-lg text-gray-800 hover:text-honey-600 transition block">
                                    <?php echo $r['title']; ?>
                                </a>
                                <p class="text-sm text-gray-500 line-clamp-2 mt-1"><?php echo strip_tags($r['content']); ?></p>
                                <div class="text-xs font-bold text-gray-400 mt-2 flex items-center gap-1">
                                    <i class="ph-bold ph-clock"></i> Đăng lúc: <?php echo date('H:i d/m/Y', strtotime($r['created_at'])); ?>
                                </div>
                            </div>
                            
                            <div class="flex-shrink-0 flex gap-2 pt-1 opacity-70 group-hover:opacity-100 transition">
                                <a href="edit_news.php?id=<?php echo $r['id']; ?>" class="w-8 h-8 rounded-lg bg-gray-50 text-gray-400 hover:bg-blue-50 hover:text-blue-600 flex items-center justify-center transition" title="Sửa bài viết">
                                    <i class="ph-bold ph-pencil-simple"></i>
                                </a>
                                <a href="?del=<?php echo $r['id']; ?>" onclick="return confirm('Xóa vĩnh viễn tin này?')" class="w-8 h-8 rounded-lg bg-gray-50 text-gray-400 hover:bg-red-50 hover:text-red-600 flex items-center justify-center transition" title="Xóa bài viết">
                                    <i class="ph-bold ph-trash"></i>
                                </a>
                            </div>

                        </div>
                        <?php endwhile; endif; ?>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
</body>
</html>