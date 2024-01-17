<?php

namespace repository;

use database\DatabaseConnection;
use dataset\Comment;

class CommentRepository {
    public $pdo;

    public function __construct() {
        $this->pdo = DatabaseConnection::getInstance()->getConnection();
    }

    public function getCommentsByBoardId($board_id) {
        $commentsQuery = "SELECT * FROM comment WHERE board_id = :board_id";
        $stmt = $this->pdo->prepare($commentsQuery);
        $stmt->bindParam(':board_id', $board_id, \PDO::PARAM_INT);
        $stmt->execute();

        return array_map(
            function ($comment) {
                return new Comment($comment);
            },
            $stmt->fetchAll(\PDO::FETCH_ASSOC)
        );
    }

    public function addComment($content, $board_id, $user_id) {
        $insertQuery = "INSERT INTO comment (content, date, board_id, user_id) VALUES (:content, NOW(), :board_id, :user_id)";
        $stmt = $this->pdo->prepare($insertQuery);
        $stmt->bindParam(':content', $content, \PDO::PARAM_STR);
        $stmt->bindParam(':board_id', $board_id, \PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $user_id, \PDO::PARAM_INT);
        $stmt->execute();
    }
}

?>