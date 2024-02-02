<?php
session_start();
include_once '/var/www/html/lib/config.php';

use util\Util;

if (!isset($_SESSION['session_start_time'])) {
    $_SESSION['session_start_time'] = time();
}

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    Util::serverRedirect("/index.php");
    exit;
} elseif (isset($_SESSION['role'])) {
    $currentPath = $_SERVER['REQUEST_URI'];
    if ($_SESSION['role'] == 'user' && (in_array('admin', explode("/", $currentPath)))) {
        Util::serverRedirect("/pages/home.php");
        exit;
    }
} else {
    Util::serverRedirect("/index.php");
    exit;
}

if ($_SESSION['available'] == 0 && strpos($_SERVER['REQUEST_URI'], '/pages/changePassword.php') === false) {
    Util::serverRedirect("/pages/changePassword.php");
    exit;
}

?>