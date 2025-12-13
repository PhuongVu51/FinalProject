<?php
session_start();
include "connection.php";
include "auth.php";
requireRole(['teacher']);

$uid = intval($_SESSION['user_id'] ?? 0);
if(!$uid){ header("Location: login.php"); exit; }

$redirect = $_POST['redirect'] ?? 'teacher_home.php';
$fullName = trim($_POST['full_name'] ?? '');

if($fullName === ''){
    header("Location: {$redirect}?profile_error=empty_name");
    exit;
}

// Handle avatar upload
$avatarPath = '';
$hasAvatarCol = false;
$colRes = @mysqli_query($link, "SHOW COLUMNS FROM users LIKE 'avatar'");
if($colRes && mysqli_num_rows($colRes) > 0){
    $hasAvatarCol = true;
}

if($hasAvatarCol && isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK){
    $file = $_FILES['avatar'];
    if($file['size'] > 2 * 1024 * 1024){
        header("Location: {$redirect}?profile_error=avatar_too_large");
        exit;
    }

    $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/gif' => 'gif'];
    $mime = mime_content_type($file['tmp_name']);
    if(!isset($allowed[$mime])){
        header("Location: {$redirect}?profile_error=invalid_avatar_type");
        exit;
    }

    $ext = $allowed[$mime];
    $uploadDir = __DIR__ . '/uploads/avatars';
    if(!is_dir($uploadDir)){
        mkdir($uploadDir, 0755, true);
    }
    $fileName = 'avatar_'.$uid.'_'.time().'.'.$ext;
    $dest = $uploadDir.'/'.$fileName;
    if(move_uploaded_file($file['tmp_name'], $dest)){
        $avatarPath = 'uploads/avatars/'.$fileName;
    }
}

$safeName = mysqli_real_escape_string($link, $fullName);
$updates = ["full_name='$safeName'"];
if($avatarPath && $hasAvatarCol){
    $safeAvatar = mysqli_real_escape_string($link, $avatarPath);
    $updates[] = "avatar='$safeAvatar'";
}

mysqli_query($link, "UPDATE users SET ".implode(',', $updates)." WHERE id=$uid");
mysqli_query($link, "UPDATE teachers SET full_name='$safeName' WHERE user_id=$uid");

$_SESSION['full_name'] = $fullName;

header("Location: {$redirect}?profile=updated");
exit;
