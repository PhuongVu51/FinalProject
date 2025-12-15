<?php
// Resolve root path so includes work when inside /admin
$rootPath = realpath(__DIR__ . '/..');
include $rootPath . "/connection.php";
include $rootPath . "/auth.php";
requireRole(['admin']);

$id = intval($_GET['id']);
$class = mysqli_fetch_assoc(mysqli_query($link, "SELECT * FROM classes WHERE id=$id"));
if(!$class) header("Location: manage_classes.php");

// Lấy danh sách GV
$teachers = mysqli_query($link, "SELECT t.id, u.full_name FROM teachers t JOIN users u ON t.user_id=u.id");

if(isset($_POST['update'])){
    $name = mysqli_real_escape_string($link, $_POST['name']);
    $tid = intval($_POST['teacher_id']);
    $t_sql = ($tid > 0) ? $tid : "NULL";
    
    if(mysqli_query($link, "UPDATE classes SET name='$name', teacher_id=$t_sql WHERE id=$id")){
        echo "<script>alert('Cập nhật thành công!'); window.location='manage_classes.php';</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <title>Cập nhật lớp học</title>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script> tailwind.config = { theme: { extend: { fontFamily: { sans: ['"Be Vietnam Pro"', 'sans-serif'] }, colors: { honey: { 500:'#FFB300', 600:'#FFA000' } } } } } </script>
</head>
<body class="bg-gray-50 flex items-center justify-center min-h-screen font-sans text-gray-900">
    <div class="bg-white p-8 rounded-2xl shadow-lg w-full max-w-md border border-gray-100">
        <h2 class="text-2xl font-bold mb-6 text-center">Cập nhật Lớp học</h2>
        
        <form method="post" class="space-y-5">
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Tên lớp</label>
                <input type="text" name="name" value="<?php echo $class['name']; ?>" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:border-honey-500 outline-none transition" required>
            </div>
            
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Giáo viên chủ nhiệm</label>
                <select name="teacher_id" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:border-honey-500 outline-none bg-white">
                    <option value="0">-- Chưa phân công --</option>
                    <?php while($t = mysqli_fetch_assoc($teachers)): ?>
                        <option value="<?php echo $t['id']; ?>" <?php if($class['teacher_id']==$t['id']) echo 'selected'; ?>>
                            <?php echo $t['full_name']; ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="flex gap-3 pt-4">
                <button type="submit" name="update" class="flex-1 bg-honey-500 hover:bg-honey-600 text-white font-bold py-3 rounded-xl transition shadow-lg shadow-honey-500/30">Lưu thay đổi</button>
                <a href="manage_classes.php" class="px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-600 font-bold rounded-xl transition">Hủy</a>
            </div>
        </form>
    </div>
</body>
</html>