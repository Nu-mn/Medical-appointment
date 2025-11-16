<?php
header("Content-Type: application/json");
require_once __DIR__ . "/../../database/db.php";


require_once __DIR__ . "/StudentService.php";


$conn = connectDB("studentservice");
$service = new StudentService($conn);

$method = $_SERVER['REQUEST_METHOD'];

// Đọc dữ liệu gửi lên (nếu có)
$input = json_decode(file_get_contents("php://input"), true);

switch ($method) {
    case 'GET':
    if (isset($_GET['mssv'])) {
        $data = $service->getStudentByMSSV($_GET['mssv']);
        echo json_encode($data ?: ["error" => "Không tìm thấy sinh viên"]);
    } elseif (isset($_GET['fee'])) {
        $data = $service->getStudentFee($_GET['fee']);
        echo json_encode($data ?: ["error" => "Không tìm thấy học phí"]);
    } else {
        echo json_encode($service->getAllStudents());
    }
    break;


    case 'PUT':
        if (isset($input['tuition_id'], $input['status'])) {
            $ok = $service->updateFeeStatus($input['tuition_id'], $input['status']);
            echo json_encode(["success" => $ok]);
        } else {
            http_response_code(400);
            echo json_encode(["error" => "Thiếu tuition_id hoặc status"]);
        }
        break;
    default:
        http_response_code(405);
        echo json_encode(["error" => "Phương thức không được hỗ trợ"]);
}
?>
