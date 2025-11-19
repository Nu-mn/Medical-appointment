<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type");

// Bảo trì
define('MAINTENANCE_MODE', true); // bật/tắt bảo trì
if (MAINTENANCE_MODE) {
    http_response_code(503); // Service Unavailable
    header("Content-Type: text/html; charset=UTF-8");
    include __DIR__ . '/maintenance.php';
    exit;
}

// Import
require_once __DIR__ . "/../../database/db.php";
require_once "PatientService.php";


$conn = connectDB("patient_db");
$service = new PatientService($conn);


// Method & input
$method = $_SERVER["REQUEST_METHOD"];
$input = json_decode(file_get_contents("php://input"), true);

switch ($method) {

    /* === GET LIST hoặc GET 1 === */
    case "GET":
        if (isset($_GET["id"])) {
            echo json_encode($service->getById($_GET["id"]));
        } else if ( (isset($_GET["user_id"]))) {
            echo json_encode($service->getAll($_GET["user_id"]));
        }
        break;

    /* === CREATE === */
    case "POST":
        if ($service->create($input)) {
            echo json_encode(["message" => "Patient created"]);
        } else {
            echo json_encode(["error" => "Create failed"]);
        }
        break;

    /* === UPDATE === */
    case "PUT":
        if (!isset($_GET["id"])) {
            echo json_encode(["error" => "Missing ID"]);
            break;
        }

        if ($service->update($_GET["id"], $input)) {
            echo json_encode(["message" => "Patient updated"]);
        } else {
            echo json_encode(["error" => "Update failed"]);
        }
        break;

    /* === DELETE === */
    case "DELETE":
        if (!isset($_GET["id"])) {
            echo json_encode(["error" => "Missing ID"]);
            break;
        }

        if ($service->delete($_GET["id"])) {
            echo json_encode(["message" => "Patient deleted"]);
        } else {
            echo json_encode(["error" => "Delete failed"]);
        }
        break;

    default:
        echo json_encode(["error" => "Unsupported method"]);
        break;
}
?>
