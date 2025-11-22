<?php  
header("Access-Control-Allow-Origin: http://localhost:3000"); // Hoặc "*" nếu test
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS");
header("Content-Type: application/json");

// Bảo trì
define('MAINTENANCE_MODE', false); // bật/tắt bảo trì
if (MAINTENANCE_MODE) {
    http_response_code(503); // Service Unavailable
    header("Content-Type: text/html; charset=UTF-8");
    include __DIR__ . '/maintenance.php';
    exit;
}

require_once "../../database/db.php";
require_once "InvoiceService.php";

// Connect DB
$conn = connectDB("invoice_db");

// Service
$service = new InvoiceService($conn);

// Giả sử có biến $maintenance_mode kiểm tra trạng thái service
$maintenance_mode = false;

if ($maintenance_mode) {
    http_response_code(503);
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
switch ($method) {

    // --------------------------
    // CREATE INVOICE (POST)
    // --------------------------
    case "POST":
        if (str_ends_with($path, "/invoice/create")) {

            $required = ['booking_id', 'payment_id', 'user_id', 'fee',
                         'specialization_name', 'patient_name', 'status'];

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
                $input['status']
            );

            echo json_encode($result);
            break;
        }

        http_response_code(404);
        echo json_encode(["error" => "Not Found"]);
        break;


    // --------------------------
    // GET INVOICE HISTORY (GET)
    // --------------------------
    case "GET":
        if (str_ends_with($path, "/invoice/history")) {

            if (!isset($_GET['user_id'])) {
                http_response_code(400);
                echo json_encode(["error" => "user_id required"]);
                exit;
            }

            $result = $service->getInvoiceHistory($_GET['user_id']);
            echo json_encode($result);
            break;
        }

       

    if (str_ends_with($path, "/invoice/by_payment")) {

        if (!isset($_GET['payment_id'])) {
            http_response_code(400);
            echo json_encode(["error" => "Thiếu payment_id"]);
            exit;
        }

        $payment_id = intval($_GET['payment_id']);  // ✔ GÁN BIẾN

        $sql = "SELECT * FROM invoices WHERE payment_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $payment_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $invoice = $result->fetch_assoc();
        $stmt->close();

        if (!$invoice) {
            http_response_code(404);
            echo json_encode(["error" => "Invoice không tồn tại"]);
            exit;
        }

    echo json_encode($invoice);
    exit;
}


     http_response_code(404);
        echo json_encode(["error" => "Not Found"]);
        break;
    // --------------------------
    // UPDATE TUITION STATUS (PUT)
    // --------------------------
    case "PUT":
        if (str_ends_with($path, "/invoice/update") && isset($input['invoice_id'], $input['status'])) {

            $ok = $service->updateInvoiceStatus($input['invoice_id'], $input['status']);
            echo json_encode(["success" => $ok]);

        } else {
            http_response_code(400);
            echo json_encode(["error" => "Thiếu invoice_id hoặc status"]);
        }
        break;


  
    default:
        http_response_code(405);
        echo json_encode(["error" => "Phương thức không được hỗ trợ"]);
}
?>
