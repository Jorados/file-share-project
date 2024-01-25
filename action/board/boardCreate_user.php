<?php
/**
 * 사용자 글 생성 액션
 */
session_start();
include '/var/www/html/lib/config.php';

use service\BoardService;

$boardService = new BoardService();

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $title = $_POST['title'];
        $content = $_POST['content'];
        $date = date('Y-m-d H:i:s');
        $email = $_SESSION['email'];

        $result = $boardService->boardCreateUser($title,$content,$date,$email);
        echo json_encode(['content'=>$result['content']]);
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>