<?php
/**
 * 유저 로그
 */

namespace log;

class UserLogger {

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

    /**
     * 사용자 액션 -> 로그인, 로그아웃, 회원가입, 비밀번호 변경, 권한 변경
     */

    // 사용자 로그인 (admin,user)
    public function login($action, $email){
        $timestamp = date('Y-m-d H:i:s');
        $ip_address = $_SERVER['REMOTE_ADDR'];
        $log_entry = "$timestamp - $ip_address - $action\n";
        file_put_contents($this->getLogFilename(), $log_entry, FILE_APPEND | LOCK_EX);

        $log_entry2 = "--> $email 님이 로그인 하셨습니다.\n\n";
        file_put_contents($this->getLogFilename(), $log_entry2, FILE_APPEND | LOCK_EX);
    }

    // 사용자 로그아웃 (admin,user)
    public function logout($action, $email){
        $timestamp = date('Y-m-d H:i:s');
        $ip_address = $_SERVER['REMOTE_ADDR'];
        $log_entry = "$timestamp - $ip_address - $action\n";
        file_put_contents($this->getLogFilename(), $log_entry, FILE_APPEND | LOCK_EX);

        $log_entry2 = "--> $email 님이 로그아웃 하셨습니다.\n\n";
        file_put_contents($this->getLogFilename(), $log_entry2, FILE_APPEND | LOCK_EX);
    }

    // 사용자 생성(회원가입)
    public function createUser($action, $adminEmail, $email){
        $timestamp = date('Y-m-d H:i:s');
        $ip_address = $_SERVER['REMOTE_ADDR'];
        $log_entry = "$timestamp - $ip_address - $action\n ";
        file_put_contents($this->getLogFilename(), $log_entry, FILE_APPEND | LOCK_EX);

        $log_entry2 = "--> 관리자 $adminEmail 님이  $email 계정을 생성 하셨습니다.\n\n";
        file_put_contents($this->getLogFilename(), $log_entry2, FILE_APPEND | LOCK_EX);
    }

    // 사용자 비밀번호 변경
    public function changePassword($action, $userEmail){
        $timestamp = date('Y-m-d H:i:s');
        $ip_address = $_SERVER['REMOTE_ADDR'];
        $log_entry = "$timestamp - $ip_address - $action\n";
        file_put_contents($this->getLogFilename(), $log_entry, FILE_APPEND | LOCK_EX);

        $log_entry2 = "--> $userEmail 님이 비밀번호를 변경 하셨습니다.\n\n";
        file_put_contents($this->getLogFilename(), $log_entry2, FILE_APPEND | LOCK_EX);
    }

    // 관리자 -> 사용자 권한 변경
    public function changeAuthority($action, $adminEmail, $userEmail, $authority){
        $timestamp = date('Y-m-d H:i:s');
        $ip_address = $_SERVER['REMOTE_ADDR'];

        $message = "";
        if($authority==0) $message = "불가";
        else if($authority==1) $message = "허용";

        $log_entry = "$timestamp - $ip_address - $action\n";
        file_put_contents($this->getLogFilename(), $log_entry, FILE_APPEND | LOCK_EX);

        $log_entry2 = "--> 관리자 $adminEmail 님이  $userEmail 님의 글 생성 권한을 $message 상태로 변경하였습니다.\n\n";
        file_put_contents($this->getLogFilename(), $log_entry2, FILE_APPEND | LOCK_EX);
    }

}


?>