<?php
/**
 * User 관련 비즈니스 로직 처리 클래스
 */
namespace service;
include_once  '/var/www/html/lib/config.php';

use repository\UserRepository;
use repository\BoardRepository;
use repository\InfoRepository;
use repository\AttachmentRepository;
use repository\CommentRepository;
use log\PostLogger;
use dataset\User;

class UserService{

    /**
     * 사용자 - 공지 상세 조회
     * @param $board_id
     * @param $email
     * @return array
     */
    public function noticeDetailsByUser($board_id,$email){
        $boardRepository = new BoardRepository();
        $attachmentRepository = new AttachmentRepository();
        $commentRepository = new CommentRepository();

        $board = $boardRepository->getBoardById($board_id);
        $attachments = $attachmentRepository->getAttachmentsByBoardId($board_id);
        $comments = $commentRepository -> getCommentsByBoardId($board_id);

        // 글 상세 조회 로그
        $logger = new PostLogger();
        $title = $board->getTitle();
        $status = $board->getStatus();
        $logger->readPost($_SERVER['REQUEST_URI'], $email, $status, $title);

        return [
            'board' => $board,
            'attachments' => $attachments,
            'comments'=> $comments
        ];
    }

    /**
     * 사용자 - 글 상세 조회
     */
    public function boardDetailByUser($email, $board_id){
        $userRepository = new UserRepository();
        $boardRepository = new BoardRepository();
        $infoRepository = new InfoRepository();
        $commentRepository = new CommentRepository();
        $attachmentRepository = new AttachmentRepository();

        $board = $boardRepository -> getBoardById($board_id);
        $info = $infoRepository -> getLatestInfoByBoardId($board_id);
        $user = $userRepository->getUserById($info->getUserId());
        $comments = $commentRepository -> getCommentsByBoardId($board_id);
        $attachments = $attachmentRepository->getAttachmentsByBoardId($board_id);

        // 글 상세 조회 로그
        $logger = new PostLogger();
        $title = $board->getTitle();
        $status = $board->getStatus();
        $logger->readPost($_SERVER['REQUEST_URI'], $email, $status, $title);

        return [
            'board' => $board,
            'info' => $info,
            'user'=> $user,
            'comments' => $comments,
            'attachments' => $attachments
        ];
    }

    /**
     * 관리자 - 글 상세 조회
     */
    public function boardDetailByAdmin($email,$board_id){
        $boardRepository = new BoardRepository();
        $userRepository = new UserRepository();
        $commentRepository = new CommentRepository();
        $attachmentRepository = new AttachmentRepository();

        $board = $boardRepository -> getBoardByid($board_id);
        $user_id = $board->getUserId();
        $user = $userRepository -> getUserById($user_id);
        $comments = $commentRepository -> getCommentsByBoardId($board_id);
        $attachments = $attachmentRepository->getAttachmentsByBoardId($board_id);

        $logger = new PostLogger();
        $title = $board->getTitle();
        $status = $board->getStatus();
        $logger->readPost($_SERVER['REQUEST_URI'], $email, $status, $title);

        return [
            'board' => $board,
            'user'=> $user,
            'comments' => $comments,
            'attachments' => $attachments
        ];
    }
}

?>