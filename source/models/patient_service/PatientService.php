<?php
class PatientService {
    private $conn;
    private $table = "patients";

    public function __construct($db) {
        $this->conn = $db;
    }

    // Lấy toàn bộ bệnh nhân
    public function getAll($id) {
        $stmt = $this->conn->prepare("SELECT * FROM patients WHERE user_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result(); // lấy kết quả từ mysqli_stmt
        return $result->fetch_all(MYSQLI_ASSOC); // trả về mảng associative
    }


    // Lấy theo ID
    public function getById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM patients WHERE patient_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc(); // trả về 1 row
    }


  public function create($data) {
    $query = "INSERT INTO patients
             (user_id, full_name, date_of_birth, gender, email, phone, address, citizen_id, insurance_number)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $this->conn->prepare($query);
    if (!$stmt) {
    die("Prepare failed: " . $this->conn->error);
}
    $stmt->bind_param(
        "issssssss", // i=int, s=string
        $data["user_id"],
        $data["full_name"],
        $data["date_of_birth"],
        $data["gender"],
        $data["email"],
        $data["phone"],
        $data["address"],
        $data["citizen_id"],
        $data["insurance_number"]
    );

    return $stmt->execute();
}
public function update($id, $data) {
    $query = "UPDATE patients SET
            full_name = ?, date_of_birth = ?, gender = ?, email = ?, phone = ?, address = ?, 
            citizen_id = ?, insurance_number = ?
            WHERE patient_id = ?";

    $stmt = $this->conn->prepare($query);
    if (!$stmt) {
    die("Prepare failed: " . $this->conn->error);
}
    $stmt->bind_param(
        "ssssssssi",
        $data["full_name"],
        $data["date_of_birth"],
        $data["gender"],
        $data["email"],
        $data["phone"],
        $data["address"],
        $data["citizen_id"],
        $data["insurance_number"],
        $id
    );

    return $stmt->execute();
}



    // Xóa
    public function delete($id) {
        $query = "DELETE FROM $this->table WHERE patient_id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$id]);
    }
}
