<?php
function checkLogin($link) {
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (isset($_SESSION['user_id'])) return true;

    // Check Cookie
    if (isset($_COOKIE['remember_token'])) {
        $token = mysqli_real_escape_string($link, $_COOKIE['remember_token']);
        $res = mysqli_query($link, "SELECT * FROM users WHERE remember_token = '$token'");
        if ($user = mysqli_fetch_assoc($res)) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['full_name'] = $user['full_name'];
            
            // Map role_id to role string
            if ($user['role_id'] == 1) $_SESSION['role'] = 'admin';
            elseif ($user['role_id'] == 2) $_SESSION['role'] = 'teacher';
            elseif ($user['role_id'] == 3) $_SESSION['role'] = 'student';
            
            loadSubId($link, $user);
            return true;
        }
    }
    return false;
}

function loadSubId($link, $user) {
    // Map role_id to role string if not already set
    if (!isset($_SESSION['role'])) {
        if ($user['role_id'] == 1) $_SESSION['role'] = 'admin';
        elseif ($user['role_id'] == 2) $_SESSION['role'] = 'teacher';
        elseif ($user['role_id'] == 3) $_SESSION['role'] = 'student';
    }
    
    if ($_SESSION['role'] == 'teacher') {
        $r = mysqli_fetch_assoc(mysqli_query($link, "SELECT id FROM teachers WHERE user_id=".$user['id']));
        if($r) $_SESSION['teacher_id'] = $r['id'];
    } elseif ($_SESSION['role'] == 'student') {
        $r = mysqli_fetch_assoc(mysqli_query($link, "SELECT id FROM students WHERE user_id=".$user['id']));
        if($r) $_SESSION['student_id'] = $r['id'];
    }
}

function requireRole($allowed_roles) {
    global $link;
    if (!checkLogin($link)) { header("Location: login.php"); exit(); }
    if (!in_array($_SESSION['role'], $allowed_roles)) {
        die("<h1>🚫 Access Denied</h1><p>Bạn không có quyền vào trang này.</p><a href='logout.php'>Đăng xuất</a>");
    }
}
?>