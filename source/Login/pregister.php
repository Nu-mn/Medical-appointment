<?php
require_once __DIR__ . "/../database/db.php";
require_once __DIR__ . "/../models/user_service/UserService.php";

header("Content-Type: application/json; charset=UTF-8");
session_start();

// Kết nối database
$conn = connectDB("user_db");
$service = new UserService($conn);

// Lấy dữ liệu JSON
$data = json_decode(file_get_contents("php://input"), true);
file_put_contents("debug.txt", "JSON received: " . print_r($data, true) . "\n", FILE_APPEND);

$username = $data["username"] ?? "";
$phone    = $data["phone"] ?? "";
$email    = $data["email"] ?? "";
$password = $data["password"] ?? "";

// Kiểm tra dữ liệu đầu vào
if (!$username || !$phone || !$email || !$password) {
    echo json_encode([
        "success" => false,
        "message" => "Thiếu dữ liệu gửi lên!"
    ]);
    exit;
}

try {
    // Kiểm tra số điện thoại tồn tại
    if ($service->checkPhoneExist($phone)) {
        echo json_encode([
            "success" => false,
            "message" => "Số điện thoại đã được sử dụng!"
        ]);
        exit;
    }

    // Kiểm tra email tồn tại (nếu cần)
    if ($service->checkEmailExist($email)) {
        echo json_encode([
            "success" => false,
            "message" => "Email đã được sử dụng!"
        ]);
        exit;
    }

    // Tạo tài khoản mới (hash password)
;
    $result = $service->registerUser($username, $phone, $email, $password);

    if ($result) {
        echo json_encode([
            "success" => true,
            "message" => "Đăng ký thành công!"
        ]);
    } else {
        echo json_encode([
            "success" => false,
            "message" => "Lỗi hệ thống! Không đăng ký được."
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        "success" => false,
        "message" => "Lỗi hệ thống: " . $e->getMessage()
    ]);
}
?>
