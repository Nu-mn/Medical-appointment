<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS");
header("Content-Type: application/json");
// OPTIONS request cho preflight
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

require_once __DIR__."/DoctorService.php";
require_once __DIR__."/../../database/db.php"; // file trả về mysqli connection function connectDB()

$conn = connectDB("doctor_db");
$service = new DoctorService($conn);

$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$input = json_decode(file_get_contents("php://input"), true);

switch ($method) {

    case 'GET':

        // LẤY BÁC SĨ THEO CHUYÊN KHOA
        // Endpoint: /doctor/by-specialization
        if (str_ends_with($path, "/doctor/by-specialization") && isset($_GET['specialization_id'])) {
            echo json_encode($service->getDoctorsBySpecialization($_GET['specialization_id']));
            break;
        }

        // LẤY LỊCH BÁC SĨ
        // Endpoint: /doctor/schedule
        if (str_ends_with($path, "/doctor/schedule") && isset($_GET['doctor_id'])) {
            echo json_encode($service->getSchedule($_GET['doctor_id']));
            break;
        }


        // LẤY TẤT CẢ CHUYÊN KHOA
        // Endpoint: /specializations
        if (str_ends_with($path, "/specializations")) {
            echo json_encode($service->getSpecializations());
            break;
        }

        // LẤY TÊN CHUYÊN KHOA THEO ID
// Endpoint: /specialization/name
        if (str_ends_with($path, "/specialization/name") && isset($_GET['specialization_id'])) {
            echo json_encode([
                "specialization_id" => $_GET['specialization_id'],
                "name" => $service->getSpecializationNameById($_GET['specialization_id'])
            ]);
            break;
        }



        echo json_encode(["error" => "Không có route phù hợp"]);
        break;

    case 'POST':

        // API: POST /doctor/book  (tăng/giảm slot)
        if (str_ends_with($path, "/doctor/book")) {

            // Kiểm tra đủ dữ liệu
            if (!isset($input['doctor_id'], $input['date'], $input['session'], $input['change'])) {
                http_response_code(400);
                echo json_encode(["success" => false, "message" => "Thiếu dữ liệu"]);
                break;
            }

            $doctor_id = $input['doctor_id'];
            $date      = $input['date'];
            $session   = $input['session'];
            $change    = $input['change']; // +1 hoặc -1

            // Gọi service update slot
            $result = $service->updateSlot($doctor_id, $date, $session, $change);

            echo json_encode($result);
            break;
        }

        http_response_code(400);
        echo json_encode(["error" => "Route không hợp lệ"]);
        break;



    default:
        http_response_code(405);
        echo json_encode(["error" => "Phương thức không hỗ trợ"]);
        break;
}
?>