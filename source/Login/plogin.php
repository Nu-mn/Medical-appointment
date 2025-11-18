<?php
require_once __DIR__ . "/../database/db.php";
require_once __DIR__ . "/../models/user_service/UserService.php";

header("Content-Type: application/json; charset=UTF-8");
session_start();

// Kết nối DB
$conn = connectDB("user_db");
$service = new UserService($conn);

// Lấy dữ liệu JSON từ fetch()
$input = json_decode(file_get_contents("php://input"), true);
file_put_contents("debug.txt", print_r($input, true));


// fallback nếu test bằng form POST
if (!$input) {
    $input = $_POST;
}

$phone = trim($input['phone'] ?? '');
$password = trim($input['password'] ?? '');

// Kiểm tra dữ liệu
if ($phone === '' || $password === '') {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "Thiếu số điện thoại hoặc mật khẩu"
    ]);
    exit;
}

// Gọi service login
$result = $service->login($phone, $password);

if ($result['success']) {

    $_SESSION["user_id"] = $result["user"]["user_id"];
    $_SESSION["phone"] = $result["user"]["phone"];


    echo json_encode([
        "success" => true,
        "message" => "Đăng nhập thành công",
        "user" => $result["user"]
    ]);
} else {
    echo json_encode([
        "success" => false,
        "message" => $result["message"] ?? "Sai số điện thoại hoặc mật khẩu"
    ]);
}
?>
