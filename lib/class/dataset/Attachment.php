<?php

namespace dataset;

class Attachment extends BaseModel{

    protected $attachment_id;
    protected $filename;
    protected $filepath;
    protected $filesize;
    protected $file_type;
    protected $upload_date;
    protected $board_id;

    public function __construct($data = null){
        parent::__construct($data);
    }

    function getAttachmentId(){
        return $this->attachment_id;
    }

    public function setAttachmentId($attachment_id): self{
        $this->attachment_id = $attachment_id;
        return $this;
    }

    public function getFilename(){
        return $this->filename;
    }

    public function setFilename($filename): self{
        $this->filename = $filename;
        return $this;
    }

    public function getFilepath(){
        return $this->filepath;
    }

    public function setFilepath($filepath): self{
        $this->filepath = $filepath;
        return $this;
    }

    public function getFilesize(){
        return $this->filesize;
    }

    public function setFilesize($filesize): self{
        $this->filesize = $filesize;
        return $this;
    }

    public function getFileType(){
        return $this->file_type;
    }

    public function setFileType($file_type): self{
        $this->file_type = $file_type;
        return $this;
    }

    public function getUploadDate(){
        return $this->upload_date;
    }

    public function setUploadDate($upload_date): self{
        $this->upload_date = $upload_date;
        return $this;
    }

    public function getBoardId(){
        return $this->board_id;
    }

    public function setBoardId($board_id): self{
        $this->board_id = $board_id;
        return $this;
    }

}
?>