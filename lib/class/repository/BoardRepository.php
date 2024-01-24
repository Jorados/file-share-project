<?php
/**
 * 데이터모델 Board sql 레포지토리
 */
namespace repository;

use database\DatabaseConnection;
use database\DatabaseController;
use dataset\Board;
use dataset\User;

class BoardRepository {
    public $pdo;

    public function __construct() {
        $this->pdo = DatabaseConnection::getInstance()->getConnection();
    }

    /**
     * status가 'notification'인 board 조회
     * @return array|\dataset\BaseModel[]
     */
    public function getNotificationBoardItems() {
        $query = "SELECT * FROM board WHERE status = 'notification'";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        return DatabaseController::arrayMapObjects(new Board(), $stmt->fetchAll(\PDO::FETCH_ASSOC));
    }

    /**
     * 특정 글 read
     * @param $board_id
     * @return Board
     */
    public function getBoardById($board_id) {
        $query = "SELECT * FROM board WHERE board_id = :board_id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':board_id', $board_id, \PDO::PARAM_INT);
        $stmt->execute();
        return new Board($stmt->fetch(\PDO::FETCH_ASSOC));
    }

    /**
     * 특정 글 delete
     * @param $board_id
     * @return bool
     */
    public function deleteBoardById($board_id) {
        $deleteQuery = "DELETE FROM board WHERE board_id = :board_id";
        $stmt = $this->pdo->prepare($deleteQuery);
        $stmt->bindParam(':board_id', $board_id, \PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * 글 create
     * @param Board $board
     */
    public function addBoard(Board $board) {
        $query = "INSERT INTO board (title, content, date, user_id) VALUES (:title, :content, :date, :user_id)";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([
            'title'=>$board->getTitle(),
            'content'=>$board->getContent(),
            'date'=>$board->getDate(),
            'user_id'=>$board->getUserId()
        ]);
    }

    /**
     * 글 update
     * @param Board $board
     */
    public function updateBoardPermission(Board $board) {
        $updateQuery = "UPDATE board SET openclose = :openclose WHERE board_id = :board_id";
        $stmt = $this->pdo->prepare($updateQuery);
        $stmt->execute([
            'openclose'=>$board->getOpenclose(),
            'board_id'=>$board->getBoardId()
        ]);
    }

    /**
     * 관리자 글 생성
     * @param Board $board
     */
    public function adminCreateBoard(Board $board) {
        $query = "INSERT INTO board (title, content, date, user_id, status) VALUES (:title, :content, :date, :user_id, :status)";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([
            'title'=>$board->getTitle(),
            'content'=>$board->getContent(),
            'date'=>$board->getDate(),
            'user_id'=>$board->getUserId(),
            'status'=>$board->getStatus()
        ]);
    }

    /**
     * 가장 최신 글 1개 조회
     * @return Board
     */
    public function getBoardIdLimit1() {
        $updateQuery = "SELECT board_id FROM board ORDER BY board_id DESC LIMIT 1";
        $stmt = $this->pdo->prepare($updateQuery);
        $stmt->execute();
        return new Board($stmt->fetch(\PDO::FETCH_ASSOC));
    }

    /**
     * 특정 글 작성한 유저의 email read
     * @param $board_id
     * @return User
     */
    public function getBoardUserEmail($board_id) {
        $sql = "SELECT u.email 
            FROM user u 
            WHERE u.user_id IN (
                SELECT b.user_id 
                FROM board b 
                WHERE b.board_id = :board_id
            )";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':board_id', $board_id, \PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return new User($result);
    }

    /**
     * 허용된 글 중에서 1일 이상지난 board read
     * @return array|\dataset\BaseModel[]
     */
    public function getOpencloseBoard(){
        $sql = "SELECT board_id FROM board WHERE openclose = 1 AND date <= DATE_SUB(NOW(), INTERVAL 1 DAY) AND status = 'normal'";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return DatabaseController::arrayMapObjects(new Board(), $stmt->fetchAll(\PDO::FETCH_ASSOC));
    }


    /**
     * 허용된 글 중에서 1일 이상지난 board를 열람 불가상태로 update
     */
    public function updateOpencloseBoard(){
        $sql = "UPDATE board SET openclose = 0 WHERE openclose = 1 AND date <= DATE_SUB(NOW(), INTERVAL 1 DAY) AND status = 'normal'";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
    }

    /**
     * 매개변수(검색 조건) 고려하여 board의 count read
     * @param null $permission
     * @param null $searchType
     * @param null $searchQuery
     * @param null $userId
     * @return mixed
     */
    public function getTotalBoardCount($permission = null, $searchType = null, $searchQuery = null, $userId = null) {
        $whereClause = $this->buildWhereClause($permission, $searchType, $searchQuery, $userId);
        $query = "SELECT COUNT(*) as total FROM board WHERE status = 'normal' $whereClause;";
        $stmt = $this->pdo->prepare($query);

        if ($permission !== null) {
            $stmt->bindParam(':permission', $permission, \PDO::PARAM_STR);
        }

        if($userId !== null){
            $stmt->bindParam(':userId',$userId, \PDO::PARAM_INT);
        }

        if ($searchType !== null && $searchQuery !== null) {
            $str = "%$searchQuery%";
            $stmt->bindParam(':search_query', $str, \PDO::PARAM_STR);
        }

        $stmt->execute();
        $result = $stmt->fetch();
        return $result['total'];
    }

    /**
     * 매개변수(검색 조건) 고려하여 특정 페이지 글 read
     * @param $offset
     * @param $items_per_page
     * @param $order
     * @param null $permission
     * @param null $searchType
     * @param null $searchQuery
     * @param null $userId
     * @return array|\dataset\BaseModel[]
     */
    public function getBoardsByPage($offset, $items_per_page, $order, $permission = null, $searchType = null, $searchQuery = null, $userId = null) {
        $paramArr = [];
        $orderClause = ($order === 'oldest') ? 'ORDER BY date ASC' : 'ORDER BY date DESC';
        $whereClause = $this->buildWhereClause($permission, $searchType, $searchQuery, $userId);
        $query = "SELECT * FROM board WHERE status = 'normal' $whereClause $orderClause LIMIT :offset, :items_per_page;";
        $stmt = $this->pdo->prepare($query);

        if ($permission !== null) {
            $paramArr[':permission'] = $permission;
        }
        if($userId !== null) {
            $paramArr[':userId'] = $userId;
        }
        if ($searchType !== null && $searchQuery !== null) {
            $str = "%$searchQuery%";
            $paramArr[':search_query'] = $str;
        }

        $stmt->execute(
            array_merge($paramArr,
            [':offset'=>$offset],
            [':items_per_page'=>$items_per_page]
            )
        );
        return DatabaseController::arrayMapObjects(new Board(), $stmt->fetchAll(\PDO::FETCH_ASSOC));
    }



    /**
     * 검색 조건 필터링
     * @param null $permission
     * @param null $searchType
     * @param null $searchQuery
     * @param null $userId
     * @return string
     */

    private function buildWhereClause($permission = null, $searchType = null, $searchQuery = null, $userId = null) {
        $whereConditions = [];

        if ($permission !== null) {
            $whereConditions[] = "openclose = :permission";
        }

        if ($userId !== null){
            $whereConditions[] = "user_id = :userId";
        }

        if ($searchType !== null && $searchQuery !== null) {
            $whereConditions[] = "$searchType LIKE :search_query";
        }

        if (!empty($whereConditions)) {
            return "AND " . implode(" AND ", $whereConditions);
        }

        return "";
    }

}
?>