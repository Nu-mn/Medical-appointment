<?php 
header("Content-Type: application/json");

require_once "../../database/db.php";
require_once "InvoiceService.php";

// Connect DB
$conn = connectDB("invoice_db");

// Service
$service = new InvoiceService($conn);

// Request
$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$input = json_decode(file_get_contents("php://input"), true);

// Routing
if ($method === "POST" && str_ends_with($path, "/invoice/create")) {
     if (!isset($input['booking_id'], $input['payment_id'], $input['user_id'], $input['total_amount'], $input['details'])) {
        http_response_code(400);
        echo json_encode(["error" => "booking_id, payment_id, user_id, total_amount, details required"]);
        exit;
    }
    $result = $service->createInvoice(
        $input['booking_id'],
        $input['payment_id'],
        $input['user_id'],
        $input['total_amount'],
        $input['details']
    );
    echo json_encode($result);

} elseif ($method === "GET" && str_ends_with($path, "/invoice/history")) {
    if (!isset($input['user_id'])) {
        http_response_code(400);
        echo json_encode(["error" => "user_id required"]);
        exit;
    }
    $result = $service->getInvoiceHistory($input['user_id']);
    echo json_encode($result);

} else {
    http_response_code(404);
    echo json_encode(["error" => "Not Found"]);
}