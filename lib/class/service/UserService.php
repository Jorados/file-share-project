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
use log\UserLogger;

use dataset\User;

class UserService{


    /**
     * 사용자 - 공지 상세 조회
     * @param $board_id
     * @param $email
     * @return array
     */
    public function noticeDetailsByUser($board_id,$email): array{
        $boardRepository = new BoardRepository();
        $attachmentRepository = new AttachmentRepository();
        $commentRepository = new CommentRepository();
        $logger = new PostLogger();

        $board = $boardRepository->getBoardById($board_id);
        $attachments = $attachmentRepository->getAttachmentsByBoardId($board_id);
        $comments = $commentRepository -> getCommentsByBoardId($board_id);

        $title = $board->getTitle();
        $status = $board->getStatus();
        $logger->readPost($_SERVER['REQUEST_URI'], $email, $status, $title);

        return [
            'board' => $board,
            'attachments' => $attachments,
            'comments'=> $comments
        ];

        //return [$board,$attachments,$comments];
    }

    /**
     * 사용자 글 조회
     * @param $email
     * @param $board_id
     * @return array
     */
    public function boardDetailByUser($email, $board_id): array{
        $userRepository = new UserRepository();
        $boardRepository = new BoardRepository();
        $infoRepository = new InfoRepository();
        $commentRepository = new CommentRepository();
        $attachmentRepository = new AttachmentRepository();
        $logger = new PostLogger();

        $board = $boardRepository -> getBoardById($board_id);
        $info = $infoRepository -> getLatestInfoByBoardId($board_id);
        $user = $userRepository->getUserById($info->getUserId());
        $comments = $commentRepository -> getCommentsByBoardId($board_id);
        $attachments = $attachmentRepository->getAttachmentsByBoardId($board_id);

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
     * 관리자 글 조회
     * @param $email
     * @param $board_id
     * @return array
     */
    public function boardDetailByAdmin($email,$board_id): array{
        $boardRepository = new BoardRepository();
        $userRepository = new UserRepository();
        $commentRepository = new CommentRepository();
        $attachmentRepository = new AttachmentRepository();
        $logger = new PostLogger();

        $board = $boardRepository -> getBoardByid($board_id);
        $user_id = $board->getUserId();
        $user = $userRepository -> getUserById($user_id);
        $comments = $commentRepository -> getCommentsByBoardId($board_id);
        $attachments = $attachmentRepository->getAttachmentsByBoardId($board_id);

        $status = $board->getStatus();
        $title = $board->getTitle();
        $logger->readPost($_SERVER['REQUEST_URI'], $email, $status, $title);

        return [
            'board' => $board,
            'user'=> $user,
            'comments' => $comments,
            'attachments' => $attachments
        ];
    }

    /**
     * 비밀번호 변경
     * @param mixed $password
     * @param String $email
     * @return array
     */
    public function changePassword($password, $email){
        if (!preg_match("/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$/", $password)) {
            return [
                'status' => false,
                'content' => '비밀번호는 영문자와 숫자를 모두 포함하고, 최소 8자 이상이어야 합니다.'
            ];
        }

        $userRepository = new UserRepository();
        $logger = new UserLogger();

        // 비밀번호 유효성 검사
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $user = new User(['email' => $email,'password'=>$hashed_password]);
        $userRepository->updateUserPassword($user);
        $userRepository->updateAvailableStatus($user);

        $logger->changePassword($_SERVER['REQUEST_URI'], $email);

        return [
            'status'=>true,
            'content'=>'성공적으로 비밀번호가 변경되었습니다. 다시 로그인 해주세요.'
        ];
    }

    /**
     * 관리자 -> 회원 생성
     * @param String $email_user
     * @param String $email_admin
     * @param String $username
     * @param String $phone
     * @param mixed $password
     * @return array
     */
    public function createUser($email_user, $email_admin, $username, $phone, $password){
        $userRepository = new UserRepository();
        $logger = new UserLogger();

        // 이메일 유효성 검사
        $result = [];
        if (!filter_var($email_user, FILTER_VALIDATE_EMAIL)) {
            $result['status'] =false;
            $result['content'] ="유효한 이메일 형식이 아닙니다. 올바른 이메일 주소를 입력해주세요.";
        } else {
            // 중복 이메일 검사
            if($userRepository->isEmailDuplicate(new User(['email'=>$email_user]))){
                $result['status'] =false;
                $result['content'] ="이미 사용 중인 이메일 주소입니다.";
            }
            else{
                // 공백 여부 검사
                if (empty($username) || empty($phone)) {
                    $result['status'] =false;
                    $result['content'] ="이름과 전화번호는 공백일 수 없습니다.";
                } else {
                    // 전화번호 숫자 및 길이 검사
                    if (!preg_match("/^\d{11}$/", $phone)) {
                        $result['status'] = false;
                        $result['content'] ="전화번호는 11자리의 숫자여야 합니다.";
                    } else {
                        // 비밀번호 유효성 검사
                        if (!preg_match("/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$/", $password)) {
                            $result['status'] = false;
                            $result['content'] = "비밀번호는 영어와 숫자를 모두 포함하고, 최소 8자 이상이어야 합니다.";
                        } else {
                            // 비밀번호 해싱
                            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                            // 회원 가입
                            $user = new User(['email'=>$email_user,'password'=>$hashed_password,'username'=>$username,'phone'=>$phone]);
                            $userRepository->createUser($user);

                            $logger->createUser($_SERVER['REQUEST_URI'], $email_admin, $email_user);

                            $result['status'] = true;
                            $result['content'] = "회원 가입이 성공적으로 완료되었습니다.";
                        }
                    }
                }
            }
        }
        return $result;
    }

    /**
     * 관리자 -> 회원 정보 변경
     * @param int $user_id
     * @param String $email
     * @param String $username
     * @param String $phone
     * @param mixed $password
     * @return array
     */
    public function editUser($user_id, $email,$username,$phone,$password){
        $userRepository = new UserRepository();

        $result = [];
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $result['status'] = false;
            $result['content'] = "유효한 이메일 형식이 아닙니다. 올바른 이메일 주소를 입력해주세요.";
        } else if (empty($username) || empty($phone)) {
            // username과 phone이 둘 다 공백인지 확인
            $result['status'] = false;
            $result['content'] = "이름과 전화번호는 공백일 수 없습니다.";
        } else if (!preg_match("/^\d{11}$/", $phone)) {
            // phone이 11자리의 숫자인지 확인
            $result['status'] = false;
            $result['content'] = "전화번호는 11자리의 숫자여야 합니다.";
        } else if (empty($password)) {
            // 비밀번호 입력 여부 확인
            $result['status'] = false;
            $result['content'] = "비밀번호를 입력해주세요.";
        } else if (!preg_match("/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$/", $password)) {
            // 비밀번호 유효성 검사 (영어와 숫자, 최소 8자)
            $result['status'] = false;
            $result['content'] = "비밀번호는 영어와 숫자를 포함하여 8자 이상이어야 합니다.";
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT); // 비밀번호 암호화

            $user = new User(['$user_id'=>$user_id , '$email' => $email, 'password'=>$hashedPassword, 'username'=>$username, 'phone'=>$phone]);
            $userRepository->updateUserDetails($user);

            $result['status'] = true;
            $result['content'] = "회원 정보가 성공적으로 업데이트되었습니다.";
        }

