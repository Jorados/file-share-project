<?php
session_start();
include '/var/www/html/lib/config.php';

use repository\BoardRepository;
use repository\CommentRepository;
use log\CommentLogger;
use dataset\Comment;

$boardRepository = new BoardRepository();
$commentRepository = new CommentRepository();
$logger = new CommentLogger();

if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    $board_id = $_POST['board_id'];
    $content = $_POST['content'];
    $user_id = $_SESSION['user_id']; // 로그인한 사용자의 ID를 사용

    if($content==null) {
        echo json_encode(['status' => false, 'content' => '댓글을 다시 작성해주세요.']);
    }
    else{
        $commentRepository -> addComment(new Comment(['content'=>$content,'board_id'=>$board_id,'user_id'=>$user_id]));
        $board = $boardRepository -> getBoardByid($board_id);

        $email = $_SESSION['email'];
        $logger->createComment($_SERVER['REQUEST_URI'], $email, $board->getTitle());
        echo json_encode(['status' => true, 'content' => '댓글이 작성되었습니다.']);
    }
}
?>