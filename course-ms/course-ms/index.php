<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang chủ - MathsAM (Mô phỏng)</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <style>
        body { background-color: #FFF8E1; }
        .navbar-custom {
            background-color: #FFC107; /* Màu vàng ong */
            padding: 10px 20px;
        }
        .navbar-custom .nav-link { color: #333; font-weight: 500; }
        .navbar-custom .navbar-brand { font-weight: 700; color: #E65100; }
        .btn-login {
            border: 2px solid #E65100;
            color: #E65100 !important;
            border-radius: 20px;
            font-weight: bold;
        }
        .hero-section {
            background-image: url('background-bee.jpg');
            background-size: cover;
            padding: 100px 0;
            text-align: center;
        }
        .hero-section h1 {
            color: #E65100;
            font-weight: bold;
            font-size: 48px;
        }
    </style>
</head>
<body>
    
    <nav class="navbar navbar-expand-lg navbar-custom">
        <a class="navbar-brand" href="#">MathsAM</a>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item active"><a class="nav-link" href="#">Trang chủ</a></li>
                <li class="nav-item"><a class="nav-link" href="#">Giới thiệu</a></li>
                <li class="nav-item"><a class="nav-link" href="#">Khoá học</a></li>
                <li class="nav-item"><a class="nav-link" href="#">Học tập</a></li>
            </ul>
            <a href="login.php" class="btn btn-light btn-login">Đăng nhập</a>
        </div>
    </nav>

    <div class="hero-section">
        <h1>Học toán Tiếng Anh</h1>
        <p>Ưu đãi mùa tựu trường - Giảm ngay 30%!</p>
        <a href="register.php" class="btn btn-warning btn-lg">Đăng ký ngay <i class="fas fa-arrow-right"></i></a>
    </div>

</body>
</html>