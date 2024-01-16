<?php
namespace log;

class CommentLogger {

    // 사용 예:
//    include '/var/access_logs/CommentLogger.php';
//    $logger = new CommentLogger();
//    $logger->logAction($_SERVER['REQUEST_URI']);

    private $log_filename;

    public function __construct($log_directory = '/var/www/html/file/logs') {
        // 로그 디렉터리가 없다면 생성
        if (!is_dir($log_directory)) {
            mkdir($log_directory, 0444, true);  // 0444 -> 모든 사용자에게 읽기 권한 부여.
        }
        // 오늘 날짜를 기반으로 로그 파일 이름 생성
        $this->log_filename = $log_directory . '/' . date('Y-m-d') . '.log';
    }

    /*
     *  댓글 액션 -> 댓글 생성
     *  등등
     */

    // 관리자 댓글 생성
    public function createComment($action, $email , $board_title){
        $timestamp = date('Y-m-d H:i:s');
        $ip_address = $_SERVER['REMOTE_ADDR'];

        $log_entry = "$timestamp - $ip_address - $action\n";
        file_put_contents($this->log_filename, $log_entry, FILE_APPEND | LOCK_EX);

        $log_entry2 = "--> $email 님이 제목 : '$board_title' 글에 댓글을 작성하였습니다. \n\n";
        file_put_contents($this->log_filename, $log_entry2, FILE_APPEND | LOCK_EX);
    }

}


?>