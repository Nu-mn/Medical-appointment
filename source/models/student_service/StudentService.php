<?php
class StudentService {
    private $conn;

    // Nhận connection MySQLi khi tạo đối tượng
    public function __construct($mysqli_conn) {
        $this->conn = $mysqli_conn;
    }

    // Lấy tất cả sinh viên
    public function getAllStudents() {
        $sql = "SELECT * FROM students";
        $result = $this->conn->query($sql);

        $students = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $students[] = $row;
            }
        }
        return $students;
    }

    // Lấy thông tin 1 sinh viên theo MSSV
    public function getStudentByMSSV($mssv) {
        $stmt = $this->conn->prepare("SELECT * FROM students WHERE mssv = ?");
        if (!$stmt) return null;

        $stmt->bind_param("s", $mssv);
        $stmt->execute();

        $result = $stmt->get_result();
        $student = $result->fetch_assoc();

        $stmt->close();
        return $student;
    }

    // Lấy thông tin học phí của 1 sinh viên
   public function getStudentFee($key) {
    $stmt = $this->conn->prepare("
        SELECT 
            f.tuition_id,
            s.fullname, 
            s.department, 
            f.amount, 
            f.status, 
            f.due_date
        FROM students s
        JOIN studentfee f ON s.mssv = f.student_id
        WHERE s.mssv = ? OR f.tuition_id = ?
    ");

    if (!$stmt) return [];

    // Vì tuition_id là số, ta ép kiểu để tránh lỗi type mismatch
    $stmt->bind_param("si", $key, $key);

    $stmt->execute();
    $result = $stmt->get_result();

    $fees = [];
    while ($row = $result->fetch_assoc()) {
        $fees[] = $row;
    }

    $stmt->close();
    return $fees;
}


    public function updateFeeStatus($tuition_id, $status) {
        $stmt = $this->conn->prepare("
            UPDATE studentfee
            SET status = ?
            WHERE tuition_id = ?
        ");
        if (!$stmt) return false;

        $stmt->bind_param("si", $status, $tuition_id);
        $ok = $stmt->execute();
        $stmt->close();

        return $ok; // true nếu thành công, false nếu thất bại
    }
}
