<?php
class DoctorService {
    private $conn;

    public function __construct($mysqli_conn) {
        $this->conn = $mysqli_conn;
    }

    // 1. Lấy bác sĩ theo chuyên khoa
    public function getDoctorsBySpecialization($specialization_id) {
        $sql = "
            SELECT 
                d.*, 
                s.amount AS specialty_fee
            FROM doctors d
            JOIN specializations s ON d.specialization_id = s.specialization_id
            WHERE d.specialization_id = ? 
            AND d.status = 1
        ";

        $stmt = $this->conn->prepare($sql);
        if (!$stmt) return [];

        $stmt->bind_param("i", $specialization_id);
        $stmt->execute();
        $result = $stmt->get_result();

        $doctors = [];
        while ($row = $result->fetch_assoc()) {
            $doctors[] = $row;
        }
        $stmt->close();
        return $doctors;
    }


    // 2. Lấy lịch bác sĩ
    public function getSchedule($doctor_id) {
        $stmt = $this->conn->prepare("SELECT * FROM doctor_schedule WHERE doctor_id = ?");
        if (!$stmt) return [];

        $stmt->bind_param("i", $doctor_id);
        $stmt->execute();
        $result = $stmt->get_result();

        $schedules = [];
        while ($row = $result->fetch_assoc()) {
            $schedules[] = $row;
        }
        $stmt->close();
        return $schedules;
    }

    // 3. Kiểm tra slot trống
    public function checkSlot($doctor_id, $date, $session) {
        $stmt = $this->conn->prepare("
            SELECT available_slots 
            FROM doctor_schedule 
            WHERE doctor_id = ? AND date = ? AND session = ?
        ");
        if (!$stmt) return false;

        $stmt->bind_param("iss", $doctor_id, $date, $session);
        $stmt->execute();
        $result = $stmt->get_result();
        $slot = $result->fetch_assoc();
        $stmt->close();

        return $slot;
    }

    // 4. Đặt lịch (giảm slot)
    public function bookSlot($doctor_id, $date, $session) {
        $stmt = $this->conn->prepare("
            SELECT schedule_id, available_slots 
            FROM doctor_schedule 
            WHERE doctor_id = ? AND date = ? AND session = ?
        ");
        if (!$stmt) return ["success"=>false, "message"=>"Lỗi truy vấn"];

        $stmt->bind_param("iss", $doctor_id, $date, $session);
        $stmt->execute();
        $result = $stmt->get_result();
        $slot = $result->fetch_assoc();
        $stmt->close();

        if (!$slot) return ["success"=>false, "message"=>"Slot không tồn tại"];
        if ($slot['available_slots'] <= 0) return ["success"=>false, "message"=>"Hết slot"];

        // Giảm 1 slot
        $newSlots = $slot['available_slots'] - 1;
        $update = $this->conn->prepare("UPDATE doctor_schedule SET available_slots = ? WHERE schedule_id = ?");
        if (!$update) return ["success"=>false, "message"=>"Lỗi update"];

        $update->bind_param("ii", $newSlots, $slot['schedule_id']);
        $ok = $update->execute();
        $update->close();

        return ["success"=>$ok, "message"=> $ok ? "Đặt lịch thành công" : "Đặt lịch thất bại"];
    }

    // Lấy danh sách chuyên khoa
    public function getSpecializations() {
        $stmt = $this->conn->prepare("SELECT * FROM specializations");
        if (!$stmt) return [];

        $stmt->execute();
        $result = $stmt->get_result();

        $specializations = [];
        while ($row = $result->fetch_assoc()) {
            $specializations[] = $row;
        }
        $stmt->close();
        return $specializations;
    }
}
?>
