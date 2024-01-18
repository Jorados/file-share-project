<?php
namespace log;

class CommentLogger {

    public function __construct($log_directory = '/var/www/html/file/logs') {
        // 로그 디렉터리가 없다면 생성
        if (!is_dir($log_directory)) {
            mkdir($log_directory, 0444, true);  // 0444 -> 모든 사용자에게 읽기 권한 부여.
        }
        // 오늘 날짜를 기반으로 로그 파일 이름 생성
        $this->log_directory = $log_directory . '/' . date('Y') . '/' . date('m');
        if (!is_dir($this->log_directory)) {
            mkdir($this->log_directory, 0755, true);  // 년도와 월에 맞게 디렉토리 생성
        }
    }

    private function getLogFilename() {
        // 오늘 날짜를 기반으로 로그 파일 이름 생성
        return $this->log_directory . '/' . date('Y-m-d') . '.log';
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
        file_put_contents($this->getLogFilename(), $log_entry, FILE_APPEND | LOCK_EX);

        $log_entry2 = "--> $email 님이 제목 : '$board_title' 글에 댓글을 작성하였습니다. \n\n";
        file_put_contents($this->getLogFilename(), $log_entry2, FILE_APPEND | LOCK_EX);
    }

}


?>