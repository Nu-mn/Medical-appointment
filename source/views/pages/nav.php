<nav class="navbar navbar-expand-sm bg-dark navbar-dark fixed-top" id="nav">
    <div class="container-fluid">
        <div class="leftnav justify-content-start">
            <a class="logo navbar-brand" href="index.php" data-bs-toggle="tooltip"  title="Về trang chủ">
                <img src="../images/logo.ico" alt="Logo" style="width:40px;" > 
                <h4>iBanking</h4>
            </a>
            <div class="openclose"><span class="navbar-toggler-icon" style="font-size:30px;cursor:pointer" onclick="open_closeNav() " class = "las la-bars"></span>  </div>
            
        </div>
        <div class="rightnav justify-content-end">
            <ul  class=" navbar-nav" >
                <li class="nav-item">
                    <a  href="#">
                    <span class = "fa fa-fw fa-envelope"></span>  
                    </a></li>
                <li class="nav-item">
                    <a  href="#">
                    <span class = "fa fa-bell"></span> 
                    </a></li>
                <li class="nav-item dropdown">
                        <a id="nav-user" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                        <img id="nav-avatar" src = "/SOA_GK/source/images/user.jpg" height = "30px"  width = "30px" alt = "Avatar">
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