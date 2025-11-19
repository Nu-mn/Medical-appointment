<?php
class TransactionService {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // Kiểm tra số dư tài khoản
    public function getBalance($user_id) {
        $stmt = $this->conn->prepare("SELECT balance FROM account WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc()['balance'] ?? null;
    }

    // Khóa MSSV (FIFO cơ bản)
    private function isTuitionLocked($tuition_id) {
        $stmt = $this->conn->prepare("SELECT status FROM transactions WHERE tuition_id = ? AND status = 'processing'");
        $stmt->bind_param("i", $tuition_id);
        $stmt->execute();
        return $stmt->get_result()->num_rows > 0;
    }

    // Tạo giao dịch
    public function createTransaction($user_id, $tuition_id, $amount) {
        // // 🔹 1. Kiểm tra xem user này đã có giao dịch đang xử lý với học phí này chưa
        // $checkStmt = $this->conn->prepare("
        //     SELECT transaction_id 
        //     FROM transactions 
        //     WHERE user_id = ? 
        //     AND tuition_id = ? 
        //     AND status = 'processing'
        //     LIMIT 1
        // ");
        // $checkStmt->bind_param("ii", $user_id, $tuition_id);
        // $checkStmt->execute();
        // $checkResult = $checkStmt->get_result();

        // if ($checkResult->num_rows > 0) {
        //     $existingTx = $checkResult->fetch_assoc();
        //     return [
        //         "transaction_id" => $existingTx["transaction_id"],
        //         "status" => "processing",
        //         "message" => "Giao dịch đang xử lý"
        //     ];
        // }

        // 🔹 1. Kiểm tra khóa MSSV bởi user khác
        $stmtLock = $this->conn->prepare("
            SELECT user_id 
            FROM transactions 
            WHERE tuition_id = ? 
            AND status = 'processing'
        ");
        $stmtLock->bind_param("i", $tuition_id);
        $stmtLock->execute();
        $lockResult = $stmtLock->get_result();

        if ($lockResult->num_rows > 0) {
            $lockUser = $lockResult->fetch_assoc();
            if ($lockUser["user_id"] != $user_id) {
                return ["error" => "MSSV này đang được thanh toán bởi tài khoản khác, vui lòng thử lại sau"];
            }
        }
        // 🔹 2. Kiểm tra user này có đang có giao dịch 'processing' nào khác không
        $stmtUser = $this->conn->prepare("
            SELECT transaction_id 
            FROM transactions 
            WHERE user_id = ? 
            AND status = 'processing'
        ");
        $stmtUser->bind_param("i", $user_id);
        $stmtUser->execute();
        $userResult = $stmtUser->get_result();

        if ($userResult->num_rows > 0) {
            $tx = $userResult->fetch_assoc();
            return ["error" => "Bạn đang có giao dịch #" . $tx['transaction_id'] . " chưa hoàn tất. Vui lòng hoàn tất hoặc chờ xử lý trước khi tạo giao dịch mới."];
        }
        $stmtUser->close();

        // 🔹 3. Kiểm tra số dư
        $balance = $this->getBalance($user_id);
        if ($balance === null) {
            return ["error" => "Tài khoản không tồn tại"];
        }
        if ($balance < $amount) {
            return ["error" => "Số dư không đủ"];
        }

        // 🔹 4. Tạo giao dịch mới
        $stmt = $this->conn->prepare("
            INSERT INTO transactions (user_id, tuition_id, amount, balance, status) 
            VALUES (?, ?, ?, ?, 'processing')
        ");
        $stmt->bind_param("iidd", $user_id, $tuition_id, $amount, $balance);

        if ($stmt->execute()) {
            return [
                "transaction_id" => $stmt->insert_id,
                "status" => "processing",
                "message" => "Giao dịch mới được tạo"
            ];
        }

        return ["error" => "Không thể tạo giao dịch"];
    }


    // Xác nhận OTP thành công => trừ tiền, cập nhật giao dịch
   public function confirmTransaction($transaction_id) {
        $this->conn->begin_transaction();

        try {
            // ✅ Lấy thông tin giao dịch bao gồm tuition_id
            $stmt = $this->conn->prepare("SELECT user_id, amount, tuition_id, balance FROM transactions WHERE transaction_id = ? AND status = 'processing'");
            $stmt->bind_param("i", $transaction_id);
            $stmt->execute();
            $result = $stmt->get_result()->fetch_assoc();

            if (!$result) {
                throw new Exception("Giao dịch không tồn tại hoặc đã xử lý");
            }

            $user_id = $result['user_id'];
            $amount = $result['amount'];
            $tuition_id = $result['tuition_id'];
            $balance_before = $result['balance'];

            // ✅ Trừ tiền trong tài khoản
            $update = $this->conn->prepare("UPDATE account SET balance = balance - ? WHERE user_id = ?");
            $update->bind_param("di", $amount, $user_id);
            if (!$update->execute()) {
                throw new Exception("Lỗi khi trừ tiền");
            }
            $balance_after = $balance_before - $amount;
            // ✅ Cập nhật trạng thái giao dịch
            $updateTx = $this->conn->prepare("
                UPDATE transactions 
                SET status = 'success',
                    balance = ?,
                    updated_at = NOW()
                WHERE transaction_id = ?
            ");

             $updateTx->bind_param("di",$balance_after, $transaction_id);
            $updateTx->execute();

            $this->conn->commit();

            // ✅ Trả về tuition_id để TransactionAPI biết mà gọi StudentAPI
            return [
                "message" => "Giao dịch thành công",
                "tuition_id" => $tuition_id
            ];

        } catch (Exception $e) {
            $this->conn->rollback();
            return ["error" => $e->getMessage()];
        }
    }

    // Giao dịch thất bại => rollback
    public function failTransaction($transaction_id) {
        $stmt = $this->conn->prepare("UPDATE transactions SET status = 'failed' WHERE transaction_id = ?");
        $stmt->bind_param("i", $transaction_id);
        $stmt->execute();
        return ["message" => "Giao dịch đã bị hủy"];
    }


    // 📜 Lấy lịch sử giao dịch của 1 sinh viên
public function getTransactionHistory($user_id) {
    $stmt = $this->conn->prepare("
        SELECT 
            t.transaction_id,
            t.tuition_id,
            t.amount,
            t.status,
            t.created_at,
            t.updated_at,
            t.balance
        FROM transactions t
        INNER JOIN account a ON t.user_id = a.user_id
        WHERE t.user_id = ?
        ORDER BY t.created_at DESC
    ");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $transactions = [];
    while ($row = $result->fetch_assoc()) {
        $transactions[] = $row;
    }

    return $transactions;
}

}
?>
