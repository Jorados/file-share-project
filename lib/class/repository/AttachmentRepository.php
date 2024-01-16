<?php

namespace repository;

use database\DatabaseConnection;

class AttachmentRepository{
    public $pdo;

    public function __construct(){
        $this->pdo = DatabaseConnection::getInstance()->getConnection();
    }

    public function setAttachment($board_id, $fileName, $fileSize, $fileType, $filePath){
        $uploadDate = date('Y-m-d H:i:s'); // 현재 날짜와 시간을 가져옵니다.

        $query = "INSERT INTO attachment (filename, filepath, filesize, file_type, upload_date, board_id) 
                  VALUES (:filename, :filepath, :filesize, :filetype, :uploaddate, :boardid)";

        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':filename', $fileName, \PDO::PARAM_STR);
        $stmt->bindParam(':filepath', $filePath, \PDO::PARAM_STR);
        $stmt->bindParam(':filesize', $fileSize, \PDO::PARAM_INT);
        $stmt->bindParam(':filetype', $fileType, \PDO::PARAM_STR);
        $stmt->bindParam(':uploaddate', $uploadDate, \PDO::PARAM_STR);
        $stmt->bindParam(':boardid', $board_id, \PDO::PARAM_INT);
        $stmt->execute();
    }

    public function getAttachmentsByBoardId($board_id) {
        $query = "SELECT * FROM attachment WHERE board_id = :board_id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':board_id', $board_id, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getAttachmentFilePath($attachment_id) {
        $query = "SELECT filepath FROM attachment WHERE attachment_id = :attachment_id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':attachment_id', $attachment_id, \PDO::PARAM_INT);
        $stmt->execute();

        $result = $stmt->fetch(\PDO::FETCH_ASSOC);

        return $result['filepath'] ?? null;
    }

    public function deleteAttachment($board_id) {
        $deleteQuery = "DELETE FROM attachment WHERE board_id = :board_id";
        $stmt = $this->pdo->prepare($deleteQuery);
        $stmt->bindParam(':board_id', $board_id, \PDO::PARAM_INT);
        return $stmt->execute();
    }
}
?>