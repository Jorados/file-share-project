<?php
session_start();
include '/var/www/html/lib/config.php';

use database\DatabaseConnection;
use repository\BoardRepository;
use repository\CommentRepository;
use log\CommentLogger;


$dbConnection = new DatabaseConnection();
$pdo = $dbConnection->getConnection();
$boardRepository = new BoardRepository($pdo);
$commentRepository = new CommentRepository($pdo);
$logger = new CommentLogger();

if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    $board_id = $_POST['board_id'];
    $content = $_POST['content'];
    $user_id = $_SESSION['user_id']; // 로그인한 사용자의 ID를 사용

    if($content==null) {
        echo json_encode(['status' => false, 'content' => '댓글을 다시 작성해주세요.']);
    }
    else{
        $stmt = $commentRepository -> addComment($content, $board_id, $user_id);
        $board = $boardRepository -> getBoardByid($board_id);

        $email = $_SESSION['email'];
        $logger->createComment($_SERVER['REQUEST_URI'], $email, $board['title']);
        echo json_encode(['status' => true, 'content' => '댓글이 작성되었습니다.']);
    }
}
?>