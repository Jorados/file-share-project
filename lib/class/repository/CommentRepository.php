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
        $data = ['board_id'=>$board_id];
        $stmt = $this->select($this->table,null,$data);
        return DatabaseController::arrayMapObjects(new Comment(), $stmt->fetchAll(\PDO::FETCH_ASSOC));
    }

    /**
     * 게시글에 존재하는 댓글 count
     * @param int $board_id
     * @return mixed
     */
    public function getCountComments($board_id){
        $query = "SELECT COUNT(*) as total FROM comment WHERE board_id = :board_id;";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute(['board_id'=>$board_id]);
        $result = $stmt->fetch();
        return $result['total'];
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

    /**
     * 댓글 삭제
     * @param Comment $comment
     */
    public function deleteComment(Comment $comment){
        $data=['comment_id'=>$comment->getCommentId()];
        $this->delete($this->table, $data);
    }

    /**
     * 특정 댓글 조회
     * @param Comment $comment
     * @return Comment
     */
    public function findCommentById(Comment $comment){
        $data=['comment_id'=>$comment->getCommentId()];
        $stmt = $this->select($this->table,null,$data);
        return new Comment($stmt->fetch(\PDO::FETCH_ASSOC));
    }
}

?>