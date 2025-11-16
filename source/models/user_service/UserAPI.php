<?php
require_once "../../database/db.php";
require_once "UserService.php";

// Kết nối database
$conn = connectDB("userservice");

// Khởi tạo service xử lý nghiệp vụ
$service = new UserService($conn);

// Đọc thông tin request
$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

header("Content-Type: application/json");

if (str_ends_with($path, "/users") && $method === "GET") {
    $users = $service->getAllUsers();
    echo json_encode($users);


} elseif (preg_match("#/users/(\d+)$#", $path, $matches) && $method === "GET") {
    $id = intval($matches[1]);
    $user = $service->getUserById($id);
    if ($user) {
        echo json_encode($user);
    } else {
        http_response_code(404);
        echo json_encode(["error" => "User not found"]);
    }

} else {
    http_response_code(404);
    echo json_encode(["error" => "Not found"]);
}
?>
