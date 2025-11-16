<?php 
    class OtpService {
        private $conn;

        public function __construct($conn) {
            $this->conn = $conn;
        }

        private function generateRandomOtp($length = 6) {
            return str_pad(random_int(0, pow(10, $length) - 1), $length, '0', STR_PAD_LEFT);
        }

        public function generateOtp($transactionId, $ttl = 300) {
            // Xóa OTP cũ nếu có
            $stmt = $this->conn->prepare("DELETE FROM otps WHERE transaction_id = ? AND status = 'unused'");
            $stmt->bind_param("s", $transactionId);
            $stmt->execute();

            // Sinh OTP mới (đảm bảo không trùng code đang active)
            do {
                $otp = $this->generateRandomOtp();
                $check = $this->conn->prepare("SELECT otp_id FROM otps WHERE code = ? AND status = 'unused'");
                $check->bind_param("s", $otp);
                $check->execute();
                $check->store_result();
            } while ($check->num_rows > 0);

            $expiredAt = date('Y-m-d H:i:s', time() + $ttl); // 5 phút

            // Lưu vào DB
            $stmt = $this->conn->prepare("
                INSERT INTO otps (transaction_id, code, expired_at) 
                VALUES (?, ?, ?)
            ");
            $stmt->bind_param("sss", $transactionId, $otp, $expiredAt);
            $stmt->execute();
            $otpId = $stmt->insert_id;

            return [
                "otp_id" => $otpId,
                "expired_at" => $expiredAt,
                "otp_code" => $otp
            ];
        }

        public function validateOtp($transactionId, $otp) {
            $stmt = $this->conn->prepare("
                SELECT * FROM otps 
                WHERE transaction_id = ? AND status = 'unused' 
                ORDER BY created_at DESC LIMIT 1
            ");
            $stmt->bind_param("s", $transactionId);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();

            if (!$row) {
                return ["valid" => false, "error" => "not_found"];
            }

            if (strtotime($row['expired_at']) < time()) {
                return ["valid" => false, "error" => "expired"];
            }

            if ($row['code'] !== $otp) {
                return ["valid" => false, "error" => "invalid"];
            }

            $this->markUsed($row['transaction_id']);
            return ["valid" => true];
        }

        public function markUsed($transactionId) {
            $stmt = $this->conn->prepare("
                UPDATE otps SET status = 'used' 
                WHERE transaction_id = ? AND status = 'unused'
            ");
            $stmt->bind_param("i", $transactionId);
            $stmt->execute();
            return ["success" => true];
        }
    }
   
?>