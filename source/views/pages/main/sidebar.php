<div class="sidebar"  id="mySidebar" >
    <div class="sidebar-user">
       <img src="../images/user.jpg" height = "70px"  width = "70px" alt = "">
            <div>
                <h3><?= htmlspecialchars($_SESSION["fullname"]) ?></h3>
               
            </div>
    </div>
    <div class="sidebar-menu">
        <ul >
            <li class="item">
                <a class="link" href="index.php">
                    <span class = "fa fa-home"></span>
                    <p class="item-sidebar">Trang chủ</p>
                </a>
            </li>
            
                    <li class="item">
                        <a class="link" href="index.php?sidebar=thanhtoan">
                            <span class = "fa fa-users"></span>
                            <p class="item-sidebar">Thanh toán</p> 
                        </a>
                    </li>
              
            <li class="item">
                <a class="link" href="index.php?sidebar=lichsugiaodich">
                    <span class = "fa fa-tags"></span>
                    <p class="item-sidebar">Lịch sử giao dịch</p>
                </a>
            </li>
            <br>
        </ul>
    </div>
    <div class="logout">
        <a class="link" href="../Login/logout.php">
            <span class = "fa fa-sign-out"></span>
        </a>
    </div>
</div> 