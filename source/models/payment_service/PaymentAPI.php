<?php
require_once "../../database/db.php";
require_once "PaymentService.php";


header("Content-Type: application/json");

// Kết nối DB
$conn = connectDB("payment_db");
$paymentService = new PaymentService($conn);

session_start();

// Lấy dữ liệu JSON từ request
$input = json_decode(file_get_contents("php://input"), true);
$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// ===================== TẠO GIAO DỊCH =====================
if (str_ends_with($path, "/payments/create") && $method === "POST") {

    if (!isset($input['booking_id']) || !isset($input['amount'])) {
        http_response_code(400);
        echo json_encode(["error" => "Thiếu booking_id hoặc amount"]);
        exit;
    }

    $booking_id = $input['booking_id'];
    $amount = $input['amount'];

    // 1️⃣ Tạo payment mặc định unpaid
    $payment = $paymentService->createPayments($booking_id, $amount);
    if (!isset($payment['payment_id'])) {
        http_response_code(500);
        echo json_encode(["error" => "Tạo payment thất bại"]);
        exit;
    }
    $payment_id = $payment['payment_id'];
   
    // 2️⃣ Lấy thông tin booking
    $bookingRes = file_get_contents("http://localhost/Medical-appointment/source/models/booking_service/BookingAPI.php?booking_id=" . $booking_id);
    $bookingData = json_decode($bookingRes, true);
    $booking = $bookingData['data'] ?? null;
    if (!$booking) {
        http_response_code(404);
        echo json_encode(["error" => "Booking không tồn tại"]);
        exit;
    }

    // 3️⃣ Lấy thông tin patient
    $patientRes = file_get_contents("http://localhost/Medical-appointment/source/models/patient_service/PatientAPI.php?id=" . $booking['patient_id']);
    $patientData = json_decode($patientRes, true);

    // 4️⃣ Lấy thông tin doctor
    $doctorRes = file_get_contents("http://localhost/Medical-appointment/source/models/doctor_service/DoctorAPI.php/specialization/name?specialization_id=" . $booking['specialization_id']);
    $doctorData = json_decode($doctorRes, true);

    // 5️⃣ Tạo invoice
    $invoiceData = [
        'booking_id' => $booking_id,
        'payment_id' => $payment_id,
        'user_id' => $booking['user_id'],
        'fee' => $amount,
        'specialization_name' => $doctorData['name'] ?? '',
        'patient_name' => $patientData['full_name'] ?? '',
        'status' => 'Đang xử lý'
    ];

    $ch = curl_init("http://localhost/Medical-appointment/source/models/invoice_service/InvoiceAPI.php/invoice/create");
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($invoiceData));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    $invoiceRes = curl_exec($ch);
    curl_close($ch);
    $invoiceRes = json_decode($invoiceRes, true);

   


    // 9️⃣ Trả kết quả JSON
    echo json_encode([
        'payment' => $payment,
        'invoice' => $invoiceRes,
        'message' => 'Payment, invoice, email và slot đã xử lý xong'
    ]);
    exit;
}


// ===================== CẬP NHẬT KẾT QUẢ THANH TOÁN =====================

