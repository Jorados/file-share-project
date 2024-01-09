<?php
session_start();
include '/var/www/html/database/DatabaseConnection.php';
include '/var/repository/boardRepository.php';
include '/var/repository/userRepository.php';
include '/var/www/html/mail/sendMail.php';

$dbConnection = new DatabaseConnection();
$pdo = $dbConnection->getConnection();

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $title = $_POST['title']; $content = $_POST['content']; $date = date('Y-m-d H:i:s'); $user_email = $_SESSION['email'];

        $userRepository = new UserRepository($pdo);
        $user_id = $userRepository->getUserIdByEmail($user_email);

        $boardRepository = new BoardRepository($pdo);
        $boardRepository->addBoard($title, $content, $date, $user_id);

        /*
         *  메일 기능
         *  php - send mail
         */
        $mailSender = new SendMail($pdo);

        $subject = '게시글이 작성되었습니다.';
        $message = $_SESSION['email'] . ' 님의 게시글이 작성되었습니다.';

        if ($mailSender->sendToAdmins($subject, $message)) {
            echo "메일이 성공적으로 전송되었습니다.";
        } else {
            echo "메일 전송에 실패했습니다.";
        }

        echo "게시글이 성공적으로 추가되었습니다.";
    }
} catch (PDOException $e) {
    error_log(E_ALL);
    ini_set("display_errors", 1);
    echo "Error: " . $e->getMessage();
}
?>