<?php
/**
 * 관리자,사용자 로그인 액션
 */
session_start();
include '/var/www/html/lib/config.php';

use service\UserService;
use util\Constant;

$userService = new UserService();

if ($_SERVER["REQUEST_METHOD"] == Constant::METHOD_POST) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $result = $userService->loginUser($email,$password);
    echo json_encode(['status'=>$result['status'], 'content'=>$result['content'], 'role'=>$result['role'] ?? null, 'available'=>$result['available'] ?? null]);
}
?>