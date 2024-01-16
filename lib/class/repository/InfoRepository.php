<?php

namespace repository;
use database\DatabaseConnection;
class InfoRepository {
    public $pdo;

    public function __construct() {
        $this->pdo = DatabaseConnection::getInstance()->getConnection();
    }

    public function getLatestInfoByBoardId($board_id) {
        try {
            $infoQuery = "SELECT * FROM info WHERE board_id = :board_id ORDER BY info_id DESC LIMIT 1";
            $stmt = $this->pdo->prepare($infoQuery);
            $stmt->bindParam(':board_id', $board_id, \PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch();
        } catch (\PDOException $e) {
            die("Error while fetching info: " . $e->getMessage());
        }
    }

    public function addInfo($reason_content, $user_id, $board_id) {
        $insertQuery = "INSERT INTO info (reason_content, date, user_id, board_id) VALUES (:reason_content, NOW(), :user_id, :board_id)";
        $stmt = $this->pdo->prepare($insertQuery);
        $stmt->bindParam(':reason_content', $reason_content, \PDO::PARAM_STR);
        $stmt->bindParam(':user_id', $user_id, \PDO::PARAM_INT);
        $stmt->bindParam(':board_id', $board_id, \PDO::PARAM_INT);
        $stmt->execute();
    }
}
?>