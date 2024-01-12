<?php
session_start();

include '/var/www/html/database/DatabaseConnection.php';
include '/var/www/html/repository/boardRepository.php';
include '/var/www/html/repository/attachmentRepository.php';
include '/var/access_logs/PostLogger.php';

$dbConnection = new DatabaseConnection();
$pdo = $dbConnection->getConnection();

$boardRepository = new BoardRepository($pdo);
$attachmentRepository = new AttachmentRepository($pdo);
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
            $stmt = $boardRepository->deleteBoardById($board_id);
            $stmt = $attachmentRepository->deleteAttachment($board_id);

            // 글 삭제 로그
            $email = $_SESSION['email'];
            $title = $board['title'];
            $status = $board['status'];
            $logger->deletePost($_SERVER['REQUEST_URI'], $email, $status, $title);

            echo json_encode(['status' => true, 'content' => '게시글이 삭제되었습니다.']);
        }
    }
}
?>