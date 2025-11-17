<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

class NotificationService {
    private $conn;
    private $mailer;

    public function __construct($conn) {
        $this->conn = $conn;
        $this->mailer = new PHPMailer(true);

        // Config SMTP
        $this->mailer->isSMTP();
        $this->mailer->Host = 'smtp.gmail.com';  //SMTP Server
        $this->mailer->SMTPAuth = true;
        $this->mailer->Username = 'itnotformehihi@gmail.com';
        $this->mailer->Password = 'hwcrpxkjpsgvnhlc';
        $this->mailer->SMTPSecure = 'tls';
        $this->mailer->Port = 587;
    }

    private function maskEmail($email) {
        $parts = explode("@", $email);
        $name = $parts[0];
        $domain = $parts[1];
        return substr($name, 0, 1) . str_repeat("*", max(1, strlen($name) - 1)) . "@" . $domain;
    }

    public function sendEmail($userId, $to, $subject, $body) {
        // Save notify
        $stmt = $this->conn->prepare("
            INSERT INTO notifications (user_id, title, message, status) 
            VALUES (?, ?, ?, 'pending')
        ");
        $stmt->bind_param("iss", $userId, $subject, $body);
        $stmt->execute();
        $notificationId = $stmt->insert_id;

        try {
            $this->mailer->CharSet = 'UTF-8';
            $this->mailer->isHTML(true);
            // Send email
            $this->mailer->setFrom('itnotformehihi@gmail.com', 'Booking Hospital System');
            $this->mailer->addAddress($to);
            $this->mailer->Subject = $subject;
            $this->mailer->Body = $body;

            $this->mailer->send();

            // Update status = sent
            $stmt = $this->conn->prepare("
                UPDATE notifications SET status = 'sent' WHERE notification_id = ?
            ");
            $stmt->bind_param("i", $notificationId);
            $stmt->execute();

            return [
                "notification_id" => $notificationId,
                "status" => "sent",
                "masked_email" => $this->maskEmail($to)
            ];
        } catch (Exception $e) {
            $stmt = $this->conn->prepare("
                UPDATE notifications SET status = 'failed' WHERE notification_id = ?
            ");
            $stmt->bind_param("i", $notificationId);
            $stmt->execute();

            return [
                "message_id" => $notificationId,
                "status" => "failed",
                "error" => $e->getMessage(),
                "masked_email" => $this->maskEmail($to)
            ];
        }
    }

    public function sendPaymentEmail($userId, $to, $patientName, $appointmentDate, $amount) {
        $subject = "Xác nhận thanh toán lịch khám";
        $body = '
            <p>Bạn đã thanh toán thành công hóa đơn đặt lịch khám. Đây là phiếu khám bệnh của bạn. \n
            Vui lòng mang theo phiếu này khi đến khám tại bệnh viện. Xin chân thành cảm ơn!</p>
            <html>
                
            </html>';
        return $this->sendEmail($userId, $to, $subject, $body);
    }
}
