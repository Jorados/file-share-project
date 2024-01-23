<?php
/**
 * 댓글 - 데이터모델
 */
namespace dataset;

class Comment extends BaseModel{

    protected $comment_id;
    protected $content;
    protected $date;
    protected $board_id;
    protected $user_id;

    function __construct($data = null){  //$data=null --> 매개변수 없으면 기본값으로 null을 사용
        parent::__construct($data);
    }

    public function getCommentId(){
        return $this->comment_id;
    }

    public function setCommentId($comment_id): self{
        $this->comment_id = $comment_id;
        return $this;
    }

    public function getContent(){
        return $this->content;
    }

    public function setContent($content): self{
        $this->content = $content;
        return $this;
    }

    public function getDate(){
        return $this->date;
    }

    public function setDate($date): self{
        $this->date = $date;
        return $this;
    }

    public function getBoardId(){
        return $this->board_id;
    }

    public function setBoardId($board_id): self{
        $this->board_id = $board_id;
        return $this;
    }

    public function getUserId(){
        return $this->user_id;
    }

    public function setUserId($user_id): self{
        $this->user_id = $user_id;
        return $this;
    }
}

?>