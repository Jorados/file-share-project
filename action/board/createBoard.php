<?php
/**
 * 관리자 글 생성 액션
 */
session_start();

include '/var/www/html/lib/config.php';

use service\BoardService;
use util\Constant;

$boardService = new BoardService();

try {
    if ($_SERVER['REQUEST_METHOD'] === Constant::METHOD_POST) {
        $title = $_POST['title'];
        $content = $_POST['content'];
        $date = date('Y-m-d H:i:s');
        $email = $_SESSION['email'];

        if($_SESSION['role']=='user'){
            $result = $boardService->boardCreateUser($title,$content,$date,$email);
        }
        else if($_SESSION['role']=='admin'){
            $user_id = $_SESSION['user_id'];
            $postStatus = $_POST['status'];
            $result = $boardService->boardCreateAdmin($title, $content, $date, $user_id, $postStatus, $email);
        }

        echo json_encode(['content'=>$result['content']]);
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>