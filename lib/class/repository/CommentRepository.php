<?php

namespace repository;

class CommentRepository {
    public $pdo;

    public function __construct(\PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function getCommentsByBoardId($board_id) {
        try {
            $commentsQuery = "SELECT * FROM comment WHERE board_id = :board_id";
            $stmt = $this->pdo->prepare($commentsQuery);
            $stmt->bindParam(':board_id', $board_id, \PDO::PARAM_INT);
            $stmt->execute();
            $comments = $stmt->fetchAll();
            return $comments;
        } catch (\PDOException $e) {
            die("댓글 조회 중 오류가 발생했습니다: " . $e->getMessage());
        }
    }

    public function addComment($content, $board_id, $user_id) {
        $insertQuery = "INSERT INTO comment (content, date, board_id, user_id) VALUES (:content, NOW(), :board_id, :user_id)";
        $stmt = $this->pdo->prepare($insertQuery);
        $stmt->bindParam(':content', $content, \PDO::PARAM_STR);
        $stmt->bindParam(':board_id', $board_id, \PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $user_id, \PDO::PARAM_INT);
        $stmt->execute();
    }

    public function getCommentByBoardId($board_id) {
        $commentsQuery = "SELECT * FROM comment WHERE board_id = :board_id ";
        $stmt = $this->pdo->prepare($commentsQuery);
        $stmt->bindParam(':board_id', $board_id, \PDO::PARAM_INT);
        $stmt->execute();
        return $comments = $stmt->fetchAll();
    }
}

?>