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

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if($id == 0) {
    header("Location: manage_news.php"); exit;
}

$sql = "SELECT * FROM news WHERE id=$id";
$news = mysqli_fetch_assoc(runQuery($link, $sql));

if(!$news) {
    header("Location: manage_news.php"); exit;
}

// XỬ LÝ CẬP NHẬT
$message = '';
if(isset($_POST['update'])){
    $title = mysqli_real_escape_string($link, $_POST['title']);
    $content = mysqli_real_escape_string($link, $_POST['content']);
    
    runQuery($link, "UPDATE news SET title='$title', content='$content' WHERE id=$id");
    $message = '<p class="text-green-600 font-bold">Cập nhật bài viết thành công!</p>';
    
    // Lấy lại dữ liệu mới sau khi update
    $news = mysqli_fetch_assoc(runQuery($link, $sql));
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <title>Sửa tin tức: <?php echo $news['title']; ?> | Admin</title>
</head>
<body class="bg-gray-50 flex font-sans text-gray-900">
    
    <?php include $rootPath . "/includes/sidebar.php"; ?>

    <div class="flex-1 p-8 ml-[260px]">
        
        <div class="mb-6">
            <a href="manage_news.php" class="inline-flex items-center gap-2 text-gray-500 hover:text-honey-500 transition font-bold text-sm">
                <i class="ph-bold ph-arrow-left"></i> Quay lại Quản lý Tin tức
            </a>
        </div>

        <div class="bg-white p-8 rounded-2xl shadow-lg w-full max-w-4xl mx-auto border border-gray-100">
            
            <h1 class="text-3xl font-bold mb-2 text-gray-800 flex items-center gap-3">
                <i class="ph-duotone ph-pencil-simple text-honey-500"></i> Chỉnh sửa Tin tức
            </h1>
            <p class="text-sm text-gray-500 mb-6">Đăng lúc: **<?php echo date('H:i d/m/Y', strtotime($news['created_at'])); ?>**</p>
            
            <?php if($message): ?>
                <div class="bg-green-100 border border-green-200 text-green-700 px-4 py-3 rounded-xl mb-6">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <form method="post" class="space-y-6">
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Tiêu đề</label>
                    <input type="text" name="title" value="<?php echo $news['title']; ?>" class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl focus:border-honey-500 focus:ring-2 focus:ring-honey-100 outline-none transition font-medium" required>
                </div>
                
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Nội dung</label>
                    <textarea name="content" rows="12" class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl focus:border-honey-500 focus:ring-2 focus:ring-honey-100 outline-none transition resize-y" required><?php echo $news['content']; ?></textarea>
                </div>

                <div class="flex gap-3 pt-4">
                    <button type="submit" name="update" class="flex-1 px-6 py-3 bg-honey-500 hover:bg-honey-600 text-white font-bold rounded-xl transition shadow-lg shadow-honey-500/30 flex items-center justify-center gap-2 transform active:scale-95">
                        <i class="ph-bold ph-check-circle"></i> Cập nhật bài viết
                    </button>
                    <a href="manage_news.php" class="px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-600 font-bold rounded-xl transition flex items-center gap-2">
                        <i class="ph-bold ph-x"></i> Hủy
                    </a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>