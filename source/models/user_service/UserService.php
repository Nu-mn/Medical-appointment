<?php
class UserService {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // Lấy tất cả user
    public function getAllUsers() {
        $sql = "SELECT id, username, fullname, email, phone FROM users";
        $result = $this->conn->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Lấy user theo ID
    public function getUserById($id) {
        $stmt = $this->conn->prepare("SELECT id, username, fullname, email, phone FROM users WHERE id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

   

    // Đăng nhập
    public function login($username, $password) {
        $stmt = $this->conn->prepare("SELECT id, username, fullname, email, phone FROM users WHERE username=? AND password=?");
        $stmt->bind_param("ss", $username, $password);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();

        if ($user) {
            return ["success" => true, "user" => $user];
        } else {
            return ["success" => false, "message" => "Sai username hoặc password"];
        }
    }

    // Cập nhật thông tin user
   
}
?>
