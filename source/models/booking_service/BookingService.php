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

    // GET BY ID
    public function getById($id) {
        $sql = "SELECT * FROM $this->table WHERE booking_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();

        $result = $stmt->get_result();
        return $result->fetch_assoc(); 
    }

    // GET BY STATUS
    public function getByStatus($status) {
        $sql = "SELECT * FROM $this->table WHERE status = ? ORDER BY booking_id DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$status]);
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC); 
    }

    // CREATE
    public function create($data) {
        $sql = "INSERT INTO $this->table 
                (patient_id, doctor_id, booking_date, specialization_id, amount, slot_time, status)
                VALUES (?, ?, ?, ?, ?, ?, ?)";

        $stmt = $this->conn->prepare($sql);

        $stmt->bind_param(
            "iisisss", 
            $data["patient_id"],
            $data["doctor_id"],
            $data["booking_date"],
            $data["specialization_id"],
            $data["amount"],
            $data["slot_time"],
            $data["status"]
        );

        return $stmt->execute();
    }


    // // UPDATE
    // public function update($id, $data) {
    //     $sql = "UPDATE $this->table SET
    //                 patient_id = ?, 
    //                 doctor_id = ?, 
    //                 booking_date = ?, 
    //                 specialization_id = ?, 
    //                 amount = ?, 
    //                 slot_time = ?, 
    //                 status = ?
    //             WHERE booking_id = ?";

    //     $stmt = $this->conn->prepare($sql);

    //     $stmt->bind_param(
    //         "iisisssi",
    //         $data["patient_id"],
    //         $data["doctor_id"],
    //         $data["booking_date"],
    //         $data["specialization_id"],
    //         $data["amount"],
    //         $data["slot_time"],
    //         $data["status"],
    //         $id
    //     );

    //     return $stmt->execute();
    // }

    // UPDATE STATUS ONLY
    public function updateStatus($id, $status) {
        $sql = "UPDATE $this->table SET status = ? WHERE booking_id = ?";
        $stmt = $this->conn->prepare($sql);

        $stmt->bind_param("si", $status, $id);

        return $stmt->execute();
    }


    // DELETE
    public function delete($id) {
        $sql = "DELETE FROM $this->table WHERE booking_id = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$id]);
    }
}
?>
