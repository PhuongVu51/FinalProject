<?php
// 1. SỬA ĐƯỜNG DẪN & KẾT NỐI
// KHẮC PHỤC LỖI ĐƯỜNG DẪN
$rootPath = dirname(__DIR__); 
include $rootPath . "/connection.php"; 
include $rootPath . "/auth.php"; 
requireRole(['admin']);

// Helper bắt lỗi SQL
function runQuery($link, $sql) {
    $res = mysqli_query($link, $sql);
    if(!$res) die("Lỗi SQL: " . mysqli_error($link));
    return $res;
}

// 2. XỬ LÝ DUYỆT ĐƠN (APPROVE)
if(isset($_GET['approve'])){
    $id = intval($_GET['approve']);
    $app = mysqli_fetch_assoc(runQuery($link, "SELECT * FROM applications WHERE id=$id"));
    
    if($app){
        // Cập nhật lớp cho học sinh
        runQuery($link, "UPDATE students SET class_id={$app['class_id']} WHERE id={$app['student_id']}");
        // Cập nhật trạng thái đơn (bảng hiện không có cột processed_at)
        runQuery($link, "UPDATE applications SET status='approved' WHERE id=$id");
        
        echo "<script>alert('Đã duyệt đơn thành công! Học sinh đã được xếp vào lớp.'); window.location='manage_applications.php';</script>";
    }
}

// 3. XỬ LÝ TỪ CHỐI ĐƠN (REJECT)
if(isset($_GET['reject'])){
    $id = intval($_GET['reject']);
    // Bảng applications không có cột processed_at nên chỉ cập nhật trạng thái
    runQuery($link, "UPDATE applications SET status='rejected' WHERE id=$id");
    echo "<script>alert('Đã từ chối đơn này!'); window.location='manage_applications.php';</script>";
}

// 4. LẤY THỐNG KÊ
$stats = [
    'pending' => mysqli_fetch_assoc(runQuery($link, "SELECT COUNT(*) as c FROM applications WHERE status='pending'"))['c'],
    'approved' => mysqli_fetch_assoc(runQuery($link, "SELECT COUNT(*) as c FROM applications WHERE status='approved'"))['c'],
    'rejected' => mysqli_fetch_assoc(runQuery($link, "SELECT COUNT(*) as c FROM applications WHERE status='rejected'"))['c']
];
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <title>Duyệt đơn | Admin</title>
    <?php include $rootPath . "/includes/header_config.php"; ?>
