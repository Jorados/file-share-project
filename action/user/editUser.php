<?php
/**
 * 관리자 -> 사용자 계정 수정 액션
 */
include '/var/www/html/lib/config.php';

use service\UserService;
use util\Constant;

$userService = new UserService();

// 수정 버튼 클릭
if ($_SERVER['REQUEST_METHOD'] === Constant::METHOD_POST) {
    $user_id = isset($_POST['user_id']) ? $_POST['user_id'] : null;
    $email = $_POST['email'];
    $username = $_POST['username'];
    $phone = $_POST['phone'];
    $password = $_POST['password'];

    $result = $userService->editUser($user_id, $email, $username, $phone, $password);
    echo json_encode(['status'=>$result['status'], 'content'=>$result['content']]);
}
