<?php
session_start();
include '/var/www/html/lib/config.php';

use database\DatabaseConnection;
use log\UserLogger;

$pdo = DatabaseConnection::getInstance()->getConnection();
$logger = new UserLogger();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $username = $_POST['username'];
    $phone = $_POST['phone'];
    $password = $_POST['password'];

    // 이메일 유효성 검사
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "유효한 이메일 형식이 아닙니다. 올바른 이메일 주소를 입력해주세요.";
        echo json_encode(['status' => false, 'content' => $message]);
    } else {
        // 공백 여부 검사
        if (empty($username) || empty($phone)) {
            $message = "이름과 전화번호는 공백일 수 없습니다.";
            echo json_encode(['status' => false, 'content' => $message]);
        } else {
            // 전화번호 숫자 및 길이 검사
            if (!preg_match("/^\d{11}$/", $phone)) {
                $message = "전화번호는 11자리의 숫자여야 합니다.";
                echo json_encode(['status' => false, 'content' => $message]);
            } else {
                // 비밀번호 유효성 검사
                if (!preg_match("/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$/", $password)) {
                    $message = "비밀번호는 영어와 숫자를 모두 포함하고, 최소 8자 이상이어야 합니다.";
                    echo json_encode(['status' => false, 'content' => $message]);
                } else {
                    // 비밀번호 해싱
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                    // 회원 가입
                    $query = "INSERT INTO user (email, password, username, phone) VALUES (:email, :password, :username, :phone)";
                    $stmt = $pdo->prepare($query);
                    $stmt->bindParam(':email', $email, \PDO::PARAM_STR);
                    $stmt->bindParam(':password', $hashed_password, \PDO::PARAM_STR);
                    $stmt->bindParam(':username', $username, \PDO::PARAM_STR);
                    $stmt->bindParam(':phone', $phone, \PDO::PARAM_STR);
                    $stmt->execute();

                    $message = "회원 가입이 성공적으로 완료되었습니다.";

                    $adminEmail = $_SESSION['email'];
                    $logger->createUser($_SERVER['REQUEST_URI'], $adminEmail, $email);

                    echo json_encode(['status' => true, 'content' => $message]);
                }
            }
        }
    }
}
?>