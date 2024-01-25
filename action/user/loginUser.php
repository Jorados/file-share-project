<?php
/**
 * 관리자,사용자 로그인 액션
 */
error_log(E_ALL);
ini_set("display_errors", 1);

session_start();
include '/var/www/html/lib/config.php';

use repository\UserRepository;
use log\UserLogger;
use dataset\User;

$userRepository = new UserRepository();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $userInfo = new User(['email'=>$email ,'password'=>$password]);
    $user = $userRepository->loginUser($userInfo);
    if ($user) {
        handleUser($user);
        handleLogin($user);
        exit;
    } else {
        echo json_encode(['status'=>false,'content'=>'유효하지 않은 이메일 또는 비밀번호입니다.']);
    }
}


function handleLogin($user) {
    $logger = new UserLogger();
    if ($user->getRole() == 'admin') {
        echo json_encode(['status'=>true, 'content'=> '로그인 성공!', 'role' => 1]);
        $logger->login($_SERVER['REQUEST_URI'], $user->getEmail());
    } else if ($user->getRole() == 'user') {
        echo json_encode(['status'=>true, 'content'=> '로그인 성공!', 'role' => 0]);
        $logger->login($_SERVER['REQUEST_URI'], $user->getEmail());
    } else {
        echo json_encode(['status'=>false, 'content' => "알 수 없는 역할입니다."]);
    }
}


function handleUser($user){
    $_SESSION['loggedin'] = true;
    $_SESSION['email'] = $user->getEmail();
    $_SESSION['session_start_time'] = time(); // 세션 시작 시간 설정
    $_SESSION['available'] = $user->getAvailable();
    $_SESSION['authority'] = $user->getAuthority();
    $_SESSION['role'] = $user->getRole();
    $_SESSION['user_id'] = $user->getUserId();
}
?>