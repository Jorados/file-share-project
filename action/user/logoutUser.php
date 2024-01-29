<?php
session_start();

include_once '/var/www/html/lib/config.php';

use util\Constant;
use log\UserLogger;


// 로그아웃 버튼 클릭 시 세션 제거 및 리다이렉션
if ($_SERVER["REQUEST_METHOD"] == Constant::METHOD_POST) {

    $logger = new UserLogger();
    // 로그아웃 로그
    $email = $_SESSION['email'];
    $logger->logout($_SERVER['REQUEST_URI'],$email);

    // 세션 파기
    session_unset();
    session_destroy();
}
?>