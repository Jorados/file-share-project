<?php
session_start();
include '/var/www/html/lib/config.php';

use repository\BoardRepository;
use mail\SendMail;
use log\PostLogger;

$logger = new PostLogger();
try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $title = $_POST['title']; $content = $_POST['content']; $date = date('Y-m-d H:i:s'); $user_email = $_SESSION['email'];

        $userRepository = new UserRepository();
        $user_id = $userRepository->getUserIdByEmail($user_email);

        $boardRepository = new BoardRepository();
        $boardRepository->addBoard($title, $content, $date, $user_id);

        /*
         *  메일 기능
         *  php - send mail
         */
        $mailSender = new SendMail();

        $subject = 'post has been written.';
        $message = $_SESSION['email'] . ' post has been written.';

        if ($mailSender->sendToAdmins($subject, $message)) {
            echo "메일이 성공적으로 전송되었습니다.";
        } else {
            echo "메일 전송에 실패했습니다.";
        }

        // 로그 작성
        $email = $_SESSION['email'];
        $status = 'normal';
        $logger->createPost($_SERVER['REQUEST_URI'], $email, $status);

        echo "게시글이 성공적으로 추가되었습니다.";
    }
} catch (PDOException $e) {
    error_log(E_ALL);
    ini_set("display_errors", 1);
    echo "Error: " . $e->getMessage();
}
?>