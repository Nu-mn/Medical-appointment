<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- HEADER -->
<nav class="navbar navbar-expand-lg bg-white shadow-sm py-3">
    <div class="container">
        <a class="navbar-brand fw-bold text-primary" href="#" title="Về trang chủ">
            <img src="../images/logo.ico" alt="Logo" style="width:40px;" > 
           BookingHospital
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navMenu">
            <ul class="navbar-nav mx-auto mb-2 mb-lg-0 fw-semibold">
                <li class="nav-item"><a class="nav-link px-3" href="index.php?nav=home">Trang chủ</a></li>
                <li class="nav-item"><a class="nav-link px-3" href="index.php?nav=appointment">Đặt lịch</a></li>
                <li class="nav-item"><a class="nav-link px-3" href="index.php?nav=form">Phiếu khám</a></li>
                <li class="nav-item"><a class="nav-link px-3" href="index.php?nav=doctors">Bác sĩ</a></li>
                <li class="nav-item"><a class="nav-link px-3" href="index.php?nav=patients">Hồ sơ</a></li>
                <li class="nav-item"><a class="nav-link px-3" href="#">Liên hệ</a></li>

            </ul>
        
        <div class="rightnav justify-content-end">
            <ul  class=" navbar-nav" >
                <li class="nav-item dropdown">
                    <a id="nav-user" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                        <img id="nav-avatar" src = "/source/images/user.jpg" height = "30px"  width = "30px" alt = "Avatar">
                        <div class="infor">
                            <p id="user"><?= htmlspecialchars($_SESSION["username"] ?? 'Guest') ?></p>
                            <p id="email"><?= htmlspecialchars($_SESSION["email"] ?? '') ?></p>

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