<?php

namespace repository;

use database\DatabaseConnection;
use dataset\Info;

class InfoRepository {
    public $pdo;

    public function __construct() {
        $this->pdo = DatabaseConnection::getInstance()->getConnection();
    }

    public function getLatestInfoByBoardId($board_id) {
        $infoQuery = "SELECT * FROM info WHERE board_id = :board_id ORDER BY info_id DESC LIMIT 1";
        $stmt = $this->pdo->prepare($infoQuery);
        $stmt->bindParam(':board_id', $board_id, \PDO::PARAM_INT);
        $stmt->execute();
        return new Info($stmt->fetch(\PDO::FETCH_ASSOC));
    }

    public function addInfo(Info $info) {
        $insertQuery = "INSERT INTO info (reason_content, date, user_id, board_id) VALUES (:reason_content, NOW(), :user_id, :board_id)";
        $stmt = $this->pdo->prepare($insertQuery);
        $stmt->execute([
            'reason_content'=>$info->getReasonContent(),
            'user_id'=>$info->getUserId(),
            'board_id'=>$info->getBoardId()
        ]);
    }
}
?>