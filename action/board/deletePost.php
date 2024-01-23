<?php
/**
 * 관리자 글 삭제 액션
 */

session_start();

include '/var/www/html/lib/config.php';

use repository\BoardRepository;
use repository\AttachmentRepository;
use log\PostLogger;

$boardRepository = new BoardRepository();
$attachmentRepository = new AttachmentRepository();
$logger = new PostLogger();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $board_id = isset($_POST['board_id']) ? $_POST['board_id'] : null;
    if (!$board_id) {
        echo json_encode(['status' => false, 'content' => '게시글을 찾을 수 없습니다.']);
    }
    else{
        $board = $boardRepository->getBoardByid($board_id);
        if (!$board){
            echo json_encode(['status' => false, 'content' => '게시글을 찾을 수 없습니다.']);
        }
        else{
            // 삭제 로직
            $boardRepository->deleteBoardById($board_id);
            $attachmentRepository->deleteAttachment($board_id);

            // 글 삭제 로그
            $email = $_SESSION['email'];
            $title = $board->getTitle();
            $status = $board->getStatus();
            $logger->deletePost($_SERVER['REQUEST_URI'], $email, $status, $title);

            echo json_encode(['status' => true, 'content' => '게시글이 삭제되었습니다.']);
        }
    }
}
?>