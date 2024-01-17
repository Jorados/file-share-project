<?php

namespace repository;

use database\DatabaseConnection;
use dataset\Board;
use dataset\User;

class BoardRepository {
    public $pdo;

    public function __construct() {
        $this->pdo = DatabaseConnection::getInstance()->getConnection();
    }

    // 토탈 board
    public function getTotalItemsByUserId($user_id) {
        $query = "SELECT COUNT(*) as total FROM board WHERE user_id = :user_id AND status = 'normal';";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':user_id', $user_id, \PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['total'];
    }

    // status가 'notification'인 board 조회
    public function getNotificationBoardItems() {
        $query = "SELECT * FROM board WHERE status = 'notification'";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();

        return array_map(
            function ($board){
                return new Board($board);
            },
            $stmt->fetchAll(\PDO::FETCH_ASSOC)
        );
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

    public function addBoard($title, $content, $date, $user_id) {
        $query = "INSERT INTO board (title, content, date, user_id) VALUES (:title, :content, :date, :user_id)";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':title', $title, \PDO::PARAM_STR);
        $stmt->bindParam(':content', $content, \PDO::PARAM_STR);
        $stmt->bindParam(':date', $date, \PDO::PARAM_STR);
        $stmt->bindParam(':user_id', $user_id, \PDO::PARAM_INT);
        $stmt->execute();
    }

    public function getTotalBoardCount() {
        $query = "SELECT COUNT(*) as total FROM board WHERE status = 'normal';";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['total'];
    }

    public function getBoardsByPage($offset, $items_per_page, $order) {
        $orderClause = ($order === 'oldest') ? 'ORDER BY date ASC' : 'ORDER BY date DESC';

        $query = "SELECT * FROM board WHERE status = 'normal' $orderClause LIMIT :offset, :items_per_page;";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':offset', $offset, \PDO::PARAM_INT);
        $stmt->bindParam(':items_per_page', $items_per_page, \PDO::PARAM_INT);
        $stmt->execute();

        return array_map(
            function ($board){
                return new Board($board);
            },
            $stmt->fetchAll(\PDO::FETCH_ASSOC)
        );
    }

    public function getBoardsByPageAndUser($user_id, $offset, $items_per_page, $order) {
        $orderClause = ($order === 'oldest') ? 'ORDER BY date ASC' : 'ORDER BY date DESC';
        $query = "SELECT * FROM board WHERE status = 'normal' AND user_id = :user_id $orderClause LIMIT :offset, :items_per_page;";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':user_id', $user_id, \PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, \PDO::PARAM_INT);
        $stmt->bindParam(':items_per_page', $items_per_page, \PDO::PARAM_INT);
        $stmt->execute();

        return array_map(
            function ($board){
                return new Board($board);
            },
            $stmt->fetchAll(\PDO::FETCH_ASSOC)
        );
    }


    public function adminCreateBoard($title, $content, $date, $user_id, $status) {
        $query = "INSERT INTO board (title, content, date, user_id, status) VALUES (:title, :content, :date, :user_id, :status)";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':title', $title, \PDO::PARAM_STR);
        $stmt->bindParam(':content', $content, \PDO::PARAM_STR);
        $stmt->bindParam(':date', $date, \PDO::PARAM_STR);
        $stmt->bindParam(':user_id', $user_id, \PDO::PARAM_INT);
        $stmt->bindParam(':status', $status, \PDO::PARAM_STR);
        $stmt->execute();
    }

    public function updateBoardPermission($board_id, $newPermission) {
        $updateQuery = "UPDATE board SET openclose = :openclose WHERE board_id = :board_id";
        $stmt = $this->pdo->prepare($updateQuery);
        $stmt->bindParam(':openclose', $newPermission, \PDO::PARAM_INT);
        $stmt->bindParam(':board_id', $board_id, \PDO::PARAM_INT);
        $stmt->execute();
    }

    public function getBoardIdLimit1() {
        $updateQuery = "SELECT board_id FROM board ORDER BY board_id DESC LIMIT 1";
        $stmt = $this->pdo->prepare($updateQuery);
        $stmt->execute();
        $stmt->fetch(\PDO::FETCH_ASSOC);
        return new Board($stmt);
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
}
?>