</head>
<body class="bg-gray-50 flex font-sans text-gray-900">
    
    <?php include $rootPath . "/includes/sidebar.php"; ?>
    
    <div class="flex-1 p-8 ml-[260px]">
        
        <div class="flex justify-between items-end mb-8">
            <div>
                <h1 class="text-2xl font-bold flex items-center gap-3">
                    <i class="ph-duotone ph-files text-honey-500 text-3xl"></i> Duyệt đơn vào lớp
                </h1>
                <p class="text-gray-500 text-sm mt-1">Quản lý và xử lý các yêu cầu xin vào lớp từ học sinh.</p>
            </div>
            <div class="text-right">
                <span class="text-sm font-medium text-gray-500">Hôm nay: <?php echo date('d/m/Y'); ?></span>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-4 hover:shadow-md transition">
                <div class="w-12 h-12 rounded-full bg-yellow-100 text-yellow-600 flex items-center justify-center text-xl">
                    <i class="ph-fill ph-clock-countdown"></i>
                </div>
                <div>
                    <p class="text-gray-500 text-xs font-bold uppercase">Chờ duyệt</p>
                    <p class="text-2xl font-bold text-gray-800"><?php echo $stats['pending']; ?></p>
                </div>
            </div>
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-4 hover:shadow-md transition">
                <div class="w-12 h-12 rounded-full bg-green-100 text-green-600 flex items-center justify-center text-xl">
                    <i class="ph-fill ph-check-circle"></i>
                </div>
                <div>
                    <p class="text-gray-500 text-xs font-bold uppercase">Đã duyệt</p>
                    <p class="text-2xl font-bold text-gray-800"><?php echo $stats['approved']; ?></p>
                </div>
            </div>
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-4 hover:shadow-md transition">
                <div class="w-12 h-12 rounded-full bg-red-100 text-red-600 flex items-center justify-center text-xl">
                    <i class="ph-fill ph-x-circle"></i>
                </div>
                <div>
                    <p class="text-gray-500 text-xs font-bold uppercase">Đã từ chối</p>
                    <p class="text-2xl font-bold text-gray-800"><?php echo $stats['rejected']; ?></p>
                </div>
            </div>
        </div>


        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                <h3 class="font-bold text-gray-800 flex items-center gap-2">
                    <i class="ph-fill ph-clock-countdown text-yellow-500"></i> Danh sách yêu cầu mới
                </h3>
                
                <span class="bg-honey-100 text-honey-700 text-xs font-bold px-3 py-1 rounded-full">
                    <?php echo $stats['pending']; ?> yêu cầu chờ xử lý
                </span>
            </div>
            
            <table class="w-full text-left text-sm">
                <thead class="bg-white border-b text-gray-400 uppercase font-bold text-xs">
                    <tr>
                        <th class="px-6 py-4">Học sinh</th>
                        <th class="px-6 py-4">Mã SV</th>
                        <th class="px-6 py-4">Xin vào lớp</th>
                        <th class="px-6 py-4 text-center">Trạng thái hiện tại</th>
                        <th class="px-6 py-4">Ngày nộp</th>
                        <th class="px-6 py-4 text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php 
                    $sql = "SELECT a.*, u.full_name, s.student_code, c.name as class_name, 
                            sc.name as current_class_name
                            FROM applications a 
                            JOIN students s ON a.student_id=s.id 
                            JOIN users u ON s.user_id=u.id 
                            JOIN classes c ON a.class_id=c.id 
                            LEFT JOIN classes sc ON s.class_id=sc.id  -- Lấy lớp hiện tại của HS
                            WHERE a.status='pending' 
                            ORDER BY a.applied_at ASC";
                    $res = runQuery($link, $sql);
                    
                    if(mysqli_num_rows($res) == 0): 
                        echo "<tr><td colspan='6' class='px-6 py-16 text-center text-gray-400'>
                            <div class='flex flex-col items-center gap-3'>
                                <i class='ph-duotone ph-tray text-4xl text-gray-300'></i>
                                <span>Hiện không có đơn nào cần xử lý.</span>
                            </div>
                        </td></tr>";
                    else: while($r = mysqli_fetch_assoc($res)): ?>
                        <tr class="hover:bg-honey-50/10 transition group">
                            <td class="px-6 py-4 font-bold text-gray-800">
                                <a href="edit_student.php?id=<?php echo $r['student_id']; ?>" class="hover:text-honey-500 hover:underline"><?php echo $r['full_name']; ?></a>
                            </td>
                            <td class="px-6 py-4 font-mono text-gray-700 font-bold text-sm">
                                <?php echo $r['student_code']; ?>
                            </td>
                            <td class="px-6 py-4">
                                <span class="bg-blue-50 text-blue-600 px-3 py-1.5 rounded-lg text-xs font-bold shadow-sm border border-blue-100">
                                    <?php echo $r['class_name']; ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <?php if($r['current_class_name']): ?>
                                    <span class="bg-gray-100 text-gray-700 px-3 py-1 rounded-full text-xs font-medium border border-gray-200" title="Đang ở lớp cũ">
                                        Đang ở: <?php echo $r['current_class_name']; ?>
                                    </span>
                                <?php else: ?>
                                    <span class="bg-red-50 text-red-600 px-3 py-1 rounded-full text-xs font-medium border border-red-100">
                                        Chưa phân lớp
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 text-gray-500 text-xs font-medium">
                                <?php echo date('H:i d/m/Y', strtotime($r['applied_at'])); ?>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="?reject=<?php echo $r['id']; ?>" onclick="return confirm('Bạn muốn từ chối yêu cầu này? Đơn sẽ bị hủy và học sinh sẽ không được xếp lớp.')" class="w-8 h-8 rounded-lg bg-red-50 text-red-500 hover:bg-red-500 hover:text-white flex items-center justify-center transition" title="Từ chối đơn">
                                        <i class="ph-bold ph-x"></i>
                                    </a>
                                    <a href="?approve=<?php echo $r['id']; ?>" onclick="return confirm('Xác nhận duyệt học sinh vào lớp <?php echo $r['class_name']; ?>?')" class="inline-flex items-center gap-1 px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded-xl text-xs font-bold transition shadow-lg shadow-green-500/20 transform active:scale-95">
                                        <i class="ph-bold ph-check"></i> Duyệt
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>