<?php
session_start();

require_once 'layout/header.php';
require_once 'layout/left_menu.php';

$action = isset($_GET['action']) ? $_GET['action'] : 'main';

$view_file = "views/" . $action . ".php";

if (file_exists($view_file)) {
    require_once $view_file;
} else {
    require_once 'views/main.php';
}

require_once 'layout/footer.php';
?>