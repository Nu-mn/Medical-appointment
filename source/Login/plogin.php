<?php
require_once __DIR__ . "/../database/db.php";
require_once __DIR__ . "/../models/user_service/UserService.php";

header("Content-Type: application/json; charset=UTF-8");
session_start();

// Kết nối database
$conn = connectDB("userservice");
$service = new UserService($conn);


$input = json_decode(file_get_contents("php://input"), true);

// Nếu không có JSON, thử lấy từ form POST (phòng trường hợp test Postman)
if (!$input) {
    $input = $_POST;
}


$username = trim($input['username'] ?? '');
$password = trim($input['password'] ?? '');

// Kiểm tra có đủ dữ liệu không
if ($username === '' || $password === '') {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "Thiếu username hoặc password"
    ]);
    exit;
}

// Gọi UserService -> kiểm tra login
$result = $service->login($username, $password);

// Nếu đăng nhập thành công → lưu session
if (isset($result['success']) && $result['success'] === true) {
    $_SESSION["id"] = $result["user"]["id"];
    $_SESSION["username"] = $result["user"]["username"];
    $_SESSION["fullname"] = $result["user"]["fullname"];
    $_SESSION["email"] = $result["user"]["email"];
    $_SESSION["phone"] = $result["user"]["phone"];

    echo json_encode([
        "success" => true,
        "message" => "Đăng nhập thành công",
        "user" => $result["user"]
    ]);
} else {
    // Đăng nhập thất bại
    echo json_encode([
        "success" => false,
        "message" => $result["message"] ?? "Sai username hoặc password"
    ]);
}
?>
