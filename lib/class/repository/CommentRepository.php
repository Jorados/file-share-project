<?php

namespace repository;

use database\DatabaseConnection;
use database\DatabaseController;
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
        return DatabaseController::arrayMapObjects(new Comment(), $stmt->fetchAll(\PDO::FETCH_ASSOC));
    }

    public function addComment($comment) {
        $insertQuery = "INSERT INTO comment (content, date, board_id, user_id) VALUES (:content, NOW(), :board_id, :user_id)";
        $stmt = $this->pdo->prepare($insertQuery);
        $stmt->bindParam(':content', $comment->getContent(), \PDO::PARAM_STR);
        $stmt->bindParam(':board_id', $comment->getBoardId(), \PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $comment->getUserId(), \PDO::PARAM_INT);
        $stmt->execute();
    }
}

?>