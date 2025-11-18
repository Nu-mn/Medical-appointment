<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- HEADER -->
<nav class="navbar navbar-expand-lg bg-white shadow-sm py-3">
    <div class="container">
        <a class="navbar-brand fw-bold text-primary" href="#" title="Về trang chủ">
            <img src="../images/pharmacy.png" alt="Logo" style="width:40px;" > 
            BookingHospital
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navMenu">
            <ul class="navbar-nav mx-auto mb-2 mb-lg-0 fw-semibold">
                <li class="nav-item"><a class="nav-link px-3" href="index.php?nav=home">Trang chủ</a></li>
                <li class="nav-item"><a class="nav-link px-3" onclick="checkLogin(event, 'appointment')">Đặt lịch</a></li>
                <li class="nav-item"><a class="nav-link px-3" onclick="checkLogin(event, 'invoice')">Hóa đơn</a></li>
                <li class="nav-item"><a class="nav-link px-3" href="index.php?nav=doctors">Bác sĩ</a></li>
                <li class="nav-item"><a class="nav-link px-3" onclick="checkLogin(event, 'patients')">Hồ sơ</a></li>
                <li class="nav-item"><a class="nav-link px-3" href="#">Liên hệ</a></li>

            </ul>
        
        <div class="rightnav justify-content-end">
            <ul  class=" navbar-nav" >
                <li class="nav-item dropdown">
                    <a id="nav-user" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                        <img id="nav-avatar" src = "../images/user.jpg" height = "30px"  width = "30px" alt = "Avatar">
                        <div class="infor">
                            <p id="user"></p>
                            <p id="email_user"></p>

                        </div>
                    </a>
            
                    <ul class="dropdown-menu dropdown-menu-end" >
                        <li>
                            <a class="dropdown-item" href="index.php?nav=hoso">
                                <i class = "fa fa-user fa-fw "> </i> 
                                <span>Hồ sơ</span>
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="../Login/logout.php">
                                <i class = "fa fa-sign-out fa-fw "> </i> 
                                <span>Đăng xuất </span>
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div> 
    </div>
</nav>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
window.addEventListener("DOMContentLoaded", () => {
    const user_id = <?= (int)$_SESSION['user_id'] ?>; 
    if (!user_id) return;

     fetch("http://localhost/medical-appointment/source/models/user_service/UserAPI.php/users/" + user_id)
        .then(res => res.json())
        .then(data => {
            if (data.error) return console.error("API Error:", data.error);

            document.getElementById("user").textContent = data.username || " ";
            document.getElementById("email_user").textContent = data.email || " ";
        })
        .catch(err => console.error("Lỗi fetch profile:", err));
});
</script>