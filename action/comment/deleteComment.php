<?php
/**
 * 댓글 삭제 액션
 */

session_start();
include '/var/www/html/lib/config.php';

use service\CommentService;
use util\Constant;

$commentService = new CommentService();

if ($_SERVER['REQUEST_METHOD'] === Constant::METHOD_POST) {
    $comment_id = isset($_POST['comment_id']) ? $_POST['comment_id'] : null;

    $result = $commentService->deleteComment($comment_id);
    echo json_encode(['status' => $result['status'], 'content' => $result['content']]);
} else {
    echo json_encode(['status' => false, 'content' => '올바른 요청이 아닙니다.']);
}
?>
