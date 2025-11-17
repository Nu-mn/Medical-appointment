<?php
header("Content-Type: application/json");

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
        if (str_ends_with($path, "/doctor/by-specialization") && isset($input['specialization_id'])) {
            echo json_encode($service->getDoctorsBySpecialization($input['specialization_id']));
            break;
        }

        // LẤY LỊCH BÁC SĨ
        // Endpoint: /doctor/schedule
        if (str_ends_with($path, "/doctor/schedule") && isset($input['doctor_id'])) {
            echo json_encode($service->getSchedule($input['doctor_id']));
            break;
        }

        // KIỂM TRA SLOT CÒN HAY HẾT
        // Endpoint: /doctor/check
        if (str_ends_with($path, "/doctor/check") && isset($input['doctor_id'], $input['date'], $input['session'])) {
            $available = $service->checkSlot($input['doctor_id'], $input['date'], $input['session']);
            echo json_encode($available);
            break;
        }

        // LẤY TẤT CẢ CHUYÊN KHOA
        // Endpoint: /doctor/allspecializations
        if (str_ends_with($path, "/doctor/allspecializations")) {
            echo json_encode($service->getSpecializations());
            break;
        }

        echo json_encode(["error" => "Không có route phù hợp"]);
        break;

    case 'POST':

        // ĐẶT LỊCH – giảm slot
        // Endpoint: /doctor/book
        if (str_ends_with($path, "/doctor/book") && isset($input['doctor_id'], $input['date'], $input['session'])) {
            $result = $service->bookSlot($input['doctor_id'], $input['date'], $input['session']);
            echo json_encode($result);
            break;
        }

        http_response_code(400);
        echo json_encode(["error" => "Thiếu dữ liệu"]);
        break;


    default:
        http_response_code(405);
        echo json_encode(["error" => "Phương thức không hỗ trợ"]);
        break;
}
?>
