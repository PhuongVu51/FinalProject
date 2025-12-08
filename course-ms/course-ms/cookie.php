<?php
/**
 * Cookie Management System
 * Quản lý tất cả cookies của trang web
 */

// ============================================
// Thời hạn cookie (tính bằng giây)
// ============================================
define('COOKIE_EXPIRE_TIME', time() + (86400 * 30)); // 30 ngày
define('COOKIE_EXPIRE_SESSION', 0); // Hết khi đóng trình duyệt
define('COOKIE_PATH', '/');
define('COOKIE_DOMAIN', '');

// ============================================
// Các hằng số tên cookie
// ============================================
define('COOKIE_USER_ID', 'user_id');
define('COOKIE_USERNAME', 'username');
define('COOKIE_EMAIL', 'user_email');
define('COOKIE_ROLE', 'user_role');
define('COOKIE_LOGIN_TIME', 'login_time');
define('COOKIE_REMEMBER_ME', 'remember_me');

/**
 * Hàm tạo cookie người dùng sau khi đăng nhập
 * @param int $userId - ID của người dùng
 * @param string $username - Tên đăng nhập
 * @param string $email - Email của người dùng
 * @param string $role - Vai trò (admin, teacher, student)
 * @param bool $rememberMe - Có nhớ tôi hay không
 */
function setUserCookies($userId, $username, $email, $role, $rememberMe = false) {
    $expireTime = $rememberMe ? COOKIE_EXPIRE_TIME : COOKIE_EXPIRE_SESSION;
    
    setcookie(COOKIE_USER_ID, $userId, $expireTime, COOKIE_PATH);
    setcookie(COOKIE_USERNAME, $username, $expireTime, COOKIE_PATH);
    setcookie(COOKIE_EMAIL, $email, $expireTime, COOKIE_PATH);
    setcookie(COOKIE_ROLE, $role, $expireTime, COOKIE_PATH);
    setcookie(COOKIE_LOGIN_TIME, time(), $expireTime, COOKIE_PATH);
    
    if ($rememberMe) {
        setcookie(COOKIE_REMEMBER_ME, '1', COOKIE_EXPIRE_TIME, COOKIE_PATH);
    }
}

/**
 * Hàm kiểm tra người dùng đã đăng nhập hay chưa
 * @return bool - True nếu đã đăng nhập, False nếu chưa
 */
function isUserLoggedIn() {
    return isset($_COOKIE[COOKIE_USER_ID]) && !empty($_COOKIE[COOKIE_USER_ID]);
}

/**
 * Hàm lấy thông tin người dùng từ cookie
 * @param string $field - Tên trường cần lấy (user_id, username, email, role)
 * @return string|null - Giá trị của trường hoặc null nếu không tồn tại
 */
function getUserCookieValue($field) {
    if ($field === 'id') {
        return $_COOKIE[COOKIE_USER_ID] ?? null;
    } elseif ($field === 'username') {
        return $_COOKIE[COOKIE_USERNAME] ?? null;
    } elseif ($field === 'email') {
        return $_COOKIE[COOKIE_EMAIL] ?? null;
    } elseif ($field === 'role') {
        return $_COOKIE[COOKIE_ROLE] ?? null;
    } elseif ($field === 'login_time') {
        return $_COOKIE[COOKIE_LOGIN_TIME] ?? null;
    }
    return null;
}

/**
 * Hàm xóa tất cả cookies của người dùng (đăng xuất)
 */
function deleteUserCookies() {
    setcookie(COOKIE_USER_ID, '', time() - 3600, COOKIE_PATH);
    setcookie(COOKIE_USERNAME, '', time() - 3600, COOKIE_PATH);
    setcookie(COOKIE_EMAIL, '', time() - 3600, COOKIE_PATH);
    setcookie(COOKIE_ROLE, '', time() - 3600, COOKIE_PATH);
    setcookie(COOKIE_LOGIN_TIME, '', time() - 3600, COOKIE_PATH);
    setcookie(COOKIE_REMEMBER_ME, '', time() - 3600, COOKIE_PATH);
    
    // Xóa khỏi mảng $_COOKIE
    unset($_COOKIE[COOKIE_USER_ID]);
    unset($_COOKIE[COOKIE_USERNAME]);
    unset($_COOKIE[COOKIE_EMAIL]);
    unset($_COOKIE[COOKIE_ROLE]);
    unset($_COOKIE[COOKIE_LOGIN_TIME]);
    unset($_COOKIE[COOKIE_REMEMBER_ME]);
}

/**
 * Hàm tạo cookie bộ nhớ tạm thời
 * @param string $name - Tên cookie
 * @param string $value - Giá trị cookie
 * @param int $expireTime - Thời gian hết hạn (mặc định 30 ngày)
 */
function setCustomCookie($name, $value, $expireTime = null) {
    if ($expireTime === null) {
        $expireTime = COOKIE_EXPIRE_TIME;
    }
    setcookie($name, $value, $expireTime, COOKIE_PATH);
}

/**
 * Hàm lấy giá trị cookie tùy chỉnh
 * @param string $name - Tên cookie
 * @param mixed $defaultValue - Giá trị mặc định nếu cookie không tồn tại
 * @return mixed - Giá trị của cookie hoặc giá trị mặc định
 */
function getCustomCookie($name, $defaultValue = null) {
    return $_COOKIE[$name] ?? $defaultValue;
}

/**
 * Hàm xóa cookie tùy chỉnh
 * @param string $name - Tên cookie cần xóa
 */
function deleteCustomCookie($name) {
    setcookie($name, '', time() - 3600, COOKIE_PATH);
    unset($_COOKIE[$name]);
}

/**
 * Hàm kiểm tra phiên đăng nhập còn hiệu lực hay không
 * @param int $sessionTimeout - Thời gian timeout tính bằng giây (mặc định 1800 giây = 30 phút)
 * @return bool - True nếu phiên còn hiệu lực, False nếu hết hạn
 */
function isSessionActive($sessionTimeout = 1800) {
    if (!isUserLoggedIn()) {
        return false;
    }
    
    $loginTime = getUserCookieValue('login_time');
    if ($loginTime && (time() - $loginTime) > $sessionTimeout) {
        deleteUserCookies();
        return false;
    }
    
    return true;
}

/**
 * Hàm hiển thị thông tin người dùng đã đăng nhập
 * @return array - Mảng chứa thông tin người dùng
 */
function getUserInfo() {
    if (!isUserLoggedIn()) {
        return null;
    }
    
    return [
        'id' => getUserCookieValue('id'),
        'username' => getUserCookieValue('username'),
        'email' => getUserCookieValue('email'),
        'role' => getUserCookieValue('role'),
        'login_time' => getUserCookieValue('login_time')
    ];
}
?>
