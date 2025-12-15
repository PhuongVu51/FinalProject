<?php
include "connection.php";
include "auth.php";
requireRole(['admin','teacher','student']);

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$news = null;

if($id > 0){
    $res = mysqli_query($link, "SELECT * FROM news WHERE id=$id");
    $news = mysqli_fetch_assoc($res);
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <title><?php echo $news ? htmlspecialchars($news['title']) : 'Tin tức'; ?> | Teacher Bee</title>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { theme: { extend: { fontFamily: { sans: ['"Be Vietnam Pro"', 'sans-serif'] }, colors: { honey: { 50:'#FFF8E1', 500:'#FFB300', 600:'#FFA000' } } } } }
    </script>
</head>
<body class="bg-gray-50 flex font-sans text-gray-900">
    
    <?php include "includes/sidebar.php"; ?>

    <div class="flex-1 p-8 ml-[260px]">
        
        <div class="mb-6">
            <a href="news.php" class="inline-flex items-center gap-2 text-gray-500 hover:text-honey-600 font-bold transition">
                <i class="ph-bold ph-arrow-left"></i> Quay lại bảng tin
            </a>
        </div>

        <?php if(!$news): ?>
            <div class="bg-white p-12 rounded-3xl border border-dashed border-gray-300 text-center">
                <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 text-gray-400">
                    <i class="ph-duotone ph-file-x text-3xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-800">Bài viết không tồn tại</h3>
                <p class="text-gray-500 mt-2">Có thể bài viết đã bị xóa hoặc đường dẫn không đúng.</p>
                <a href="news.php" class="inline-block mt-6 px-6 py-2 bg-honey-500 text-white rounded-xl font-bold hover:bg-honey-600 transition">Về trang tin tức</a>
            </div>
        <?php else: ?>
            <div class="bg-white p-8 md:p-12 rounded-3xl shadow-sm border border-gray-100 max-w-4xl mx-auto">
                
                <header class="mb-8 border-b border-gray-100 pb-8">
                    <div class="flex items-center gap-3 text-sm text-gray-500 mb-4">
                        <span class="bg-honey-50 text-honey-600 px-3 py-1 rounded-full font-bold text-xs uppercase tracking-wider">Thông báo</span>
                        <span class="flex items-center gap-1"><i class="ph-bold ph-calendar-blank"></i> <?php echo date('d/m/Y', strtotime($news['created_at'])); ?></span>
                        <span class="flex items-center gap-1"><i class="ph-bold ph-clock"></i> <?php echo date('H:i', strtotime($news['created_at'])); ?></span>
                    </div>
                    <h1 class="text-3xl md:text-4xl font-bold text-gray-900 leading-tight">
                        <?php echo htmlspecialchars($news['title']); ?>
                    </h1>
                </header>

                <article class="prose prose-lg text-gray-700 leading-relaxed whitespace-pre-line">
                    <?php 
                        // Hiển thị nội dung, chuyển đổi xuống dòng thành thẻ <br>
                        echo $news['content']; 
                    ?>
                </article>

            </div>
        <?php endif; ?>

    </div>
</body>
</html>