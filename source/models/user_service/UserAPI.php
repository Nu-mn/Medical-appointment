<?php
require_once "../../database/db.php";
require_once "UserService.php";

// Kết nối database
$conn = connectDB("user_db");

// Khởi tạo service xử lý nghiệp vụ
$service = new UserService($conn);

// Đọc thông tin request
$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

header("Content-Type: application/json");

if (str_ends_with($path, "/users") && $method === "GET") {

    $profiles = $service->getAllUser();
    echo json_encode($profiles);
    exit;

} elseif (preg_match("#/users/(\d+)$#", $path, $matches) && $method === "GET") {

    $user_id = intval($matches[1]);
    $profile = $service->getUserByUserId($user_id);

    if ($profile) {
        echo json_encode($profile);
    } else {
        http_response_code(404);
        echo json_encode(["error" => "Profile not found"]);
    }
    exit;

} else {
    http_response_code(404);
    echo json_encode(["error" => "Not found"]);
    exit;
}
?>
