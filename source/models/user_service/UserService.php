<?php
class UserService {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function getAllUser() {
    $sql = "SELECT * FROM users";
    $result = $this->conn->query($sql);

    $profiles = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $profiles[] = $row;
        }
    }
    return $profiles;
}


    // Lấy profile theo user_id
   public function getUserByUserId($user_id) {
    $stmt = $this->conn->prepare("SELECT * FROM users WHERE user_id = ?");
    if (!$stmt) throw new Exception("Lỗi chuẩn bị SQL: " . $this->conn->error);

    $stmt->bind_param("i", $user_id);
    $stmt->execute();

    $result = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    return $result ?: null;
}


    // Đăng nhập
    public function login($phone, $password) {
    $stmt = $this->conn->prepare("SELECT user_id, phone 
                                  FROM users 
                                  WHERE phone = ? AND password = ?"
                                );

  $stmt->bind_param("ss", $phone, $password);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    if ($user) {
        return [
            "success" => true,
            "user" => $user
        ];
    } else {
        return [
            "success" => false,
            "message" => "Sai số điện thoại hoặc mật khẩu"
        ];
    }
    }

    // Đăng ký
   // Kiểm tra số điện thoại tồn tại
public function checkPhoneExist($phone) {
    $stmt = $this->conn->prepare("SELECT user_id FROM users WHERE phone = ?");
    $stmt->bind_param("s", $phone);   // "s" = string
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->num_rows > 0;
}
public function checkEmailExist($email) {
    $stmt = $this->conn->prepare("SELECT user_id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);   // "s" = string
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->num_rows > 0;
}
// Đăng ký user mới

public function registerUser($username, $phone, $email, $password) {
    $stmt = $this->conn->prepare("INSERT INTO users (username, phone, email, password) VALUES (?, ?, ?, ?)");
    
    if (!$stmt) {
        throw new Exception("Lỗi chuẩn bị SQL: " . $this->conn->error);
    }
    $stmt->bind_param("ssss", $username, $phone, $email, $password);

    return $stmt->execute();
}






}

   

    


   

?>
