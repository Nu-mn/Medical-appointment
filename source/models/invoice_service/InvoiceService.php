<?php
class InvoiceService {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // Tạo phiếu khám 
    public function createInvoice($bookingId, $paymentId, $userId, $fee, $specializationName, $patientName, $numOrder, $status) {
        $stmt = $this->conn->prepare("
            INSERT INTO invoices 
                (booking_id, payment_id, user_id, fee, specialization_name, patient_name, num_order, status)
            VALUES 
                (?, ?, ?, ?, ?, ?, ?, ?)
        ");

        if (!$stmt) {
            return ["success" => false, "error" => "Lỗi prepare"];
        }

        $stmt->bind_param(
            "iiisssis",
            $bookingId,
            $paymentId,
            $userId,
            $fee,
            $specializationName,
            $patientName,
            $numOrder,
            $status
        );

        $success = $stmt->execute();
        $invoiceId = $stmt->insert_id;
        $stmt->close();

        return [
            "success" => $success,
            "invoice_id" => $invoiceId
        ];
    }

    // Lấy danh sách phiếu khám
    public function getInvoiceHistory($userId) {
        $stmt = $this->conn->prepare("
            SELECT *
            FROM invoices
            WHERE user_id = ?
            ORDER BY invoice_id DESC
        ");

        if (!$stmt) {
            return [];
        }

        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();

        $invoices = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        return $invoices;
    }
}
?>
