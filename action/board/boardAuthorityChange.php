<?php
/**
 * 관리자 -> 사용자 글 열람 권한 변경 - 액션
 */

session_start();
include '/var/www/html/lib/config.php';

use service\BoardService;

$boardService = new BoardService();

// 글 열람권한 변경 관련 --> 해당 작성자에게 메일도 전송 해야함.
if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    $newPermission = $_POST['change_permission'];
    $board_id = $_POST['board_id'];
    $reason_content = $_POST['reason_content']; // 사용자로부터의 입력
    $user_id = $_SESSION['user_id'];

    $result = $boardService->boardAuthorityChange($newPermission, $board_id, $reason_content, $user_id);
    echo json_encode(['status' => $result['status'], 'content' => $result['content']]);
}
?>