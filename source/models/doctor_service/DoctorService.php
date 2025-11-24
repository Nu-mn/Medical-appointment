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
                s.name AS specialization_name,
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
        $stmt = $this->conn->prepare("
            SELECT * 
            FROM doctor_schedule 
            WHERE doctor_id = ? 
            AND available_slots > 0
            ORDER BY date ASC
        ");

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


    // 4. Đặt lịch (giảm slot)
    public function updateSlot($doctor_id, $date, $session, $change) {
        // $change có thể là +1 hoặc -1

        // 1. Lấy slot hiện tại
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

        $newSlots = $slot['available_slots'] + $change;

        // Không cho slot âm
        if ($newSlots < 0) return ["success"=>false, "message"=>"Đã hết slot"];

        // 2. Update slot
        $update = $this->conn->prepare("
            UPDATE doctor_schedule 
            SET available_slots = ? 
            WHERE schedule_id = ?
        ");
        if (!$update) return ["success"=>false, "message"=>"Lỗi update"];

        $update->bind_param("ii", $newSlots, $slot['schedule_id']);
        $ok = $update->execute();
        $update->close();

        return [
            "success" => $ok,
            "message" => $ok ? "Cập nhật slot thành công" : "Cập nhật slot thất bại",
            "new_slots" => $newSlots
        ];
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

    public function getSpecializationNameById($specialization_id) {
    $stmt = $this->conn->prepare("SELECT name FROM specializations WHERE specialization_id = ?");
    if (!$stmt) return null;

    // SỬA: dùng đúng biến $specialization_id
    $stmt->bind_param("i", $specialization_id);

    $stmt->execute();
    $result = $stmt->get_result();

    $row = $result->fetch_assoc();
    $stmt->close();

    return $row ? $row['name'] : null;
}


}
?>
