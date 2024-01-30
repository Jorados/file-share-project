<?php

/**
 * 댓글 로그
 */

namespace log;

class CommentLogger extends BaseLogger {

    /**
     * @param $action
     * @param $email
     * @param $board_title
     * 댓글 생성
     */
    public function createComment($action, $email , $board_title){
        $this->logAction($action,"{$email} 님이 제목 : '{$board_title}' 글에 댓글을 작성하였습니다.");
    }

}

?>