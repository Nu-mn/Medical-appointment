<?php
session_start();

define('BASE_PATH', __DIR__);
define('BASE_URL', '../source');


header("Location: " . BASE_URL . "/views/index.php");
exit();

