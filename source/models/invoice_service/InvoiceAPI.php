<?php  
header("Access-Control-Allow-Origin: http://localhost:3000"); // Hoặc "*" nếu test
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS");
header("Content-Type: application/json");

// Bảo trì
define('MAINTENANCE_MODE', false); // bật/tắt bảo trì
if (MAINTENANCE_MODE) {
    http_response_code(503); // Service Unavailable
    echo json_encode([
        'status' => 'error',
        'message' => 'API đang bảo trì. Vui lòng thử lại sau.'
    ]);
    exit;
}

require_once "../../database/db.php";
require_once "InvoiceService.php";

// Connect DB
$conn = connectDB("invoice_db");

// Service
$service = new InvoiceService($conn);

// Giả sử có biến $maintenance_mode kiểm tra trạng thái service
$maintenance_mode = false; // true nếu đang bảo trì

if ($maintenance_mode) {
    http_response_code(503); // 503 Service Unavailable
    echo json_encode([
        "status" => "maintenance",
        "message" => "Service này đang bảo trì, vui lòng thử lại sau."
    ]);
    exit;
}

// Request
$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$input = json_decode(file_get_contents("php://input"), true);

// Routing
if ($method === "POST" && str_ends_with($path, "/invoice/create")) {

    $required = ['booking_id', 'payment_id', 'user_id', 'fee', 'specialization_name', 'patient_name', 'num_order', 'status'];
    foreach ($required as $key) {
        if (!isset($input[$key])) {
            http_response_code(400);
            echo json_encode(["error" => "$key required"]);
            exit;
        }
    }


    $result = $service->createInvoice(
        $input['booking_id'],
        $input['payment_id'],
        $input['user_id'],
        $input['fee'],
        $input['specialization_name'],
        $input['patient_name'],
        $input['num_order'],
        $input['status']
    );

    echo json_encode($result);

} elseif ($method === "GET" && str_ends_with($path, "/invoice/history")) {

    if (!isset($_GET['user_id'])) {
        http_response_code(400);
        echo json_encode(["error" => "user_id required"]);
        exit;
    }

    $result = $service->getInvoiceHistory($_GET['user_id']);
    echo json_encode($result);

} else {
    http_response_code(404);
    echo json_encode(["error" => "Not Found"]);
}
?>
