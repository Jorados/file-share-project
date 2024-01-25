<?php
/**
 * Comment 관련 비즈니스 로직 처리 클래스
 */
namespace service;
include_once  '/var/www/html/lib/config.php';

use repository\CommentRepository;
use repository\BoardRepository;
use log\CommentLogger;
use dataset\Comment;

class CommentService{

    /**
     * 댓글 작성 service
     * @param int $board_id
     * @param String $content
     * @param int $user_id
     * @param String $email
     * @return array
     */
    public function createComment($board_id, $content, $user_id, $email){
        $commentRepository = new CommentRepository();
        $boardRepository = new BoardRepository();
        $logger = new CommentLogger();

        $result = [];
        if($content==null) {
            $result['status'] = false;
            $result['content'] = "댓글을 다시 작성해주세요.";
        }
        else{
            $commentRepository -> addComment(new Comment(['content'=>$content,'board_id'=>$board_id,'user_id'=>$user_id]));
            $board = $boardRepository -> getBoardByid($board_id);

            $logger->createComment($_SERVER['REQUEST_URI'], $email, $board->getTitle());
            $result['status'] = true;
            $result['content'] = "댓글이 작성되었습니다.";
        }
        return $result;
    }
}

?>