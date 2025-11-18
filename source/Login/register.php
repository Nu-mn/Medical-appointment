<?php
if (!isset($error)) {
    $error = "";
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng ký</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="header">
    <h1>BookingHospital</h1>
</div>

<div class="wrapper">
    <div class="container">
        <div class="col col-1">
            <img src="imageRegister.png" style="width: fit-content;">
            <h3>Sức khỏe tốt không chỉ giúp bạn sống lâu hơn, mà còn giúp bạn sống tốt hơn</h3>
        </div>

        <div class="col col-2">
            <div class="login-form">

                <form id="registerForm" method="POST" action="">
                    <h2 class="title-form">ĐĂNG KÝ</h2>

                    <div class="input-box">
                        <input name="username" type="text" placeholder="Nhập tên người dùng" required>
                        <i class='fa fa-user'></i>
                    </div>

                    <div class="input-box">
                        <input name="phone" type="text" placeholder="Nhập số điện thoại" required>
                    </div>

                    <div class="input-box">
                        <input name="email" type="email" placeholder="Nhập email" required>
                    </div>

                    <div class="input-box">
                        <input name="password" type="password" placeholder="Nhập mật khẩu" required>
                    </div>

                    <input type="submit" class="button" value="Đăng ký">

                    <h5 style="color: #fefefeff; text-align: center; margin-top: 15px;">Or</h5>

                    <h3 class="no-account">
                        Đã có tài khoản? <a href="login.php">Đăng nhập</a>
                    </h3>

                    <?php if ($error): ?>
                        <p style="color:#FA8A5A;"><?= htmlspecialchars($error) ?></p>
                    <?php endif; ?>
                </form>

            </div>
        </div>
    </div>
</div>

<div id="toast-container"></div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    document.getElementById("registerForm").addEventListener("submit", async function (e) {
        e.preventDefault();

        const username = document.querySelector("input[name='username']").value.trim();
        const phone    = document.querySelector("input[name='phone']").value.trim();
        const email    = document.querySelector("input[name='email']").value.trim();
        const password = document.querySelector("input[name='password']").value.trim();

        try {
            const response = await fetch("pregister.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ 
                    username, 
                    phone, 
                    email, 
                    password
                })
            });

            const data = await response.json();

            if (data.success) {
                showToast("Đăng ký thành công! Chuyển đến đăng nhập...", "success");
                setTimeout(() => window.location.href = "login.php", 1200);
            } else {
                showToast(data.message, "warning");
            }
        } catch (error) {
            showToast("Lỗi kết nối máy chủ. Vui lòng thử lại!", "error");
        }
    });
});

function showToast(message, type = "info", duration = 3000) {
    const container = document.getElementById("toast-container");
    const toast = document.createElement("div");
    toast.className = `toast ${type}`;
    toast.innerText = message;
    container.appendChild(toast);
    setTimeout(() => toast.classList.add("show"), 50);
    setTimeout(() => { toast.classList.remove("show"); setTimeout(() => toast.remove(), 400); }, duration);
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

.toast.success { background-color: #28a745; }
.toast.error   { background-color: rgba(229, 79, 79, 1); }
.toast.warning { background-color: #ffc107; color: #000; }
.toast.info    { background-color: #17a2b8; }

.no-account {
    margin-top: 0px;
    color: #000;
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

.col-1 {
    display: flex;
    align-items: center;
    justify-content: center;
    flex-direction: column;
    width: 45%;
    background:  #fefefeff; 
    border-radius: 2% 30% 20% 2%;
}
.input-box {
    position: relative;
    width: 100%;
    height: 50px;
    margin: 12px 0;
}
.login-form {
    display: flex;
    width: 100%;
    flex-direction: column;
    align-items: center;
    height: 32px;
    margin-top: -50px;
}
</style>

</body>
</html>



