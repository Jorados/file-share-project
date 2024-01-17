<?php

namespace dataset;

class Info extends BaseModel{

    protected $info_id;
    protected $date;
    protected $reason_content;
    protected $board_id;
    protected $user_id;

    function __construct($data = null){
        parent::__construct($data);
    }

    public function getInfoId(){
        return $this->info_id;
    }

    public function setInfoId($info_id): self{
        $this->info_id = $info_id;
        return $this;
    }

    public function getDate(){
        return $this->date;
    }

    public function setDate($date): self{
        $this->date = $date;
        return $this;
    }

    public function getReasonContent(){
        return $this->reason_content;
    }

    public function setReasonContent($reason_content): self{
        $this->reason_content = $reason_content;
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

