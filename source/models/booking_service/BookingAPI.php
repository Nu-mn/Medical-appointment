<?php
header("Access-Control-Allow-Origin: http://localhost:3000"); // Hoặc "*" nếu test
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}
// Import
require_once __DIR__ . "/../../database/db.php";
include_once 'BookingService.php';


$conn = connectDB("booking_db");
$bookingService = new BookingService($conn);

// Lấy method
$method = $_SERVER['REQUEST_METHOD'];

// Lấy input JSON
$input = json_decode(file_get_contents("php://input"), true);

try {
    switch ($method) {
        case 'POST':  // tạo mới
            $input = json_decode(file_get_contents('php://input'), true);

            if (!$input) {
                // Thử lấy từ form POST nếu không phải JSON
                $input = $_POST;
            }

            // Validate cơ bản (có thể thêm tùy ý)
            $required = ['patient_id', 'doctor_id', 'specialization_id', 'booking_date', 'amount', 'slot_time'];
            foreach ($required as $field) {
                if (!isset($input[$field]) || $input[$field] === '') {
                    http_response_code(400);
                    echo json_encode([
                        'status'  => 'error',
                        'message' => "Missing field: $field"
                    ]);
                    exit;
                }
            }

            $booking_id = $bookingService->create($input);

            echo json_encode([
                'status'     => 'success',
                'booking_id' => $booking_id
            ]);
            break;

        case 'GET':   // lấy theo booking_id
            if (!isset($_GET['booking_id'])) {
                http_response_code(400);
                echo json_encode(['status' => 'error', 'message' => 'booking_id is required']);
                exit;
            }

            $booking_id = (int)$_GET['booking_id'];
            $appointment = $bookingService->getById($booking_id);

            if (!$appointment) {
                http_response_code(404);
                echo json_encode(['status' => 'error', 'message' => 'Appointment not found']);
                exit;
            }

            echo json_encode([
                'status' => 'success',
                'data'   => $appointment
            ]);
            break;

        case 'PUT':   // update status
            $input = json_decode(file_get_contents('php://input'), true);

            if (!isset($input['booking_id'], $input['status'])) {
                http_response_code(400);
                echo json_encode([
                    'status'  => 'error',
                    'message' => 'booking_id and status are required'
                ]);
                exit;
            }

            $booking_id = (int)$input['booking_id'];
            $status     = $input['status'];

            // Option: kiểm tra status có hợp lệ không
            $allowedStatus = ['pending','confirmed','completed','cancelled'];
            if (!in_array($status, $allowedStatus, true)) {
                http_response_code(400);
                echo json_encode([
                    'status'  => 'error',
                    'message' => 'Invalid status'
                ]);
                exit;
            }

            $ok = $bookingService->updateStatus($booking_id, $status);

            if (!$ok) {
                http_response_code(404);
                echo json_encode(['status' => 'error', 'message' => 'Appointment not found or status unchanged']);
                exit;
            }

            echo json_encode([
                'status'  => 'success',
                'message' => 'Status updated'
            ]);
            break;

        default:
            http_response_code(405);
            echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status'  => 'error',
        'message' => $e->getMessage()
    ]);
}
?>
