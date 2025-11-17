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
        // Lấy tất cả hóa đơn của user
        $stmt = $this->conn->prepare("
            SELECT invoice_id, booking_id, payment_id, total_amount, issued_date
            FROM invoices
            WHERE user_id = ?
            ORDER BY issued_date DESC
        ");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $invoicesResult = $stmt->get_result();
        $invoices = $invoicesResult->fetch_all(MYSQLI_ASSOC);

        // Lấy chi tiết dịch vụ cho từng hóa đơn
        foreach ($invoices as &$inv) {
            $stmtDetail = $this->conn->prepare("
                SELECT service_name, cost
                FROM invoice_detail
                WHERE invoice_id = ?
            ");
            $stmtDetail->bind_param("i", $inv['invoice_id']);
            $stmtDetail->execute();
            $detailsResult = $stmtDetail->get_result();
            $inv['services'] = $detailsResult->fetch_all(MYSQLI_ASSOC);
        }

        return $invoices;
    }


    
}
