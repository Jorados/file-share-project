<?php
/**
 * 데이터모델 Info sql 레포지토리
 */
namespace repository;

use dataset\Info;

class InfoRepository extends BaseRepository {

    /**
     * 특정 글 가장 최신 Info read
     * @param int $board_id
     * @return Info
     */
    public function getLatestInfoByBoardId($board_id) {
        $infoQuery = "SELECT * FROM info WHERE board_id = :board_id ORDER BY info_id DESC LIMIT 1";
        $stmt = $this->pdo->prepare($infoQuery);
        $stmt->bindParam(':board_id', $board_id, \PDO::PARAM_INT);
        $stmt->execute();
        return new Info($stmt->fetch(\PDO::FETCH_ASSOC));
    }

    /**
     * Info create
     * @param Info $info
     */
    public function addInfo(Info $info) {
        $insertQuery = "INSERT INTO info (reason_content, date, user_id, board_id) VALUES (:reason_content, NOW(), :user_id, :board_id)";
        $stmt = $this->pdo->prepare($insertQuery);
        $stmt->execute([
            'reason_content'=>$info->getReasonContent(),
            'user_id'=>$info->getUserId(),
            'board_id'=>$info->getBoardId()
        ]);
    }

    /**
     * crontab Info create
     * @param int $board_Id
     */
    public function addInfoByBoardId($board_Id){
        $insertSql = "INSERT INTO info (date, reason_content, board_id, user_id) VALUES (NOW(), '이 게시글은 일정 시간 이상 지나서 자동 반려됩니다.', :board_id, 2)";
        $stmt = $this->pdo->prepare($insertSql);
        $stmt->bindParam(':board_id', $board_Id, PDO::PARAM_INT);
        $stmt->execute();
    }
}
?>