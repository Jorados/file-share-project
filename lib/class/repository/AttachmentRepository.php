<?php

namespace repository;

use database\DatabaseConnection;
use dataset\Attachment;

class AttachmentRepository{
    public $pdo;

    public function __construct(){
        $this->pdo = DatabaseConnection::getInstance()->getConnection();
    }

    public function getAttachmentsByBoardId($board_id) {
        $query = "SELECT * FROM attachment WHERE board_id = :board_id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':board_id', $board_id, \PDO::PARAM_INT);
        $stmt->execute();
        return array_map(
            function ($attachment){
                return new Attachment($attachment);
            },
            $stmt->fetchAll(\PDO::FETCH_ASSOC)
        );
    }

    public function deleteAttachment($board_id) {
        $deleteQuery = "DELETE FROM attachment WHERE board_id = :board_id";
        $stmt = $this->pdo->prepare($deleteQuery);
        $stmt->bindParam(':board_id', $board_id, \PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function setAttachment(Attachment $attachment){
        $uploadDate = date('Y-m-d H:i:s'); // 현재 날짜와 시간을 가져옵니다.

        $query = "INSERT INTO attachment (filename, filepath, filesize, file_type, upload_date, board_id) 
                  VALUES (:filename, :filepath, :filesize, :filetype, :upload_date, :board_id)";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([
            'filename'=>$attachment->getFilename(),
            'filepath'=>$attachment->getFilepath(),
            'filesize'=>$attachment->getFilesize(),
            'filetype'=>$attachment->getFileType(),
            'upload_date'=>$uploadDate,
            'board_id'=>$attachment->getBoardId()
        ]);
    }
}
?>