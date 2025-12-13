<?php
include "connection.php";
include "auth.php";
requireRole(['student']);
?>
<!DOCTYPE html>
<html>
<link rel="stylesheet" href="https://site-assets.fontawesome.com/releases/v6.4.2/css/all.css">
<head>
    <title>Tin Tức</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="dashboard_style.css">
    <style>
        .search-bar {
            background: white;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 24px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
        .search-bar input {
            width: 100%;
            padding: 12px 20px;
            border: 2px solid #E0E0E0;
            border-radius: 8px;
            font-size: 15px;
        }
        .search-bar input:focus {
            outline: none;
            border-color: #FFC107;
        }
        .news-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 24px;
            margin-bottom: 30px;
        }
        .news-card {
            background: white;
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            cursor: pointer;
            transition: all 0.3s;
            border-left: 4px solid #FFC107;
        }
        .news-card:hover {
            box-shadow: 0 6px 16px rgba(0,0,0,0.1);
            transform: translateY(-4px);
        }
        .news-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 12px;
        }
        .news-title {
            font-size: 18px;
            font-weight: 700;
            color: #E65100;
            margin-bottom: 8px;
            line-height: 1.4;
        }
        .news-date {
            color: #64748B;
            font-size: 13px;
            display: flex;
            align-items: center;
            gap: 5px;
            white-space: nowrap;
        }
        .news-preview {
            color: #475569;
            font-size: 14px;
            line-height: 1.6;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
            margin-bottom: 12px;
        }
        .read-more {
            color: #FFA000;
            font-weight: 600;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        .news-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #FFC107 0%, #FFA000 100%);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            margin-bottom: 15px;
        }
        
        /* Modal styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            animation: fadeIn 0.3s;
        }
        .modal.active {
            display: flex;
            align-items: center;
            justify-content: center;
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        .modal-content {
            background-color: white;
            padding: 0;
            border-radius: 16px;
            max-width: 700px;
            width: 90%;
            max-height: 80vh;
            overflow-y: auto;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            animation: slideUp 0.3s;
        }
        @keyframes slideUp {
            from {
                transform: translateY(50px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
        .modal-header {
            background: linear-gradient(135deg, #FFC107 0%, #FFA000 100%);
            color: white;
            padding: 24px;
            border-radius: 16px 16px 0 0;
            position: sticky;
            top: 0;
            z-index: 10;
        }
        .modal-title {
            font-size: 24px;
            font-weight: 700;
            margin: 0 0 10px 0;
            line-height: 1.3;
        }
        .modal-date {
            font-size: 14px;
            opacity: 0.9;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .modal-body {
            padding: 30px;
        }
        .modal-body p {
            color: #333;
            font-size: 16px;
            line-height: 1.8;
            margin: 0;
            white-space: pre-wrap;
        }
        .close-btn {
            color: white;
            float: right;
            font-size: 32px;
            font-weight: bold;
            line-height: 1;
            cursor: pointer;
            opacity: 0.8;
            transition: opacity 0.3s;
        }
        .close-btn:hover {
            opacity: 1;
        }
        .stats-banner {
            background: linear-gradient(135deg, #FFC107 0%, #FFA000 100%);
            color: white;
            padding: 30px;
            border-radius: 12px;
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            gap: 20px;
            box-shadow: 0 4px 12px rgba(255, 193, 7, 0.3);
        }
        .stats-banner .icon {
            font-size: 48px;
            opacity: 0.9;
        }
        .stats-banner .content h2 {
            margin: 0 0 5px 0;
            font-size: 28px;
            font-weight: 700;
        }
        .stats-banner .content p {
            margin: 0;
            opacity: 0.9;
            font-size: 15px;
        }
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            background: white;
            border-radius: 12px;
        }
        .empty-state i {
            font-size: 64px;
            color: #E0E0E0;
            margin-bottom: 20px;
        }
        .empty-state p {
            color: #999;
            font-size: 16px;
        }
        .category-badge {
            display: inline-block;
            padding: 4px 12px;
            background: #FFF3E0;
            color: #F57C00;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
            margin-bottom: 12px;
        }
    </style>
</head>
<body>
    <?php include "includes/sidebar.php"; ?>
    <div class="main-wrapper">
        <?php include "includes/topbar.php"; ?>
        <div class="content-scroll">
            
            <!-- Stats Banner -->
            <?php
            $total_news = mysqli_num_rows(mysqli_query($link, "SELECT * FROM news"));
            ?>
            <div class="stats-banner">
                <div class="icon">
                    <i class="fa-solid fa-newspaper"></i>
                </div>
                <div class="content">
                    <h2>Tin Tức</h2>
                    <p>Cập nhật <?php echo $total_news; ?> tin tức mới nhất</p>
                </div>
            </div>

            <!-- Search Bar -->
            <div class="search-bar">
                <input type="text" 
                       id="searchInput" 
                       placeholder="🔍 Tìm kiếm tin tức theo tiêu đề hoặc nội dung..." 
                       onkeyup="searchNews()">
            </div>

            <!-- News Grid -->
            <div class="news-grid">
                <?php 
                $news_query = "SELECT * FROM news ORDER BY created_at DESC";
                $news_result = mysqli_query($link, $news_query);
                
                if(mysqli_num_rows($news_result) > 0):
                    while($news = mysqli_fetch_assoc($news_result)): 
                ?>
                    <div class="news-card searchable-news" 
                         onclick="openModal(<?php echo $news['id']; ?>)"
                         data-title="<?php echo htmlspecialchars($news['title']); ?>"
                         data-content="<?php echo htmlspecialchars($news['content']); ?>"
                         data-date="<?php echo date('d/m/Y H:i', strtotime($news['created_at'])); ?>">
                        
                        <div class="news-icon">
                            <i class="fa-solid fa-bullhorn"></i>
                        </div>
                        
                        <span class="category-badge">
                            <i class="fa-solid fa-tag"></i> Thông báo
                        </span>
                        
                        <div class="news-title">
                            <?php echo $news['title']; ?>
                        </div>
                        
                        <div class="news-date">
                            <i class="fa-regular fa-clock"></i>
                            <?php echo date('d/m/Y H:i', strtotime($news['created_at'])); ?>
                        </div>
                        
                        <div class="news-preview">
                            <?php echo $news['content']; ?>
                        </div>
                        
                        <div class="read-more">
                            Đọc thêm <i class="fa-solid fa-arrow-right"></i>
                        </div>
                    </div>
                <?php 
                    endwhile;
                else:
                ?>
                    <div class="empty-state" style="grid-column: 1/-1;">
                        <i class="fa-regular fa-newspaper"></i>
                        <p>Chưa có tin tức nào được đăng tải</p>
                    </div>
                <?php endif; ?>
            </div>

        </div>
    </div>

    <!-- Modal for News Detail -->
    <div id="newsModal" class="modal" onclick="closeModal(event)">
        <div class="modal-content" onclick="event.stopPropagation()">
            <div class="modal-header">
                <span class="close-btn" onclick="closeModal()">&times;</span>
                <h2 class="modal-title" id="modalTitle"></h2>
                <div class="modal-date" id="modalDate">
                    <i class="fa-regular fa-clock"></i>
                    <span></span>
                </div>
            </div>
            <div class="modal-body">
                <p id="modalContent"></p>
            </div>
        </div>
    </div>

    <script>
        function openModal(newsId) {
            const card = event.currentTarget;
            const title = card.getAttribute('data-title');
            const content = card.getAttribute('data-content');
            const date = card.getAttribute('data-date');
            
            document.getElementById('modalTitle').textContent = title;
            document.getElementById('modalDate').querySelector('span').textContent = date;
            document.getElementById('modalContent').textContent = content;
            
            document.getElementById('newsModal').classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeModal(event) {
            if (!event || event.target.id === 'newsModal') {
                document.getElementById('newsModal').classList.remove('active');
                document.body.style.overflow = 'auto';
            }
        }

        function searchNews() {
            const input = document.getElementById('searchInput').value.toLowerCase();
            const newsCards = document.querySelectorAll('.searchable-news');
            let visibleCount = 0;
            
            newsCards.forEach(card => {
                const title = card.getAttribute('data-title').toLowerCase();
                const content = card.getAttribute('data-content').toLowerCase();
                
                if (title.includes(input) || content.includes(input)) {
                    card.style.display = '';
                    visibleCount++;
                } else {
                    card.style.display = 'none';
                }
            });

            // Show/hide empty state
            const emptyState = document.querySelector('.empty-state');
            if (emptyState) {
                emptyState.style.display = visibleCount === 0 ? 'block' : 'none';
            }
        }

        // Close modal with ESC key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeModal();
            }
        });
    </script>
</body>
</html>