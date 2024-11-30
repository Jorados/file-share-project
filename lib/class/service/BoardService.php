<?php
/**
 * Board 관련 비즈니스 로직 처리 클래스
 */
namespace service;

use dataset\Board;
use dataset\Info;
use dataset\User;
use log\PostLogger;
use mail\SendMail;
use repository\BoardRepository;
use repository\InfoRepository;
use repository\UserRepository;
use util\Constant;

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
    public function getBoardByPage($items_per_page, $order, $offset, $status, $permission = null, $searchType = null, $searchQuery = null, $user_id = null) {
        $boardRepository = new BoardRepository();
        $userRepository = new UserRepository();

        if($permission === Constant::PERMISSION_NOT_SELECT) $permission=null;
        if($searchType === Constant::SEARCHTYPE_NOT_SELECT) $searchType=null;

        // 여기서 해당 user_id가 일반회원이면 null , 아니면 그대로. 넘기면된다.
        $user = $userRepository->getUserById($user_id);
        if($user->getRole() === "admin") $user_id = null;

        $total_items = $boardRepository->getTotalBoardCount($status, $permission, $searchType, $searchQuery, $user_id);
        $boards = $boardRepository->getBoardsByPage($offset, $items_per_page, $order, $status, $permission, $searchType, $searchQuery, $user_id);

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
    public function deleteBoard($email, $board_id=null): array{
        $boardRepository = new BoardRepository();
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
                // 논리적 삭제
                $boardRepository->updateDeleteType($board_id);

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
    public function boardAuthorityChange($newPermission, $board_id, $user_id, $reason_content=null){
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
//            $mailSender->sendToUser($subject, $message,$user->getEmail());

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
//        $mailSender->sendToAdmins($subject, $message);

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
//        $mailSender->sendToAdmins($subject, $message);

        // 로그 작성
        $status = 'normal';
        $logger->createPost($_SERVER['REQUEST_URI'], $email, $status);

        $result['content'] = "게시글이 성공적으로 추가되었습니다.";
        return $result;
    }
}

?>