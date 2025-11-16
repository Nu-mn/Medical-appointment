<div class="main-content" id="main">
    <main>
        <div class="page-header">
            <div>
                <h3>Thông tin tài khoản</h3>
            </div>
        </div>

        <section class="profile-view">
            <div class="profile-container">
                <div class="profile-header">
                    <img src="../images/user.jpg" alt="Avatar" class="avatar-img" height="300px" width="300px">
                </div>

                <div class="profile-info">
                    <div class="info-item">
                        <i class="fa fa-user icon"></i>
                        <span class="label">Họ và tên: </span>
                        <span class="value"><?= htmlspecialchars($_SESSION["fullname"]) ?></span>
                    </div>

                    <div class="info-item">
                        <i class="fa fa-phone icon"></i>
                        <span class="label">Điện thoại: </span>
                        <span class="value"><?= htmlspecialchars($_SESSION["phone"]) ?></span>
                    </div>

                    <div class="info-item">
                        <i class="fa fa-envelope icon"></i>
                        <span class="label">Email: </span>
                        <span class="value"><?= htmlspecialchars($_SESSION["email"]) ?></span>
                    </div>

                    <div class="info-item">
                        <i class="fa fa-money icon"></i>
                        <span class="label">Số dư: </span>
                        <span class="value" id="ttsodu">Đang tải...</span>
                    </div>
                </div>
            </div>
        </section>
    </main>
</div>

<script>
const user_id = <?php echo (int)$_SESSION['id']; ?>;

// 🟢 Gọi API để lấy số dư
fetch(`/SOA_GK/source/models/transaction_service/TransactionAPI.php/transaction/balance?user_id=${user_id}`)
    .then(res => res.json())
    .then(data => {
        const el = document.getElementById("ttsodu");
        if (data.balance !== undefined && data.balance !== null) {
            const formatted = new Intl.NumberFormat('vi-VN').format(data.balance) + ' VNĐ';
            el.textContent = formatted; // ✅ dùng textContent thay vì value
        } else if (data.error) {
            el.textContent = "Lỗi: " + data.error;
        } else {
            el.textContent = "Không có dữ liệu";
        }
    })
    .catch(err => {
        console.error("Lỗi khi gọi API:", err);
        document.getElementById("ttsodu").textContent = "Không thể lấy số dư";
    });
</script>
