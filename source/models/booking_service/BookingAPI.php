<?php
header("Access-Control-Allow-Origin: http://localhost:3000"); // Hoặc "*" nếu test
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS");
header("Content-Type: application/json");


if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Bảo trì
define('MAINTENANCE_MODE', false); // bật/tắt bảo trì
if (MAINTENANCE_MODE) {
    http_response_code(503); // Service Unavailable
    header("Content-Type: text/html; charset=UTF-8");
    include __DIR__ . '/maintenance.php';
    exit;
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
        case 'POST':  // Tạo mới booking theo microservice
            if (!$input) {
                $input = $_POST;  // fallback nếu gọi từ form
            }

            $required = [
                'user_id',
                'patient_id',
                'doctor_id',
                'specialization_id',
                'booking_date',
                'amount',
                'slot_time'
            ];

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

            // =============================
            // 1️⃣ Gọi DoctorService để giảm slot (-1)
            // =============================
            $slotPayload = [
                'doctor_id' => $input['doctor_id'],
                'date'      => $input['booking_date'],
                'session'   => $input['slot_time'],
                'change'    => -1
            ];

            $slotCh = curl_init("http://localhost/Medical-appointment/source/models/doctor_service/DoctorAPI.php/doctor/book");
            curl_setopt($slotCh, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($slotCh, CURLOPT_POSTFIELDS, json_encode($slotPayload));
            curl_setopt($slotCh, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($slotCh, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            $slotRes = curl_exec($slotCh);
            curl_close($slotCh);

            $slotResJson = json_decode($slotRes, true);

            if (!$slotResJson['success'] ?? false) {
                http_response_code(400);
                echo json_encode([
                    'status'  => 'error',
                    'message' => 'Không thể đặt lịch: ' . ($slotResJson['message'] ?? 'Slot API lỗi')
                ]);
                exit;
            }
            

            // =============================
            // 2️⃣ Tạo booking (BookingService)
            // =============================
            $booking_id = $bookingService->create($input);

            if (!$booking_id) {
                // =============================
                // rollback slot nếu tạo booking thất bại
                // =============================
                $rollbackPayload = [
                    'doctor_id' => $input['doctor_id'],
                    'date'      => $input['booking_date'],
                    'session'   => $input['slot_time'],
                    'change'    => +1
                ];

                $rollbackCh = curl_init("http://localhost/Medical-appointment/source/models/doctor_service/DoctorAPI.php/doctor/book");
                curl_setopt($rollbackCh, CURLOPT_CUSTOMREQUEST, "POST");
                curl_setopt($rollbackCh, CURLOPT_POSTFIELDS, json_encode($rollbackPayload));
                curl_setopt($rollbackCh, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($rollbackCh, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
                curl_exec($rollbackCh);
                curl_close($rollbackCh);

                http_response_code(500);
                echo json_encode([
                    'status'  => 'error',
                    'message' => 'Failed to create booking'
                ]);
                exit;
            }

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
