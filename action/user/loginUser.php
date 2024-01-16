<?php
session_start();
include '/var/www/html/lib/config.php';

use repository\UserRepository;
use log\UserLogger;

$userRepository = new UserRepository();
$logger = new UserLogger();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    try {
        $user = $userRepository->loginUser($email, $password);
        if ($user) {
            handleUser($user);
            handleLogin($logger,$user);
            exit;
        } else {
            echo json_encode(['status'=>false,'content'=>'유효하지 않은 이메일 또는 비밀번호입니다.']);
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}


function handleLogin($logger, $user) {
    if ($user['role'] == 'admin') {
        echo json_encode(['status'=>true, 'content'=> '로그인 성공!', 'role' => 1]);
        $logger->login($_SERVER['REQUEST_URI'], $user['email']);
    } else if ($user['role'] == 'user') {
        echo json_encode(['status'=>true, 'content'=> '로그인 성공!', 'role' => 0]);
        $logger->login($_SERVER['REQUEST_URI'], $user['email']);
    } else {
        echo json_encode(['status'=>false, 'content' => "알 수 없는 역할입니다."]);
    }
}


function handleUser($user){
    $_SESSION['loggedin'] = true;
    $_SESSION['email'] = $_POST['email'];
    $_SESSION['session_start_time'] = time(); // 세션 시작 시간 설정
    $_SESSION['available'] = $user['available'];
    $_SESSION['authority'] = $user['authority'];
    $_SESSION['role'] = $user['role'];
    $_SESSION['user_id'] = $user['user_id'];
}
?>