<?php
class InvoiceService {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

     // Tạo hóa đơn và chi tiết dịch vụ
    public function createInvoice($bookingId, $paymentId, $userId, $totalAmount, $details) {
        // Lưu hóa đơn chính
        $stmt = $this->conn->prepare("
            INSERT INTO invoices (booking_id, payment_id, user_id, total_amount)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->bind_param("iiid", $bookingId, $paymentId, $userId, $totalAmount);
        $stmt->execute();
        $invoiceId = $stmt->insert_id;

        // Lưu chi tiết hóa đơn
        $stmtDetail = $this->conn->prepare("
            INSERT INTO invoice_detail (invoice_id, service_name, cost)
            VALUES (?, ?, ?)
        ");
        foreach ($details as $item) {
            $stmtDetail->bind_param("isd", $invoiceId, $item['service_name'], $item['cost']);
            $stmtDetail->execute();
        }

        return [
            "invoice_id" => $invoiceId,
            "status" => "created"
        ];
    }

    // Lấy lịch sử hóa đơn
    public function getInvoiceHistory($userId) {
        $stmt = $this->conn->prepare("
            SELECT i.invoice_id, i.booking_id, i.payment_id, i.total_amount, i.issued_date, 
                   GROUP_CONCAT(CONCAT(d.service_name, ':', d.cost) SEPARATOR ', ') AS services
            FROM invoices i
            LEFT JOIN invoice_detail d ON i.invoice_id = d.invoice_id
            WHERE i.user_id = ? 
            GROUP BY i.invoice_id
            ORDER BY i.issued_date DESC
        ");
       
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    
}
