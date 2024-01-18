<?php

namespace log;

class PostLogger {

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
     *  게시글 액션 -> 글 생성, 글(공지,일반) 상세 조회, 글 삭제
     */

    // 관리자 및 사용자의 글 생성
    public function createPost($action, $email, $status){
        $timestamp = date('Y-m-d H:i:s');
        $ip_address = $_SERVER['REMOTE_ADDR'];

        if($status=='notification') $status = "공지";
        else $status = "일반";

        $log_entry = "$timestamp - $ip_address - $action\n";
        file_put_contents($this->getLogFilename(), $log_entry, FILE_APPEND | LOCK_EX);

        $log_entry2 = "--> $email 님이 $status 글을 작성하셨습니다.\n\n";
        file_put_contents($this->getLogFilename(), $log_entry2, FILE_APPEND | LOCK_EX);
    }

    // 관리자,사용자의 글(일반,공지) 상세 조회
    public function readPost($action, $email, $status, $title){
        $timestamp = date('Y-m-d H:i:s');
        $ip_address = $_SERVER['REMOTE_ADDR'];

        if($status=='notification') $status = "공지";
        else $status = "일반";

        $log_entry = "$timestamp - $ip_address - $action\n";
        file_put_contents($this->getLogFilename(), $log_entry, FILE_APPEND | LOCK_EX);

        $log_entry2 = "--> $email 님이 '$title' 제목의 $status 글을 상세 조회하셨습니다.\n\n";
        file_put_contents($this->getLogFilename(), $log_entry2, FILE_APPEND | LOCK_EX);
    }

    // 관리자 글 삭제
    public function deletePost($action, $email, $status, $title){
        $timestamp = date('Y-m-d H:i:s');
        $ip_address = $_SERVER['REMOTE_ADDR'];

        if($status=='notification') $status = "공지";
        else $status = "일반";

        $log_entry = "$timestamp - $ip_address - $action\n";
        file_put_contents($this->getLogFilename(), $log_entry, FILE_APPEND | LOCK_EX);

        $log_entry2 = "--> $email 님이 '$title' 제목의 $status 글을 삭제하셨습니다.\n\n";
        file_put_contents($this->getLogFilename(), $log_entry2, FILE_APPEND | LOCK_EX);
    }

    // 관리자 글 열람 권한 변경
    public function openAuthority($action, $email, $newPermission, $title){
        $timestamp = date('Y-m-d H:i:s');
        $ip_address = $_SERVER['REMOTE_ADDR'];

        if($newPermission==0) $newPermission = "열람 불가";
        else $newPermission = "열람 가능";

        $log_entry = "$timestamp - $ip_address - $action\n";
        file_put_contents($this->getLogFilename(), $log_entry, FILE_APPEND | LOCK_EX);

        $log_entry2 = "--> 관리자 $email 님이 제목 : '$title' 글을 $newPermission 상태로 변경하였습니다. \n\n";
        file_put_contents($this->getLogFilename(), $log_entry2, FILE_APPEND | LOCK_EX);
    }

    // 파일 다운로드
    public function downloadFile($action, $email, $fileName){
        $timestamp = date('Y-m-d H:i:s');
        $ip_address = $_SERVER['REMOTE_ADDR'];

        $log_entry = "$timestamp - $ip_address - $action\n";
        file_put_contents($this->getLogFilename(), $log_entry, FILE_APPEND | LOCK_EX);

        $log_entry2 = "--> $email 님이 '$fileName' 파일을 다운로드 하였습니다. \n\n";
        file_put_contents($this->getLogFilename(), $log_entry2, FILE_APPEND | LOCK_EX);
    }

}


?>