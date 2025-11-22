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

   // CREATE, trả về booking_id vừa tạo
    public function create($data) {
        $sql = "INSERT INTO {$this->table}
                (user_id, patient_id, doctor_id, specialization_id, booking_date, amount, slot_time)
                VALUES (?, ?, ?, ?, ?, ?, ?)";

        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $this->conn->error);
        }

        // Ép kiểu
        $user_id        = (int)$data['user_id'];
        $patient_id     = (int)$data['patient_id'];
        $doctor_id      = (int)$data['doctor_id'];
        $specialization_id = (int)$data['specialization_id'];
        $booking_date   = $data['booking_date'];  // yyyy-mm-dd
        $amount         = (float)$data['amount'];
        $slot_time      = $data['slot_time'];     // HH:MM:SS

        // i = int, d = double, s = string
        $stmt->bind_param(
            "iiiisds",
            $user_id,
            $patient_id,
            $doctor_id,
            $specialization_id,
            $booking_date,
            $amount,
            $slot_time
        );

        if (!$stmt->execute()) {
            throw new Exception("Execute failed: " . $stmt->error);
        }

        return $this->conn->insert_id;
    }


    // Lấy appointment theo booking_id
    public function getById($booking_id) {
        $sql = "SELECT booking_id, patient_id, doctor_id, specialization_id,
                       booking_date, amount, slot_time, created_at
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

}
?>
