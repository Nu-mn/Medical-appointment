<?php
    require_once "../../database/db.php";
    require_once "OtpService.php";

    // Connect DB
    $conn = connectDB("otpservice");

    // Service
    $service = new OtpService($conn);

    // Request
    $method = $_SERVER['REQUEST_METHOD'];
    $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $input = json_decode(file_get_contents("php://input"), true);

    header('Content-Type: application/json');

    if (str_ends_with($path, "/otp/generate") && $method === "POST") {
        if (!isset($input['transaction_id'], $input['user_id'], $input['email'])) {
            http_response_code(400);
            echo json_encode(["error" => "transaction_id, user_id, email required"]);
            exit;
        }
        $otpData = $service->generateOtp($input['transaction_id']);
        if (!$otpData) {
            echo json_encode(["error" => "Không tạo được OTP"]);
            exit;
        }

        // send mail to Notification Service
        $notifyUrl = "http://localhost/SOA_GK/source/models/notification_service/NotifyAPI.php/notify/email";
        $subject = "Mã OTP cho giao dịch #" . $input['transaction_id'];
        $body = "Mã OTP của bạn là: " . $otpData['otp_code'] . 
                "\nHiệu lực đến: " . $otpData['expired_at'];

        $payload = [
            "user_id" => $input['user_id'],
            "to" => $input['email'],
            "subject" => $subject,
            "body" => $body,
            "type" => "otp",
            "metadata" => [
                "transaction_id" => $input['transaction_id'],
                "otp_id" => $otpData['otp_id']
            ]
        ];

        $options = [
            "http" => [
                "header"  => "Content-Type: application/json\r\n",
                "method"  => "POST",
                "content" => json_encode($payload)
            ]
        ];
        $context = stream_context_create($options);
        $result = file_get_contents($notifyUrl, false, $context);

        echo json_encode([
            "otp_id" => $otpData['otp_id'],
            "expired_at" => $otpData['expired_at'],
            "notification_response" => json_decode($result, true)
        ]);

    } elseif (str_ends_with($path, "/otp/validate") && $method === "POST") {
        if (!isset($input['transaction_id'], $input['otp'])) {
            http_response_code(400);
            echo json_encode(["error" => "transaction_id and otp required"]);
            exit;
        }
        $result = $service->validateOtp($input['transaction_id'], $input['otp']);
        echo json_encode($result);

    } else {
        http_response_code(404);
        echo json_encode(["error" => "Not found"]);
    }
?>