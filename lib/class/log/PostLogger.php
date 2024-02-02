<?php
/**
 * 글 로그
 */

namespace log;

class PostLogger extends BaseLogger {

    /**
     * 게시글 액션 -> 글 생성, 글(공지,일반) 상세 조회, 글 삭제
     */

    // 관리자 및 사용자의 글 생성
    public function createPost($action, $email, $status=null){
        $status = ($status=='notification') ? "공지" : "일반";
        $this->logAction($action, "{$email} 님이 {$status} 글을 작성하셨습니다.");
    }

    // 관리자,사용자의 글(일반,공지) 상세 조회
    public function readPost($action, $email, $status, $title){
        $status = ($status=='notification') ? "공지" : "일반";
        $this->logAction($action, "{$email} 님이 '{$title}' 제목의 {$status} 글을 상세 조회하셨습니다.");
    }

    // 관리자 글 삭제
    public function deletePost($action, $email, $status, $title){
        $status = ($status=='notification') ? "공지" : "일반";
        $this->logAction($action, "{$email} 님이 '{$title}' 제목의 {$status} 글을 삭제하셨습니다.");
    }

    // 관리자 글 열람 권한 변경
    public function openAuthority($action, $email, $newPermission, $title){
        $newPermission = ($newPermission==0) ? "열람 불가" : "열람 가능" ;
        $this->logAction($action, "관리자 {$email} 님이 제목 : '{$title}' 글을 {$newPermission} 상태로 변경하였습니다.");
    }

    // 파일 다운로드
    public function downloadFile($action, $email, $fileName){
        $this->logAction($action, "{$email} 님이 '{$fileName}' 파일을 다운로드 하였습니다.");
    }

    // 크론탭에 의한 일정시간이 지나서 열람 불가 상태로 변경
    public function changePermission($action, $title, $board_id){
        $this->logAction($action, "boardId : {$board_id} 번, {$title} 제목의 게시글이 열람권한 허용 상태에서 일정 시간 후 열람 불가 상태로 변경되었습니다");
    }
}


?>