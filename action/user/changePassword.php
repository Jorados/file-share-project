<?php
/**
 *  관리자,사용자 비밀번호 변경 액션
 */

session_start();

include '/var/www/html/lib/config.php';

use service\UserService;
use util\Constant;

if ($_SERVER['REQUEST_METHOD'] === Constant::METHOD_POST){
    $userService = new UserService();
    convertJsonAndExit(
        array_merge($userService->changePassword($_POST['password'], $_SESSION['email']),['error' => false])
    );
}
else convertJsonAndExit(['error' => true , 'content' => "userService->changePassword 에 문제생김"]);

function convertJsonAndExit($jsonArr){
    echo json_encode($jsonArr);
    exit();
}
?>



