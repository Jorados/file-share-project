<?php
include '/var/www/html/lib/config.php';

use database\DatabaseConnection;
use repository\UserRepository;

$dbConnection = new DatabaseConnection();
$pdo = $dbConnection->getConnection();

$userRepository = new UserRepository($pdo);
// 수정 버튼 클릭
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = isset($_POST['user_id']) ? $_POST['user_id'] : null;
    if (!$user_id) {
        echo json_encode(['status' => false, 'content' => '회원을 찾을 수 없습니다.']);
    }

    $email = $_POST['email'];
    $username = $_POST['username'];
    $phone = $_POST['phone'];
    $password = $_POST['password'];

    $message = ""; // 초기 메시지 설정

    // 이메일 유효성 검사
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "유효한 이메일 형식이 아닙니다. 올바른 이메일 주소를 입력해주세요.";
        echo json_encode(['status' => false, 'content' => $message]);
    }
    else if (empty($username) || empty($phone)) {
        // username과 phone이 둘 다 공백인지 확인
        $message = "이름과 전화번호는 공백일 수 없습니다.";
        echo json_encode(['status' => false, 'content' => $message]);
    }
    else if (!preg_match("/^\d{11}$/", $phone)) {
        // phone이 11자리의 숫자인지 확인
        $message = "전화번호는 11자리의 숫자여야 합니다.";
        echo json_encode(['status' => false, 'content' => $message]);
    }
    else if (empty($password)) {
        // 비밀번호 입력 여부 확인
        $message = "비밀번호를 입력해주세요.";
        echo json_encode(['status' => false, 'content' => $message]);
    }
    else if (!preg_match("/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$/", $password)) {
        // 비밀번호 유효성 검사 (영어와 숫자, 최소 8자)
        $message = "비밀번호는 영어와 숫자를 포함하여 8자 이상이어야 합니다.";
        echo json_encode(['status' => false, 'content' => $message]);
    }
    else {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT); // 비밀번호 암호화
        $updateStmt = $userRepository->updateUserDetails($user_id, $email, $hashedPassword, $username, $phone);
        echo json_encode(['status' => true, 'content' => '회원 정보가 성공적으로 업데이트되었습니다.']);
    }
}
?>
