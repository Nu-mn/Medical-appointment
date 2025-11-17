<?php
session_start();

define('BASE_PATH', __DIR__);
define('BASE_URL', '/source');


// Nếu đã đăng nhập → chuyển đến trang chính
header("Location: " . BASE_URL . "/views/index.php");
exit();

