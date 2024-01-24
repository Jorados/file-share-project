<?php
/**
 * 데이터모델 Comment sql 레포지토리
 */
namespace repository;

use database\DatabaseConnection;
use database\DatabaseController;
use dataset\Comment;


class CommentRepository {
    public $pdo;

    public function __construct() {
        $this->pdo = DatabaseConnection::getInstance()->getConnection();
    }

    /**
     * 특정 board에 존재하는 comment readAll
     * @param int $board_id
     * @return array|\dataset\BaseModel[]
     */
    public function getCommentsByBoardId($board_id) {
        $commentsQuery = "SELECT * FROM comment WHERE board_id = :board_id";
        $stmt = $this->pdo->prepare($commentsQuery);
        $stmt->bindParam(':board_id', $board_id, \PDO::PARAM_INT);
        $stmt->execute();
        return DatabaseController::arrayMapObjects(new Comment(), $stmt->fetchAll(\PDO::FETCH_ASSOC));
    }

    /**
     * 댓글 create
     * @param Comment $comment
     */
    public function addComment(Comment $comment) {
        $insertQuery = "INSERT INTO comment (content, date, board_id, user_id) VALUES (:content, NOW(), :board_id, :user_id)";
        $stmt = $this->pdo->prepare($insertQuery);
        $stmt->execute([
            'content'=>$comment->getContent(),
            'board_id'=>$comment->getBoardId(),
            'user_id'=>$comment->getUserId()
        ]);
    }
}

?>