        return $result;
    }

    /**
     * 회원 로그인
     * @param String $email
     * @param String $password
     * @return array
     */
    public function loginUser($email,$password){
        $userRepository = new UserRepository();
        $logger = new UserLogger();

        $userInfo = new User(['email'=>$email ,'password'=>$password]);
        $user = $userRepository->loginUser($userInfo);

        $result = [];
        if ($user) {
            $_SESSION['loggedin'] = true;
            $_SESSION['email'] = $user->getEmail();
            $_SESSION['session_start_time'] = time(); // 세션 시작 시간 설정
            $_SESSION['available'] = $user->getAvailable();
            $_SESSION['authority'] = $user->getAuthority();
            $_SESSION['role'] = $user->getRole();
            $_SESSION['user_id'] = $user->getUserId();

            if ($user->getRole() == 'admin') {
                $result['status'] = true;
                $result['content'] = '로그인 성공!';
                $result['role'] = 1;
                $logger->login($_SERVER['REQUEST_URI'], $user->getEmail());
            } else if ($user->getRole() == 'user') {
                $result['status'] = true;
                $result['content'] = '로그인 성공!';
                $result['role'] = 1;
                $result['available'] = $user->getAvailable();
                $logger->login($_SERVER['REQUEST_URI'], $user->getEmail());
            } else {
                $result['status'] = false;
                $result['content'] = "알 수 없는 역할입니다.";
            }
        } else {
            $result['status'] = false;
            $result['content'] = "유효하지 않은 이메일 또는 비밀번호입니다.";
        }
        return $result;
    }

    /**
     * 관리자 -> 사용자 권한 변경
     * @param int $userId
     * @param int $newRole
     * @param String $adminEmail
     * @return array
     */
    public function updateRole($userId,$newRole,$adminEmail){
        $userRepository = new UserRepository();
        $logger = new UserLogger();

        $user = new User(['user_id'=>$userId , 'role'=> $newRole]);

        $result = [];
        if($user){
            $userRepository->updateUserRole($user);
            $userEmail = $userRepository->getUserEmailById($user);

            $logger->changeAuthority($_SERVER['REQUEST_URI'], $adminEmail, $userEmail->getEmail(), $user->getRole());
            $result['status'] = true;
            $result['content'] = "성공적으로 사용자 권한을 변경하였습니다";
        }
        else{
            $result['status'] = false;
            $result['content'] = "사용자 권한에 실패하였습니다";
        }

        return $result;
    }

}

?>