<?php
session_start();
include '/var/www/html/lib/config.php';

use repository\UserRepository;
use repository\BoardRepository;
use mail\SendMail;
use log\PostLogger;

$logger = new PostLogger();
$userRepository = new UserRepository();
$boardRepository = new BoardRepository();
$mailSender = new SendMail();

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $title = $_POST['title'];
        $content = $_POST['content'];
        $date = date('Y-m-d H:i:s');
        $user_email = $_SESSION['email'];

        $user = $userRepository->getUserIdByEmail($user_email);
        $boardRepository->addBoard($title, $content, $date, $user->getUserId());

        /*
         *  메일 기능
         *  php - send mail
         */

        $subject = 'post has been written.';
        $message = $user_email . ' post has been written.';

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
    echo "Error: " . $e->getMessage();
}
?>