<?php
if (!function_exists("connectDB")) {
  function connectDB($nameDB) {
    $servername = "localhost";
    $username = "root";
    $password = "";
    $database = $nameDB;
    
    // Tạo kết nối với database
    $conn = new mysqli($servername, $username, $password, $database);

    // Kiểm tra kết nối
    if ($conn->connect_error) {
      die("Connection failed: " . $conn->connect_error);
    }

    return $conn;
  }
}
?>