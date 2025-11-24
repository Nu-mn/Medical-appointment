<?php
if (!isset($error)) {
    $error = "";
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HealthcareBooking</title>
    <link rel="icon" type="image/x-icon" href="images/logo.ico">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
</head>
<body>
<div class="header">
    <h1>HealthcareBooking</h1>
</div>
<div class="wrapper">
    <div class="container">
        <div class="col col-1">
            <img src="imageLogin.png" style="width: fit-content;">
            <h3>Không có gì quý giá hơn là sức khỏe tốt. Đó chính là tài sản giá trị nhất của một con người</h3>
        </div>
        <div class="col col-2">
            <div class="login-form">
                <form id="loginForm" method="POST" action="">
                    <h2 class="title-form">Đăng Nhập</h2>
                    <div class="input-box">
                        <input name="phone" type="text" placeholder="Nhập số điện thoại" required>
                        <i class='fa fa-user'></i>
                    </div>
                    <div class="input-box">
                        <input name="password" type="password" placeholder="Nhập mật khẩu" required>
                        <div id="eye">
                            <i class='fa fa-eye'></i>
                        </div>
                    </div>
                    <input name="login" type="submit" class="button" value="Đăng nhập">
                    <h5 style="color: #fefefeff; text-align: center; margin-top: 15px;">Or</h5>
                   <h3 class="no-account">
                        Chưa có tài khoản?  
                            <a href="register.php">Đăng ký</a>
                    </h3>

                  <?php if ($error): ?>
                        <p style="color:#FA8A5A; margin: 10px 10px;"><?= htmlspecialchars($error) ?></p>
                    <?php endif; ?>
                </form>
            </div>
        </div>
    </div>
</div>
<div id="toast-container"></div>
<script src="main.js"></script>
</body>
</html>

<script>
document.addEventListener("DOMContentLoaded", function () {
    document.getElementById("loginForm").addEventListener("submit", async function (e) {
        e.preventDefault();

        const phone = document.querySelector("input[name='phone']").value.trim();
        const password = document.querySelector("input[name='password']").value.trim();

        try {
            const response = await fetch("plogin.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ phone, password })
            });

            const data = await response.json();

            if (data.success) {
                // ✅ Đăng nhập thành công
                window.location.href = "../views/index.php";
            } else {
                // ⚠️ Sai username hoặc mật khẩu -> hiện popup cảnh báo
                showToast(data.message || "Sai số điện thoại hoặc mật khẩu. Vui lòng thử lại!", "warning");
            }
        } catch (error) {
            // ⚠️ Lỗi hệ thống, không in console, chỉ hiện thông báo
            showToast(" Có lỗi xảy ra khi kết nối đến máy chủ. Vui lòng thử lại sau!", "error");
        }
    });
});
</script>


<script>
function showToast(message, type = "info", duration = 3000) {
    const container = document.getElementById("toast-container");
    const toast = document.createElement("div");
    toast.className = `toast ${type}`;
    toast.innerText = message;
    container.appendChild(toast);

    // Hiện ra với hiệu ứng
    setTimeout(() => toast.classList.add("show"), 100);

    // Ẩn và xóa sau duration ms
    setTimeout(() => {
        toast.classList.remove("show");
        setTimeout(() => toast.remove(), 400);
    }, duration);
}
</script>

<style>
#toast-container {
    position: fixed;
    bottom: 20px;
    right: 20px;
    z-index: 9999;
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.toast {
    min-width: 250px;
    background-color: #333;
    color: white;
    padding: 12px 16px;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(234, 234, 234, 0.3);
    opacity: 0;
    transform: translateX(100%);
    transition: all 0.4s ease;
    font-size: 14px;
}

.toast.show {
    opacity: 1;
    transform: translateX(0);
}

.toast.success { background-color: #28a745; } /* xanh */
.toast.error   { background-color: rgba(229, 79, 79, 1); } /* đỏ */
.toast.warning { background-color: #ffffffff; color: #000; } /* vàng */
.toast.info    { background-color: #17a2b8; } /* xanh dương nhạt */

.no-account {
    margin-top: 10px;
    color: #000000ff;
    text-align: center;
    font-size: 15px;
}

.no-account a {
    color: #fc5a4e;
    font-weight: 600;
    text-decoration: none;
}

.no-account a:hover {
    text-decoration: underline;
}

.input-box {
    position: relative;
    width: 100%;
    height: 50px;
    margin: 27px 0;
}


</style>

