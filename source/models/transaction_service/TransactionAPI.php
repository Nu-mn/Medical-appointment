<?php
require_once "../../database/db.php";
require_once "TransactionService.php";

// Kết nối DB
$conn = connectDB("transactionservice");
$service = new TransactionService($conn);

// Cấu hình API
$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$input = json_decode(file_get_contents("php://input"), true);

header("Content-Type: application/json");

// 📌 API lấy số dư
if (str_ends_with($path, "/transaction/balance") && $method === "GET") {
    if (!isset($_GET['user_id'])) {
        http_response_code(400);
        echo json_encode(["error" => "Thiếu user_id"]);
        exit;
    }
    $balance = $service->getBalance($_GET['user_id']);
    echo json_encode(["balance" => $balance]);
    exit;
} elseif (str_ends_with($path, "/transaction/history") && $method === "GET") {
    if (!isset($_GET['user_id'])) {
        http_response_code(400);
        echo json_encode(["error" => "Thiếu user_id"]);
        exit;
    }
    $history = $service->getTransactionHistory($_GET['user_id']);
    echo json_encode($history);
    exit;
}elseif (str_ends_with($path, "/transaction/create") && $method === "POST") {
    if (!isset($input['user_id'], $input['tuition_id'], $input['amount'])) {
        http_response_code(400);
        echo json_encode(["error" => "Thiếu tham số user_id, tuition_id hoặc amount"]);
        exit;
    }
    echo json_encode($service->createTransaction($input['user_id'], $input['tuition_id'], $input['amount']));

} elseif (str_ends_with($path, "/transaction/confirm") && $method === "POST") {
    if (!isset($input['transaction_id'], $input['user_id'], $input['email'])) {
        http_response_code(400);
        echo json_encode(["error" => "Thiếu transaction_id, user_id hoặc email"]);
        exit;
    }

    // ✅ Xác nhận giao dịch
    $result = $service->confirmTransaction($input['transaction_id']);

    // Giao dịch thành công
    if (!isset($result['error']) && isset($result['tuition_id'])) {

        $updateData = [
            "tuition_id" => $result['tuition_id'],
            "status" => "paid"
        ];

        // Update trạng thái học phí
        $ch = curl_init("http://localhost/SOA_GK/source/models/student_service/StudentAPI.php");
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($updateData));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        $response = curl_exec($ch);
        curl_close($ch);

        $feeData = json_decode($response, true);
        if (!$feeData['success']) {
            error_log("⚠️ Không thể cập nhật trạng thái học phí cho tuition_id=" . $result['tuition_id']);
        }

        // ✅ Lấy dữ liệu học phí để gửi mail
        $tuitionRes = file_get_contents("http://localhost/SOA_GK/source/models/student_service/StudentAPI.php?fee=" . $result['tuition_id']);
        $feeData = json_decode($tuitionRes, true);

        if ($feeData && count($feeData) > 0) {
            $hocphi = $feeData[0]['amount'];
            $ten = $feeData[0]['fullname'];
            $khoa = $feeData[0]['department'];

            $subject = "Thanh toán học phí thành công";
        $body = "Xin chào,<br><br>" .
        "Bạn đã thanh toán thành công học phí cho học kỳ hiện tại của sinh viên: {$ten}.<br>" .
        "Số tiền: " . number_format($hocphi) . " VND<br>" .
        "Khoa: {$khoa}<br>" .
        "Mã học phí: {$result['tuition_id']}<br>" .
        "Mã giao dịch: {$input['transaction_id']}<br><br>" .
        "Cảm ơn bạn đã sử dụng dịch vụ.";



            // Gửi mail qua Notification Service
            $notifyUrl = "http://localhost/SOA_GK/source/models/notification_service/NotifyAPI.php/notify/email";
            $payload = [
                "user_id" => $input['user_id'],
                "to" => $input['email'],
                "subject" => $subject,
                "body" => $body,
                "type" => "payment_success",
                "metadata" => ["transaction_id" => $input['transaction_id']]
            ];

            $options = [
                "http" => [
                    "header"  => "Content-Type: application/json\r\n",
                    "method"  => "POST",
                    "content" => json_encode($payload)
                ]
            ];
            $mailResult = file_get_contents($notifyUrl, false, stream_context_create($options));
            $result['mail_response'] = json_decode($mailResult, true);
        }
    }

    echo json_encode($result);
} elseif (str_ends_with($path, "/transaction/fail") && $method === "POST") {
    if (!isset($input['transaction_id'])) {
        http_response_code(400);
        echo json_encode(["error" => "Thiếu transaction_id"]);
        exit;
    }
    echo json_encode($service->failTransaction($input['transaction_id']));

} else {
    http_response_code(404);
    echo json_encode(["error" => "API không tồn tại"]);
}
?>

