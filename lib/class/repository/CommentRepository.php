<?php
/**
 * 데이터모델 Comment sql 레포지토리
 */
namespace repository;

use database\DatabaseController;
use dataset\Comment;


class CommentRepository extends BaseRepository {

    /** 생성자 */
    public function __construct(){
        parent::__construct();
        $this->setTable('comment');
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
        $data = [
            'content' => $comment->getContent(),
            'board_id' => $comment->getBoardId(),
            'user_id' => $comment->getUserId(),
            'date' => date('Y-m-d H:i:s') // 현재 날짜와 시간을 포맷에 맞춰 전달
        ];
        $this->insert($this->table, $data);
    }

    public function getCountComments($board_id){
        $query = "SELECT COUNT(*) as total FROM comment WHERE board_id = :board_id;";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute(['board_id'=>$board_id]);
        $result = $stmt->fetch();
        return $result['total'];
    }
}

?>