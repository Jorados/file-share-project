<?php
/**
 * 데이터모델 Attachment sql 레포지토리
 */

namespace repository;

use database\DatabaseController;
use dataset\Attachment;

class AttachmentRepository extends BaseRepository {

    /** 생성자 */
    public function __construct(){
        parent::__construct();
        $this->setTable('attachment');
    }

    /**
     * @param int $board_id
     * @return Attachment[]
     * 특정 게시글의 업로드 파일 조회
     */
    public function getAttachmentsByBoardId($board_id) {
        $data = ['board_id'=>$board_id];
        $stmt = $this->select($this->table,$data);
        return DatabaseController::arrayMapObjects(new Attachment(), $stmt->fetchAll(\PDO::FETCH_ASSOC));
    }

    /**
     * @param int $board_id
     * @return bool
     * 삭제
     */
    public function deleteAttachment($board_id) {
        $data = ['board_id'=>$board_id];
        $this->delete($this->table,$data);
    }

    /**
     * @param $attachment
     * 업로드 파일 저장
     */
    public function setAttachment($attachment){
        $uploadDate = date('Y-m-d H:i:s'); // 현재 날짜와 시간을 가져옵니다.

        $query = "INSERT INTO attachment (filename, filepath, filesize, file_type, upload_date, board_id) 
                  VALUES (:filename, :filepath, :filesize, :filetype, :uploaddate, :boardid)";

        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':filename', $attachment->getFilename(), \PDO::PARAM_STR);
        $stmt->bindParam(':filepath', $attachment->getFilepath(), \PDO::PARAM_STR);
        $stmt->bindParam(':filesize', $attachment->getFilesize(), \PDO::PARAM_INT);
        $stmt->bindParam(':filetype', $attachment->getFileType(), \PDO::PARAM_STR);
        $stmt->bindParam(':uploaddate', $uploadDate, \PDO::PARAM_STR);
        $stmt->bindParam(':boardid', $attachment->getBoardId(), \PDO::PARAM_INT);
        $stmt->execute();
    }
}
?>