<?php

namespace log;

class UserLogger extends BaseLogger {

    /**
     * 사용자 액션 -> 로그인, 로그아웃, 회원가입, 비밀번호 변경, 권한 변경
     */

    public function login($action, $email) {
        $this->logAction($action, "$email 님이 로그인 하셨습니다.");
    }

    public function logout($action, $email) {
        $this->logAction($action, "$email 님이 로그아웃 하셨습니다.");
    }

    public function createUser($action, $adminEmail, $email) {
        $this->logAction($action, "관리자 $adminEmail 님이  $email 계정을 생성 하셨습니다.");
    }

    public function changePassword($action, $userEmail) {
        $this->logAction($action, "$userEmail 님이 비밀번호를 변경 하셨습니다.");
    }

    public function changeAuthority($action, $adminEmail, $userEmail, $authority) {
        $message = ($authority == 0) ? "불가" : "허용";
        $this->logAction($action, "관리자 $adminEmail 님이  $userEmail 님의 글 생성 권한을 $message 상태로 변경하였습니다.");
    }


}