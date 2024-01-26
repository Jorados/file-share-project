<?php
/**
 *  댓글 생성 액션
 */

session_start();
include '/var/www/html/lib/config.php';

use service\CommentService;
use util\Constant;

$commentService = new CommentService();

if ($_SERVER['REQUEST_METHOD'] === Constant::METHOD_POST){
    $board_id = $_POST['board_id'];
    $content = $_POST['content'];
    $user_id = $_SESSION['user_id']; // 로그인한 사용자의 ID를 사용
    $email = $_SESSION['email'];

    $result = $commentService->createComment($board_id, $content, $user_id, $email);
    echo json_encode(['status'=>$result['status'],'content'=>$result['content']]);
}
?>