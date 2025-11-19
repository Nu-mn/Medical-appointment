<?php
    header("Content-Type: application/json");

    // Bảo trì
    define('MAINTENANCE_MODE', true); // bật/tắt bảo trì
    if (MAINTENANCE_MODE) {
        http_response_code(503); // Service Unavailable
        header("Content-Type: text/html; charset=UTF-8");
        include __DIR__ . '/maintenance.php';
        exit;
    }

    require_once "../../database/db.php";
    require_once "NotificationService.php";

    // Connect DB
    $conn = connectDB("notification_db");

    // Service
    $service = new NotificationService($conn);

    // Request
    $method = $_SERVER['REQUEST_METHOD'];
    $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $input = json_decode(file_get_contents("php://input"), true);

    if ($method === "POST" && str_ends_with($path, "/notify/email")) {
        if (!isset($input['user_id']) || !isset($input['to']) || !isset($input['subject']) || !isset($input['body'])) {
            http_response_code(400);
            echo json_encode(["error" => "user_id, to, subject, body required"]);
            exit;
        }

        $result = $service->sendEmail(
            $input['user_id'],
            $input['to'],
            $input['subject'],
            $input['body']
        );

        echo json_encode($result);
    } else {
        http_response_code(404);
        echo json_encode(["error" => "Not Found"]);
    }
