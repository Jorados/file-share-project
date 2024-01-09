<?php
session_start();

include '/var/www/html/database/DatabaseConnection.php';
include '/var/repository/boardRepository.php';
include '/var/www/html/mail/sendMail.php';

$dbConnection = new DatabaseConnection();
$pdo = $dbConnection->getConnection();

$boarRepository = new BoardRepository($pdo);
try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $title = $_POST['title'];
        $content = $_POST['content'];
        $date = date('Y-m-d H:i:s');

        $user_id = $_SESSION['user_id'];

        // 게시글 유형 설정 (normal 또는 notification)
        $postStatus = $_POST['status'];

        // 글 추가 작업
        $boarRepository->adminCreateBoard($title, $content, $date, $user_id, $postStatus);

        /*
         *  메일 기능
         *  php - send mail
         */
        $mailSender = new SendMail($pdo);

        $subject = '관리자 게시글이 작성되었습니다.';
        $message = '관리자 ' . $_SESSION['email'] . ' 님의 게시글이 작성되었습니다.';

        if ($mailSender->sendToAdmins($subject, $message)) {
            echo "메일이 성공적으로 전송되었습니다.";
        } else {
            echo "메일 전송에 실패했습니다.";
        }
        echo "게시글이 성공적으로 추가되었습니다.";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>