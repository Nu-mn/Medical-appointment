<?php
session_start();

define('BASE_PATH', __DIR__);

define('BASE_URL', '/Medical-appointment/source');

if (isset($_SESSION["user_id"])) {
    // Nếu đã đăng nhập → chuyển đến trang chính
    header("Location: " . BASE_URL . "/views/index.php");
    exit();
} else {
    // Nếu chưa đăng nhập → chuyển đến trang login
    header("Location: " . BASE_URL . "/Login/login.php");
    exit();
}

