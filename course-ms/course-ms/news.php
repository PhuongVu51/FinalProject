<?php
include "connection.php"; include "auth.php"; requireRole(['teacher','student','admin']);

// Xử lý tìm kiếm (nếu cần sau này)
$search = $_GET['q'] ?? '';
$sql = "SELECT * FROM news ORDER BY created_at DESC";
if($search){
    $s = mysqli_real_escape_string($link, $search);
    $sql = "SELECT * FROM news WHERE title LIKE '%$s%' OR content LIKE '%$s%' ORDER BY created_at DESC";
}
$news = mysqli_query($link, $sql);

// Mảng màu ngẫu nhiên cho Icon để đỡ nhàm chán
$colors = [
    ['bg' => 'bg-blue-50', 'text' => 'text-blue-600', 'icon' => 'ph-megaphone'],
    ['bg' => 'bg-green-50', 'text' => 'text-green-600', 'icon' => 'ph-plant'],
    ['bg' => 'bg-purple-50', 'text' => 'text-purple-600', 'icon' => 'ph-star'],
    ['bg' => 'bg-orange-50', 'text' => 'text-orange-600', 'icon' => 'ph-fire'],
    ['bg' => 'bg-pink-50', 'text' => 'text-pink-600', 'icon' => 'ph-heart'],
];
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <title>Tin tức nhà trường</title>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script> tailwind.config = { theme: { extend: { fontFamily: { sans: ['"Be Vietnam Pro"', 'sans-serif'] }, colors: { honey: { 50:'#FFF8E1', 100:'#FFECB3', 500:'#FFB300', 600:'#FFA000' } } } } } </script>
</head>
<body class="bg-gray-50 min-h-screen font-sans text-gray-900">
    
    <?php include "includes/sidebar.php"; ?>

    <div class="flex-1 p-8 ml-[260px]">
        
        <div class="flex flex-col md:flex-row justify-between items-center mb-10 gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-3">
                    <span class="bg-honey-500 text-white p-2 rounded-xl text-2xl shadow-lg shadow-honey-500/30"><i class="ph-bold ph-newspaper"></i></span>
                    Bảng Tin & Sự Kiện
                </h1>
                <p class="text-gray-500 mt-1 text-sm ml-14">Cập nhật những thông tin mới nhất từ nhà trường.</p>
            </div>
            
            <form class="relative w-full md:w-96">
                <i class="ph-bold ph-magnifying-glass absolute left-4 top-3.5 text-gray-400 text-lg"></i>
                <input type="text" name="q" value="<?php echo htmlspecialchars($search); ?>" placeholder="Tìm kiếm tin tức..." class="w-full pl-12 pr-4 py-3 rounded-xl border-none shadow-sm bg-white focus:ring-2 focus:ring-honey-500 outline-none transition placeholder-gray-400 font-medium">
            </form>
        </div>

        <?php if(mysqli_num_rows($news) == 0): ?>
            <div class="text-center py-20">
                <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4 text-gray-300 text-5xl"><i class="ph-duotone ph-newspaper"></i></div>
                <p class="text-gray-500 font-medium">Chưa có tin tức nào được đăng tải.</p>
            </div>
        <?php else: ?>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <?php 
                $count = 0;
                while($n = mysqli_fetch_assoc($news)): 
                    $count++;
                    // Lấy style ngẫu nhiên
                    $style = $colors[array_rand($colors)];
                    
                    // --- BÀI ĐẦU TIÊN (FEATURED) ---
                    // Sẽ hiển thị to gấp đôi (span 2 cột) và nổi bật hơn
                    if($count == 1): 
                ?>
                    <div class="md:col-span-2 relative group cursor-pointer" onclick="window.location='news_detail.php?id=<?php echo $n['id']; ?>'">
                        <div class="absolute inset-0 bg-gradient-to-br from-honey-400 to-yellow-600 rounded-3xl transform rotate-1 group-hover:rotate-2 transition-transform opacity-20"></div>
                        <div class="relative bg-gradient-to-br from-honey-500 to-yellow-600 rounded-3xl p-8 text-white h-full flex flex-col justify-between shadow-xl shadow-honey-500/30 overflow-hidden">
                            <i class="ph-duotone ph-confetti absolute top-0 right-0 text-[200px] opacity-10 transform translate-x-10 -translate-y-10"></i>
                            
                            <div class="relative z-10">
                                <span class="bg-white/20 backdrop-blur-md px-3 py-1 rounded-lg text-xs font-bold uppercase tracking-wider mb-4 inline-block">📌 Tin nổi bật</span>
                                <h2 class="text-3xl font-bold mb-3 leading-tight"><?php echo $n['title']; ?></h2>
                                <p class="text-white/90 line-clamp-2 mb-6 text-sm md:text-base"><?php echo strip_tags($n['content']); ?></p>
                            </div>
                            
                            <div class="relative z-10 flex items-center gap-2 text-xs font-bold uppercase tracking-wider opacity-80">
                                <i class="ph-bold ph-calendar"></i> <?php echo date('d \t\h\á\n\g m, Y', strtotime($n['created_at'])); ?>
                                <span class="w-1 h-1 bg-white rounded-full mx-2"></span>
                                <span>Admin</span>
                            </div>
                        </div>
                    </div>

                <?php 
                    // --- CÁC BÀI CÒN LẠI ---
                    else: 
                ?>
                    <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all duration-300 cursor-pointer group flex flex-col h-full" onclick="window.location='news_detail.php?id=<?php echo $n['id']; ?>'">
                        <div class="flex items-start justify-between mb-4">
                            <div class="w-12 h-12 <?php echo $style['bg']; ?> <?php echo $style['text']; ?> rounded-xl flex items-center justify-center text-2xl">
                                <i class="ph-duotone <?php echo $style['icon']; ?>"></i>
                            </div>
                            <span class="text-xs font-bold text-gray-400 bg-gray-50 px-2 py-1 rounded-md">
                                <?php echo date('d/m', strtotime($n['created_at'])); ?>
                            </span>
                        </div>
                        
                        <h3 class="font-bold text-lg text-gray-800 mb-3 line-clamp-2 group-hover:text-honey-600 transition-colors">
                            <?php echo $n['title']; ?>
                        </h3>
                        
                        <p class="text-gray-500 text-sm line-clamp-3 mb-4 flex-1">
                            <?php echo strip_tags($n['content']); ?>
                        </p>
                        
                        <div class="pt-4 border-t border-gray-50 flex items-center text-honey-600 font-bold text-xs uppercase tracking-wide group-hover:gap-2 transition-all">
                            Đọc tiếp <i class="ph-bold ph-arrow-right ml-1"></i>
                        </div>
                    </div>
                <?php endif; endwhile; ?>
            </div>

        <?php endif; ?>
    </div>
</body>
</html>