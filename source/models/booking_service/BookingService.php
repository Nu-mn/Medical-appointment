<?php
class BookingService {

    private $conn;
    private $table = "appointments";

    public function __construct($db) {
        $this->conn = $db;
    }

    // GET ALL BOOKINGS
    public function getAll() {
        $sql = "SELECT * FROM $this->table ORDER BY booking_id DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC); // trả về mảng associative
    }

    // GET BY STATUS
    public function getByStatus($status) {
        $sql = "SELECT * FROM $this->table WHERE status = ? ORDER BY booking_id DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$status]);
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC); 
    }

   // Tạo mới appointment, trả về booking_id vừa tạo
    public function create($data) {
        $sql = "INSERT INTO {$this->table}
                (patient_id, doctor_id, specialization_id, booking_date, amount, slot_time, status)
                VALUES (?, ?, ?, ?, ?, ?, ?)";

        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $this->conn->error);
        }

        // Ép kiểu / xử lý default
        $patient_id        = (int)$data['patient_id'];
        $doctor_id         = (int)$data['doctor_id'];
        $specialization_id = (int)$data['specialization_id'];
        $booking_date      = $data['booking_date'];        // 'YYYY-MM-DD'
        $amount            = (float)$data['amount'];       // DECIMAL
        $slot_time         = $data['slot_time'];           // 'HH:MM:SS' or 'HH:MM'
        $status            = $data['status'] ?? 'pending';

        $stmt->bind_param(
            "iiisdss",
            $patient_id,
            $doctor_id,
            $specialization_id,
            $booking_date,
            $amount,
            $slot_time,
            $status
        );

        if (!$stmt->execute()) {
            throw new Exception("Execute failed: " . $stmt->error);
        }

        // booking_id mới tạo
        return $this->conn->insert_id;
    }

    // Lấy appointment theo booking_id
    public function getById($booking_id) {
        $sql = "SELECT booking_id, patient_id, doctor_id, specialization_id,
                       booking_date, amount, slot_time, status, created_at
                FROM {$this->table}
                WHERE booking_id = ?";

        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $this->conn->error);
        }

        $stmt->bind_param("i", $booking_id);

        if (!$stmt->execute()) {
            throw new Exception("Execute failed: " . $stmt->error);
        }

        $result = $stmt->get_result();
        return $result->fetch_assoc();   // trả về mảng hoặc null nếu không có
    }

    // Cập nhật status
    public function updateStatus($booking_id, $status) {
        $sql = "UPDATE {$this->table}
                SET status = ?
                WHERE booking_id = ?";

        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $this->conn->error);
        }

        $stmt->bind_param("si", $status, $booking_id);

        if (!$stmt->execute()) {
            throw new Exception("Execute failed: " . $stmt->error);
        }

        return $stmt->affected_rows > 0;
    }
}
?>
