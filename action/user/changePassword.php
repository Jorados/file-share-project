<?php
/**
 *  관리자,사용자 비밀번호 변경 액션
 */

session_start();

include '/var/www/html/lib/config.php';

use service\UserService;

$userService = new UserService();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'];
    $email = $_SESSION['email'];

    $result = $userService->changePassword($password, $email);
    echo json_encode(['status'=>$result['status'], 'content'=>$result['content']]);
}
?>