<div class="profile-wrapper">

    <!-- Cover -->
    <div class="cover-section">
        <img src="../images/bg_doctor.jpg" class="cover-img">
    </div>

    <!-- Avatar + Basic Info -->
    <div class="avatar-section">
        <img src="../images/user.jpg" class="avatar">

        <div class="basic-info">
            <h2></h2>
            <p></p>
        </div>
    </div>

    <!-- Profile details -->
    <div class="profile-details">

        <div class="detail-row">
            <div class="detail-label">Họ và tên</div>
            <div class="detail-value username"></div>
        </div>

        <div class="detail-row">
            <div class="detail-label">Email</div>
            <div class="detail-value email"></div>
        </div>

        <div class="detail-row">
            <div class="detail-label">Số điện thoại</div>
            <div class="detail-value phone"></div>
        </div>

        <!-- THANH TOÁN MOMO -->



    </div>
</div>

<script>
window.addEventListener("DOMContentLoaded", () => { // Đảm bảo DOM load xong
    const user_id = <?= (int)$_SESSION['user_id'] ?>; // khai báo 1 lần duy nhất

    fetch("http://localhost/Medical-appointment/source/models/user_service/UserAPI.php/users/" + user_id)
        .then(res => {
            if (res.status === 503) {
                window.location.href = "/Medical-appointment/source/views/index.php?nav=404"; // dẫn tới trang bảo trì
                return;
            }
            return res.json();
        })
        .then(data => {
            if (data.error) return console.error("API Error:", data.error);

            // Cập nhật dữ liệu vào profile
            document.querySelector(".detail-value.username").textContent = data.username || "Chưa có họ tên";
            document.querySelector(".detail-value.email").textContent = data.email || "Chưa có email";
            document.querySelector(".detail-value.phone").textContent = data.phone || "Chưa có số điện thoại";

            document.querySelector(".basic-info h2").textContent = data.username; 
            document.querySelector(".basic-info p").textContent = data.email || "Chưa có email";
        })
        .catch(err => console.error("Lỗi fetch profile:", err));
});
</script>




<style>
.profile-wrapper {
    width: 900px;
    background: #fff;
    margin: 30px auto;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 8px 30px rgba(0,0,0,0.1);
    font-family: "Segoe UI", sans-serif;
}

/* Cover */
.cover-section {
    position: relative;
}
.cover-img {
    width: 100%;
    height: 220px;
    object-fit: cover;
}

/* Avatar + Name + Description */
.avatar-section {
    display: flex;
    align-items: flex-end; /* Avatar ở dưới, chữ thẳng hàng avatar */
    gap: 20px;
    padding: 0 30px;
    margin-top: -60px; /* Kéo avatar đè lên cover */
    position: relative;
}

.avatar {
    width: 130px;
    height: 130px;
    border-radius: 20px;
    border: 4px solid #fff;
    object-fit: cover;
    background: #fff;
    box-shadow: 0 4px 10px rgba(0,0,0,0.15);
}

/* Name + Description đẹp hơn */
.basic-info {
    padding-bottom: 15px; /* Căn chữ thẳng hàng avatar */
}

.basic-info h2 {
    margin: 0;
    font-size: 26px;
    font-weight: bold;
    color: #222;
}

.basic-info p {
    margin-top: 4px;
    font-size: 15px;
    color: #666;
}

/* Details */
.profile-details {
    padding: 25px 30px;
}

.detail-row {
    display: grid;
    grid-template-columns: 200px 1fr;
    padding: 18px 0;
    border-bottom: 1px solid #eee;
}

.detail-label {
    font-weight: bold;
    color: #333;
}

.detail-value {
    color: #555;
}



</style>