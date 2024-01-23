<?php

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

    // 토탈 board
    public function getTotalItemsByUserId(User $user) {
        $query = "SELECT COUNT(*) as total FROM board WHERE user_id = :user_id AND status = 'normal';";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([
            'user_id'=>$user->getUserId()
        ]);
        $result = $stmt->fetch();
        return $result['total'];
    }

    // status가 'notification'인 board 조회
    public function getNotificationBoardItems() {
        $query = "SELECT * FROM board WHERE status = 'notification'";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        return DatabaseController::arrayMapObjects(new Board(), $stmt->fetchAll(\PDO::FETCH_ASSOC));
    }


    public function getBoardById($board_id) {
        $query = "SELECT * FROM board WHERE board_id = :board_id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':board_id', $board_id, \PDO::PARAM_INT);
        $stmt->execute();
        return new Board($stmt->fetch(\PDO::FETCH_ASSOC));
    }

    public function deleteBoardById($board_id) {
        $deleteQuery = "DELETE FROM board WHERE board_id = :board_id";
        $stmt = $this->pdo->prepare($deleteQuery);
        $stmt->bindParam(':board_id', $board_id, \PDO::PARAM_INT);
        return $stmt->execute();
    }

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

//    public function getTotalBoardCount() {
//        $query = "SELECT COUNT(*) as total FROM board WHERE status = 'normal';";
//        $stmt = $this->pdo->prepare($query);
//        $stmt->execute();
//        $result = $stmt->fetch();
//        return $result['total'];
//    }
//
//    public function getBoardsByPage($offset, $items_per_page, $order) {
//        $orderClause = ($order === 'oldest') ? 'ORDER BY date ASC' : 'ORDER BY date DESC';
//
//        $query = "SELECT * FROM board WHERE status = 'normal' $orderClause LIMIT :offset, :items_per_page;";
//        $stmt = $this->pdo->prepare($query);
//        $stmt->bindParam(':offset', $offset, \PDO::PARAM_INT);
//        $stmt->bindParam(':items_per_page', $items_per_page, \PDO::PARAM_INT);
//        $stmt->execute();
//
//        return DatabaseController::arrayMapObjects(new Board(), $stmt->fetchAll(\PDO::FETCH_ASSOC));
//    }

    public function getBoardsByPageAndUser($user_id, $offset, $items_per_page, $order) {
        $orderClause = ($order === 'oldest') ? 'ORDER BY date ASC' : 'ORDER BY date DESC';
        $query = "SELECT * FROM board WHERE status = 'normal' AND user_id = :user_id $orderClause LIMIT :offset, :items_per_page;";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':user_id', $user_id, \PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, \PDO::PARAM_INT);
        $stmt->bindParam(':items_per_page', $items_per_page, \PDO::PARAM_INT);
        $stmt->execute();

        return DatabaseController::arrayMapObjects(new Board(), $stmt->fetchAll(\PDO::FETCH_ASSOC));
    }

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

    public function updateBoardPermission(Board $board) {
        $updateQuery = "UPDATE board SET openclose = :openclose WHERE board_id = :board_id";
        $stmt = $this->pdo->prepare($updateQuery);
        $stmt->execute([
            'openclose'=>$board->getOpenclose(),
            'board_id'=>$board->getBoardId()
        ]);
    }

    public function getBoardIdLimit1() {
        $updateQuery = "SELECT board_id FROM board ORDER BY board_id DESC LIMIT 1";
        $stmt = $this->pdo->prepare($updateQuery);
        $stmt->execute();
        return new Board($stmt->fetch(\PDO::FETCH_ASSOC));
    }


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

    // 허용된 글 중에서 1일 이상지난 board를 찾는 sql
    public function getOpencloseBoard(){
        $sql = "SELECT board_id FROM board WHERE openclose = 1 AND date <= DATE_SUB(NOW(), INTERVAL 1 DAY) AND status = 'normal'";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return DatabaseController::arrayMapObjects(new Board(), $stmt->fetchAll(\PDO::FETCH_ASSOC));
    }

    //허용된 글 중에서 1일 이상지난 board를 열람 불가상태로 변경하는 sql
    public function updateOpencloseBoard(){
        $sql = "UPDATE board SET openclose = 0 WHERE openclose = 1 AND date <= DATE_SUB(NOW(), INTERVAL 1 DAY) AND status = 'normal'";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
    }

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

    public function getBoardsByPage($offset, $items_per_page, $order, $permission = null, $searchType = null, $searchQuery = null, $userId = null) {
        $orderClause = ($order === 'oldest') ? 'ORDER BY date ASC' : 'ORDER BY date DESC';
        $whereClause = $this->buildWhereClause($permission, $searchType, $searchQuery, $userId);
        $query = "SELECT * FROM board WHERE status = 'normal' $whereClause $orderClause LIMIT :offset, :items_per_page;";
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
        $stmt->bindParam(':offset', $offset, \PDO::PARAM_INT);
        $stmt->bindParam(':items_per_page', $items_per_page, \PDO::PARAM_INT);
        $stmt->execute();
        return DatabaseController::arrayMapObjects(new Board(), $stmt->fetchAll(\PDO::FETCH_ASSOC));
    }


    // 검색 조건 필터링
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