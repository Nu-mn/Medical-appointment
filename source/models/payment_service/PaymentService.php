<?php

class PaymentService {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function getPaymentById($payment_id) {
        $sql = "SELECT * FROM payments WHERE payment_id=?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $payment_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    // ============================================================
    // 1️⃣ Lấy lịch sử thanh toán theo user_id
    // (bạn có thể join sang booking_db nếu muốn xem thêm thông tin)
    // ============================================================
    public function getPaymentsHistory($booking_id) {
        $sql = "SELECT p.*, a.patient_id, a.doctor_id, a.booking_date
                FROM payments p
                JOIN booking_db.appointments a ON p.booking_id = a.booking_id
                WHERE a.patient_id = ?
                ORDER BY p.created_at DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $booking_id);
        $stmt->execute();

        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // ============================================================
    // 2️⃣ Tạo giao dịch thanh toán
    // ============================================================
    public function createPayments($booking_id, $amount, $payment_method = "MoMo") {

    $sql = "INSERT INTO payments (booking_id, amount, payment_method, status, result_code)
            VALUES (?, ?, ?, 'unpaid', NULL)";

    $stmt = $this->conn->prepare($sql);

    // i = int, d = double, s = string
    $stmt->bind_param("ids", $booking_id, $amount, $payment_method);

    if (!$stmt->execute()) {
        return ["error" => "Không thể tạo giao dịch: " . $stmt->error];
    }

    return [
        "success" => true,
        "payment_id" => $this->conn->insert_id,   // Lấy ID mới
        "booking_id" => $booking_id,
        "amount" => $amount,
        "payment_method" => $payment_method
    ];
}


    // ============================================================
    // 3️⃣ Xác nhận thanh toán thành công
    // ============================================================
    public function confirmPayments($payment_id, $provider_transaction_id = null) {

        // Cập nhật trạng thái giao dịch
        $sql = "UPDATE payments SET status='paid', provider_transaction_id=? 
                WHERE payment_id=?";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("si", $provider_transaction_id, $payment_id);
        $stmt->execute();

        // Lấy lại thông tin giao dịch
        $sql2 = "SELECT * FROM payments WHERE payment_id=?";
        $stmt2 = $this->conn->prepare($sql2);
        $stmt2->bind_param("i", $payment_id);
        $stmt2->execute();

        $payment = $stmt2->get_result()->fetch_assoc();

        if (!$payment) {
            return ["error" => "Không tìm thấy giao dịch"];
        }

        return [
            "success" => true,
            "payment_id" => $payment['payment_id'],
            "booking_id" => $payment['booking_id'],
            "amount" => $payment['amount'],
            "status" => "paid"
        ];
    }

    // ============================================================
    // 4️⃣ Thanh toán thất bại
    // ============================================================
    public function failPayments($payment_id) {
        $sql = "UPDATE payments SET status='unpaid' WHERE payment_id=?";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $payment_id);
        $stmt->execute();

        return [
            "success" => true,
            "payment_id" => $payment_id,
            "status" => "unpaid"
        ];
    }

 


}
