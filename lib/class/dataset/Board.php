<?php

namespace dataset;

use dataset\BaseModel;


class Board extends BaseModel{

    protected $board_id;
    protected $title;
    protected $content;
    protected $date;
    protected $status;
    protected $user_id;
    protected $openclose;

    public function __construct($data = null){ //$data=null --> 매개변수 없으면 기본값으로 null을 사용
        parent::__construct($data);
    }

    public function getBoardId(){
        return $this->board_id;
    }

    public function setBoardId($board_id): self {
        $this->board_id = $board_id;
        return $this;
    }

    public function getTitle(){
        return $this->title;
    }

    public function setTitle($title): self{   //self-->현재클래스를 나타내기위함 (리턴타입)
        $this->title = $title;
        return $this; //여기서 하는 return은 메소드체인을위해서.
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
        return $this->date;
    }

    public function getStatus(){
        return $this->status;
    }


    public function setStatus($status): self{
        $this->status = $status;
        return $this->status;
    }

    public function getUserId(){
        return $this->user_id;
    }

    public function setUserId($user_id): self{
        $this->user_id = $user_id;
        return $this->user_id;
    }

    public function getOpenclose(){
        return $this->openclose;
    }


    public function setOpenclose($openclose): self{
        $this->openclose = $openclose;
        return $this->openclose;
    }







}