if (str_ends_with($path, "/payments/result") && $method === "POST") {

    if (!isset($input['result_code']) || !isset($input['payment_id']) || !isset($input['booking_id'])) {
        http_response_code(400);
        echo json_encode(["error" => "Thiếu payment_id hoặc result_code"]);
        exit;
    }

    $result_code = $input['result_code'];
    $payment_id  = $input['payment_id'];
    $booking_id  = $input['booking_id'];

    // 1️⃣ Lưu result_code vào database
    $sql = "UPDATE payments SET result_code = ? WHERE payment_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $result_code, $payment_id);
    $stmt->execute();
    $stmt->close();


    // Lấy thông tin booking
    $bookingRes = file_get_contents("http://localhost/Medical-appointment/source/models/booking_service/BookingAPI.php?booking_id=" . $booking_id);
    $bookingData = json_decode($bookingRes, true);
    $booking = $bookingData['data'] ?? null;
    if (!$booking) {
        http_response_code(404);
        echo json_encode(["error" => "Booking không tồn tại"]);
        exit;
    }

    // Lấy invoice_id dựa trên payment_id, trước khi kiểm tra result_code
    $invoiceRes = file_get_contents("http://localhost/Medical-appointment/source/models/invoice_service/InvoiceAPI.php/invoice/by_payment?payment_id=" . $payment_id);
    $invoiceData = json_decode($invoiceRes, true);
    $invoice_id = $invoiceData['invoice_id'] ?? null;

    if (!$invoice_id) {
        echo json_encode(["error" => "Không tìm thấy invoice từ payment_id"]);
        exit;
    }

    // 2️⃣ Nếu thanh toán thành công → update invoice, gửi mail, giảm slot
    if ($result_code === "0") {
        // Xác nhận payment
        $paymentStatus = $paymentService->confirmPayments($payment_id, null);

        if (!isset($paymentStatus['success']) || $paymentStatus['success'] !== true) {
            echo json_encode(["error" => "Thanh toán không thành công, trạng thái unpaid"]);
            exit;
        }
            

            // 4️⃣ Gọi PUT để update invoice
            $updateData = [
                "invoice_id" => $invoice_id,
                "status" => "Thành công"
            ];

        $ch = curl_init("http://localhost/Medical-appointment/source/models/invoice_service/InvoiceAPI.php/invoice/update");

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json'
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($updateData));

        $updateRes = curl_exec($ch);
        $curlErr = curl_error($ch);
        curl_close($ch);
        // Lấy lỗi curl nếu có
            

        $updateResData = json_decode($updateRes, true);

        $invoiceRes = file_get_contents(
                "http://localhost/Medical-appointment/source/models/invoice_service/InvoiceAPI.php/invoice/by_payment?payment_id=" . $payment_id
            );
            $invoiceData = json_decode($invoiceRes, true);

        file_put_contents(__DIR__ . '/debug_payment_invoice.txt', 
                date('Y-m-d H:i:s') 
                . " - UPDATE DATA: " . json_encode($updateData) 
                . "\nResponse: $updateRes\nCurl Error: $curlErr\n", 
                FILE_APPEND
            );   


        // 3️⃣ Lấy thông tin patient
        $patientRes = file_get_contents("http://localhost/Medical-appointment/source/models/patient_service/PatientAPI.php?id=" . $booking['patient_id']);
        $patientData = json_decode($patientRes, true);

        // 4️⃣ Lấy thông tin doctor
        $doctorRes = file_get_contents("http://localhost/Medical-appointment/source/models/doctor_service/DoctorAPI.php/specialization/name?specialization_id=" . $booking['specialization_id']);
        $doctorData = json_decode($doctorRes, true);


    

        // 7️⃣ Gửi email thông báo
        $notifyUrl = "http://localhost/Medical-appointment/source/models/notification_service/NotifyAPI.php/notify/email";
        $payload = [
            "user_id" => $booking['user_id'],
            "to" => $patientData["email"] ?? "",
            "subject" => ($paymentStatus['success'] ?? false) ? "Thanh toán lịch hẹn thành công" : "Thanh toán thất bại",
            "body" => '
    <div style="font-family: Arial, sans-serif; background:#f7f7f7; padding:20px;">
        <div style="
            max-width:600px; 
            margin:auto; 
            background:white; 
            padding:20px; 
            border-radius:10px; 
            box-shadow:0 0 10px rgba(0,0,0,0.1);
        ">

            <h2 style="text-align:center; color:#2a9d8f; margin-bottom:10px;">
                🎉 Thanh toán lịch hẹn thành công
            </h2>

            <p style="font-size:16px; color:#333;">
                Xin chào <strong>' . ($patientData['full_name'] ?? '') . '</strong>,
            </p>

            <p style="font-size:15px; color:#444; line-height:1.6;">
                Cảm ơn bạn đã sử dụng dịch vụ của chúng tôi. Dưới đây là thông tin chi tiết cuộc hẹn:
            </p>

            <div style="
                background:#f1f1f1; 
                padding:15px; 
                border-radius:8px; 
                margin: 20px 0;
                font-size:14px;
                color:#333;
            ">
                <p><strong>Ngày sinh:</strong> ' . ($patientData['date_of_birth'] ?? '') . '</p>
                <p><strong>Giới tính:</strong> ' . ($patientData['gender'] ?? '') . '</p>
                <p><strong>Số điện thoại:</strong> ' . ($patientData['phone'] ?? '') . '</p>
                <p><strong>CCCD/CMND:</strong> ' . ($patientData['citizen_id'] ?? '') . '</p>
                <p><strong>Địa chỉ:</strong> ' . ($patientData['address'] ?? '') . '</p>
            </div>

            <h3 style="color:#e76f51; margin-bottom:10px;">Thông tin đặt khám</h3>

            <table style="width:100%; border-collapse:collapse; font-size:15px; margin-bottom:20px;">
                <tr>
                    <td style="padding:8px; border-bottom:1px solid #ddd;"><strong>Booking ID:</strong></td>
                    <td style="padding:8px; border-bottom:1px solid #ddd;">' . $booking_id . '</td>
                </tr>
                <tr>
                    <td style="padding:8px; border-bottom:1px solid #ddd;"><strong>Chuyên khoa:</strong></td>
                    <td style="padding:8px; border-bottom:1px solid #ddd;">' . ($doctorData['name'] ?? '') . '</td>
                </tr>
                <tr>
                    <td style="padding:8px; border-bottom:1px solid #ddd;"><strong>Số tiền:</strong></td>
                    <td style="padding:8px; border-bottom:1px solid #ddd;">' . number_format($booking['amount']) . ' VND</td>
                </tr>
                <tr>
                    <td style="padding:8px; border-bottom:1px solid #ddd;"><strong>Ngày khám:</strong></td>
                    <td style="padding:8px; border-bottom:1px solid #ddd;">' . $booking['booking_date'] . '</td>
                </tr><tr>
                    <td style="padding:8px; border-bottom:1px solid #ddd;"><strong>Giờ khám:</strong></td>
                    <td style="padding:8px; border-bottom:1px solid #ddd;">' . $booking['slot_time'] . '</td>
                </tr>

            </table>

            <p style="font-size:15px; color:#555; text-align:center;">
                Nếu bạn có bất kỳ thắc mắc nào, hãy liên hệ với chúng tôi qua email hoặc số hotline.
            </p>


            <p style="font-size:14px; color:#aaa; text-align:center; margin-top:20px;">
                &copy; 2025 Medical Appointment System
            </p>
        </div>
    </div>
    '
            ,
            "type" => ($paymentStatus['success'] ?? false) ? "payment_success" : "payment_failed",
            "metadata" => ["payment_id" => $payment_id]
        ];
        file_get_contents($notifyUrl, false, stream_context_create([
            "http" => [
                "header" => "Content-Type: application/json\r\n",
                "method" => "POST",
                "content" => json_encode($payload)
            ]
        ]));

      

        // 9️⃣ Trả kết quả JSON
        echo json_encode([
            'payment' => $payment,
            'invoice' => $invoiceRes,
            'invoice_update'=> $updateResData,
            'payment_status' => $paymentStatus,
            // 'slot_status' => $slotStatus,
            'message' => 'Payment, invoice, email và slot đã xử lý xong'
        ]);
        exit;

    } else {
        // 3️⃣ Thanh toán thất bại → chỉ cập nhật invoice = 'Thất bại'
        $updateData = [
                "invoice_id" => $invoice_id,
                "status" => "Thất bại"
            ];

        $ch = curl_init("http://localhost/Medical-appointment/source/models/invoice_service/InvoiceAPI.php/invoice/update");

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json'
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($updateData));

        $updateRes = curl_exec($ch);
        $curlErr = curl_error($ch);
        curl_close($ch);

        // 8️⃣ Tăng slot khám
        $doctor_id = $booking['doctor_id'];
        $date      = $booking['booking_date'];
        $session   = $booking['slot_time'];

        $slotApiUrl = "http://localhost/Medical-appointment/source/models/doctor_service/DoctorAPI.php/doctor/book";

        // Tăng SLOT (change = +1)
        $slotPayload = json_encode([
            "doctor_id" => $doctor_id,
            "date"      => $date,
            "session"   => $session,
            "change"    => +1
        ]);

        $slotCh = curl_init($slotApiUrl);
        curl_setopt($slotCh, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($slotCh, CURLOPT_POSTFIELDS, $slotPayload);
        curl_setopt($slotCh, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($slotCh, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

        $slotRes = curl_exec($slotCh);
        curl_close($slotCh);

        $slotResJson = json_decode($slotRes, true);

        $slotStatus = isset($slotResJson['success']) && $slotResJson['success'] === true;

    }
}
?>
