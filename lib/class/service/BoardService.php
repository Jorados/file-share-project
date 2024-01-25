<?php
/**
 * Board 관련 비즈니스 로직 처리 클래스
 */
namespace service;

use repository\AttachmentRepository;
use repository\BoardRepository;
use repository\UserRepository;
use repository\InfoRepository;

use dataset\Board;
use dataset\Info;
use dataset\User;

use mail\SendMail;
use log\PostLogger;

class BoardService{

    /**
     * 모든 글 조회 또는 검색을 활용한 글 조회
     * 조회되는 글 개수와 글 리턴
     * @param int $items_per_page
     * @param int $order
     * @param int $offset
     * @param int|null $permission
     * @param String|null $searchType
     * @param String|null $searchQuery
     * @param int|null $userId
     * @param String $status
     * @return array
     */
    public function getBoardByPage($items_per_page, $order, $offset, $permission = null, $searchType = null, $searchQuery = null, $user_id = null, $status) {
        $boardRepository = new BoardRepository();

        if($permission=='-권한-') $permission=null;
        if($searchType=='-선택-') $searchType=null;
        $total_items = $boardRepository->getTotalBoardCount($permission, $searchType, $searchQuery, $user_id, $status);
        $boards = $boardRepository->getBoardsByPage($offset, $items_per_page, $order, $permission, $searchType, $searchQuery, $user_id, $status);

        $total_pages = ceil($total_items / $items_per_page);

        return [
            'total_pages' => $total_pages,
            'boards' => $boards,
        ];
    }

    /**
     * 게시글 삭제
     * @param int|null $board_id
     * @param String $email
     * @return array
     */
    public function deleteBoard($board_id=null, $email): array{
        $boardRepository = new BoardRepository();
        $attachmentRepository = new AttachmentRepository();
        $logger = new PostLogger();

        if (!$board_id) {
            $flag = false;
            $content = '게시글을 찾을 수 없습니다.';
        }
        else{
            $board = $boardRepository->getBoardByid($board_id);
            if (!$board){
                $flag = false;
                $content = '게시글을 찾을 수 없습니다.';
            }
            else{
                // 삭제 로직
                $boardRepository->deleteBoardById($board_id);
                $attachmentRepository->deleteAttachment($board_id);

                // 글 삭제 로그
                $title = $board->getTitle();
                $status = $board->getStatus();
                $logger->deletePost($_SERVER['REQUEST_URI'], $email, $status, $title);

                $flag = true;
                $content = '게시글이 삭제되었습니다.';
            }
        }

        return [
            'status' => $flag,
            'content' => $content,
        ];
    }

    /**
     * 게시글 열람권한 변경
     * @param int $newPermission
     * @param int $board_id
     * @param String|null $reason_content
     * @param int $user_id
     * @return array
     */
    public function boardAuthorityChange($newPermission, $board_id, $reason_content=null, $user_id){
        $boardRepository = new BoardRepository();
        $infoRepository = new InfoRepository();
        $mailSender = new SendMail();
        $logger = new PostLogger();

        $result = [];
        if($reason_content==null) {
            $result['status'] = false;
            $result['content'] = "다시 시도 해주시기 바랍니다.";
        }
        else{
            // 게시글 권한 업데이트
            $boardRepository->updateBoardPermission(new Board(['board_id'=>$board_id,'openclose'=>$newPermission]));
            // info 테이블에 정보 삽입
            $infoRepository->addInfo(new Info(['reason_content'=>$reason_content,'user_id'=>$user_id,'board_id'=>$board_id]));

            // 메일 전송 구현 로직 , 글 주인한테 메일 쏴야함
            // 해당 board_id가 가지고 있는 user_id를 가지는 user의 email정보를 알아야한다.
            // 그리고 그 email 정보를 이용해서 메일 전송.
            $user = $boardRepository->getBoardUserEmail($board_id);
            $subject = 'Post permission status changed.';
            $message = 'Your post status has been changed by Administrator ' . $_SESSION['email'];

            // 메일 전송
            $mailSender->sendToUser($subject, $message,$user->getEmail());

            // 로그 작성
            $board = $boardRepository->getBoardById($board_id);
            $title = $board->getTitle();
            $email = $_SESSION['email'];
            $logger->openAuthority($_SERVER['REQUEST_URI'], $email ,$newPermission, $title);

            $result['status'] = true;
            $result['content'] = '열람권한이 변경되었습니다.';
        }

        return $result;
    }

    /**
     * 관리자 -> 게시글 생성
     * @param String $title
     * @param String $content
     * @param String $date
     * @param int $user_id
     * @param String $postStatus
     * @return array
     */
    public function boardCreateAdmin($title,$content,$date,$user_id,$postStatus,$email){
        $boarRepository = new BoardRepository();
        $mailSender = new SendMail();
        $logger = new PostLogger();

        $result = [];

        // 글 추가 작업
        $board = new Board(['title'=>$title,'content'=>$content,'date'=>$date,'user_id'=>$user_id,'status'=>$postStatus]);
        $boarRepository->adminCreateBoard($board);

        /*
         *  메일 기능
         *  php - send mail
         */
        $subject = '관리자 게시글이 작성되었습니다.';
        $message = '관리자 ' . $_SESSION['email'] . ' 님의 게시글이 작성되었습니다.';

        if ($mailSender->sendToAdmins($subject, $message)) {
            echo "메일이 성공적으로 전송되었습니다.";
        } else {
            echo "메일 전송에 실패했습니다.";
        }

        // 로그 작성
        $logger->createPost($_SERVER['REQUEST_URI'], $email, $postStatus);
        $result['content'] = "게시글이 성공적으로 추가되었습니다.";

        return $result;
    }

    /**
     * 사용자 글 작성
     * @param String $title
     * @param String $content
     * @param String $date
     * @param String $email
     * @return array
     */
    public function boardCreateUser($title,$content,$date,$email){
        $boardRepository = new BoardRepository();
        $userRepository = new UserRepository();
        $mailSender = new SendMail();
        $logger = new PostLogger();

        $result = [];
        $user = $userRepository->getUserIdByEmail(new User(['email'=>$email]));
        $board = new Board(['title'=>$title,'content'=>$content,'date'=>$date,'user_id'=>$user->getUserId()]);
        $boardRepository->addBoard($board);

        /*
         *  메일 기능
         *  php - send mail
         */
        $subject = 'post has been written.';
        $message = $email . ' post has been written.';

        if ($mailSender->sendToAdmins($subject, $message)) {
            echo "메일이 성공적으로 전송되었습니다.";
        } else {
            echo "메일 전송에 실패했습니다.";
        }

        // 로그 작성
        $status = 'normal';
        $logger->createPost($_SERVER['REQUEST_URI'], $email, $status);

        $result['content'] = "게시글이 성공적으로 추가되었습니다.";
        return $result;
    }
}

?>