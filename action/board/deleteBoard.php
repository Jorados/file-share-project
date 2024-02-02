<?php
/**
 * 관리자 글 삭제 액션
 */
session_start();
include '/var/www/html/lib/config.php';

use util\Constant;
use service\BoardService;

$boardService = new BoardService();

if ($_SERVER['REQUEST_METHOD'] === Constant::METHOD_POST) {
    $board_id = isset($_POST['board_id']) ? $_POST['board_id'] : null;

    $email = $_SESSION['email'];
    $result = $boardService->deleteBoard($board_id,$email);
    echo json_encode(['status' => $result['status'], 'content' => $result['content']]);
}
?>