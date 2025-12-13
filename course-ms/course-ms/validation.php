<?php
session_start();
include "connection.php";
include "auth.php";

if (!isset($_POST['login'])) { header("Location: login.php"); exit(); }

$username = trim($_POST['username']);
$password = $_POST['password'];

if (empty($username) || empty($password)) {
    header("Location: login.php?error=empty"); exit();
}

$clean_user = mysqli_real_escape_string($link, $username);
$hash_pass = md5($password);

$sql = "SELECT * FROM users WHERE username='$clean_user' AND password='$hash_pass'";
$res = mysqli_query($link, $sql);

if (mysqli_num_rows($res) === 1) {
    $user = mysqli_fetch_assoc($res);
    
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['role'] = $user['role'];
    $_SESSION['full_name'] = $user['full_name'];
    loadSubId($link, $user);

    if (isset($_POST['remember'])) {
        $token = bin2hex(random_bytes(16));
        mysqli_query($link, "UPDATE users SET remember_token='$token' WHERE id=".$user['id']);
        setcookie('remember_token', $token, time() + (86400 * 30), "/");
    }

    if ($user['role'] == 'student') header("Location: student_home.php");
    else header("Location: home.php");
    exit();
} else {
    header("Location: login.php?error=fail"); exit();
}
?>