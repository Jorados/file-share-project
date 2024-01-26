<?php
/**
 *  관리자 -> 사용자 계정 생성 액션
 */

session_start();
include '/var/www/html/lib/config.php';

use service\UserService;
use util\Constant;

$userService = new UserService();

if ($_SERVER['REQUEST_METHOD'] === Constant::METHOD_POST) {
    $email_user = $_POST['email'];
    $email_admin = $_SESSION['email'];
    $username = $_POST['username'];
    $phone = $_POST['phone'];
    $password = $_POST['password'];

    $result = $userService->createUser($email_user, $email_admin, $username, $phone, $password);
    echo json_encode(['status'=>$result['status'],'content'=>$result['content']]);
}
?>
