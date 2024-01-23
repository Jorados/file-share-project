<?php
/**
 *  관리자,사용자 비밀번호 변경 액션
 */

session_start();

include '/var/www/html/lib/config.php';

use repository\UserRepository;
use log\UserLogger;
use dataset\User;

$userRepository = new UserRepository();
$logger = new UserLogger();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'];

    // 비밀번호 유효성 검사
    if (!preg_match("/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$/", $password)) {
        echo json_encode(['status' => false, 'content' => '비밀번호는 영문자와 숫자를 모두 포함하고, 최소 8자 이상이어야 합니다.']);
    }
    else{
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $email = $_SESSION['email'];

        $user = new User(['email' => $email,'password'=>$hashed_password]);
        $userRepository->updateUserPassword($user);
        $userRepository->updateAvailableStatus($user);

        // 로그 남기기
        $logger->changePassword($_SERVER['REQUEST_URI'], $email);

        echo json_encode(['status' => true, 'content' => '성공적으로 비밀번호가 변경되었습니다. 다시 로그인 해주세요.']);
    }
}
?>