<?php
session_start();
include '/var/www/html/lib/config.php';

use repository\BoardRepository;
use repository\InfoRepository;
use mail\SendMail;
use log\PostLogger;

$boardRepository = new BoardRepository();
$infoRepository = new InfoRepository();
$mailSender = new SendMail();
$logger = new PostLogger();

// 글 열람권한 변경 관련 --> 해당 작성자에게 메일도 전송 해야함.
if ($_SERVER['REQUEST_METHOD'] === 'POST'){
    $newPermission = $_POST['change_permission'];
    $board_id = $_POST['board_id'];
    $reason_content = $_POST['reason_content']; // 사용자로부터의 입력
    $user_id = $_SESSION['user_id'];

    if($reason_content==null) {
        echo json_encode(['status' => false, 'content' => "다시 시도 해주시기 바랍니다."]);
    }
    else{
        // 게시글 권한 업데이트
        $boardRepository->updateBoardPermission($board_id, $newPermission);
        // info 테이블에 정보 삽입
        $infoRepository->addInfo($reason_content, $user_id, $board_id);

        // 메일 전송 구현 로직 , 글 주인한테 메일 쏴야함
        // 해당 board_id가 가지고 있는 user_id를 가지는 user의 email정보를 알아야한다.
        // 그리고 그 email 정보를 이용해서 메일 전송.
        $user = $boardRepository->getBoardUserEmail($board_id);
        $subject = 'Post permission status changed.';
        $message = 'Your post status has been changed by Administrator ' . $_SESSION['email'];

        $mailSender->sendToUser($subject, $message,$user->getEmail());
//        if () {
//            echo "메일이 성공적으로 전송되었습니다.";
//        } else {
//            echo "메일 전송에 실패했습니다.";
//        }

        // 로그 작성
        $board = $boardRepository->getBoardById($board_id);
        $title = $board->getTitle();
        $email = $_SESSION['email'];
        $logger->openAuthority($_SERVER['REQUEST_URI'], $email ,$newPermission, $title);

        echo json_encode(['status' => true, 'content' => '열람권한이 변경되었습니다.']);
    }
}
?>