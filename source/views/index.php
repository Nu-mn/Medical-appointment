<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../models/user_service/UserService.php';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Hospital</title>
    <link rel="stylesheet" href="style.css">
    <link rel="icon" type="image/x-icon" href="../images/pharmacy.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script defer src="script.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/dayjs@1/dayjs.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
    <!-- Ẩn user_id nếu đã đăng nhập -->
    <?php if(isset($_SESSION['id'])): ?>
        <span id="userId" style="display:none;"><?php echo $_SESSION['id']; ?></span>
    <?php endif; ?>
    <?php
      if (isset($_SESSION["user_id"])) {

            // NAVIGATION
            //main
            include __DIR__ . '/pages/main.php';
          }
    
    ?>
     <!-- Popup login -->
    <div id="loginPopup" style="
        display:none;
        position:fixed;
        top:0; left:0; right:0; bottom:0;
        background:rgba(0,0,0,0.5);
        justify-content:center;
        align-items:center;
        z-index:1000;
    ">
        <div style="
            background:#DBEEFD;
            padding:20px;
            border-radius:8px;
            width:300px;
            text-align:center;
            color: #1f1d1dff;
        ">
            <h5>Vui lòng đăng nhập</h5>
            <p>Bạn cần đăng nhập để đặt lịch khám.</p>
            <button class="btn btn-primary" onclick="loginNow()">Đăng nhập</button>
            <button class="btn btn-secondary" onclick="closePopup()">Hủy</button>
        </div>
    </div>

</body>
</html>
