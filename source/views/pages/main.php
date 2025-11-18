<div class="main">
    <?php 
      // NAVIGATION
    include __DIR__ . '/main/nav.php';
    ?>

    <?php 
    if (isset($_GET['nav'])) {
        $nav = $_GET['nav'];

        if ($nav == 'home') {
            include __DIR__ . "/main/content/trangchu.php";

        } elseif ($nav == 'appointment') {
            include __DIR__ . "/main/content/appointment.php";

        } elseif ($nav == 'invoice') {
            include __DIR__ . "/main/content/invoice-history.php";

        } elseif ($nav == 'doctors') {
            include __DIR__ . "/main/content/doctors.php";

        } elseif ($nav == 'patients') {
            include __DIR__ . "/main/content/patients.php";

        } elseif ($nav == 'contact') {
            include __DIR__ . "/main/content/contact.php";
        } elseif ($nav == 'hoso') {
            include __DIR__ . "/main/content/hoso.php";
        } else {
            include __DIR__ . "/main/content/404.php";  // phòng lỗi
        }
    } else {
        include __DIR__ . "/main/content/trangchu.php";  // mặc định
    }

    ?>
</div>
