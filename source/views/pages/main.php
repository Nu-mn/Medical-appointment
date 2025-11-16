<div class="main">
    <?php 
    include __DIR__ . "/main/sidebar.php";
    ?>

    <?php 
    if (isset($_GET['sidebar'])) {
        $side = $_GET['sidebar'];

        if ($side == 'trangchu') {
            include __DIR__ . "/main/content/trangchu.php";
        } else if ($side == 'thanhtoan') {
            include __DIR__ . "/main/content/thanhtoan.php";
        }else if ($side == 'lichsugiaodich') {
            include __DIR__ . "/main/content/lichsugiaodich.php";
        }else if ($side == 'timkiem') {
            include __DIR__ . "/main/content/timkiem.php"; 
        }else if ($side == 'nhapOtp') {
        include __DIR__ . "/main/content/nhapOtp.php"; 
    }
    } else if (isset($_GET['nav'])) {
        switch ($_GET['nav']) {
            case 'hoso':
                    include __DIR__ . "/main/content/hoso.php";
                
                break;

            case 'Dangxuat':
                session_destroy();
                header('Location: ' . BASE_URL . '/index.php');
                exit();
                break;
        }
    } else {
        // Mặc định: nếu đã đăng nhập thì load trang chủ
        if (isset($_SESSION['id'])) {
            include __DIR__ . "/main/content/thanhtoan.php";
        }
    }
    ?>
</div>
