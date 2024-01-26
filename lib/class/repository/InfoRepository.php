<?php
/**
 * 데이터모델 Info sql 레포지토리
 */
namespace repository;

use dataset\Info;

class InfoRepository extends BaseRepository {
    /**
     * InfoRepository constructor.
     */
    public function __construct(){
        parent::__construct('info');
    }

    /**
     * 특정 글 가장 최신 Info read
     * @param int $board_id
     * @return Info
     */
    public function getLatestInfoByBoardId($board_id) {
        $infoQuery = "SELECT * FROM {$this->table} WHERE board_id = :board_id ORDER BY info_id DESC LIMIT 1";
        $stmt = $this->pdo->prepare($infoQuery);
        $stmt->execute(['board_id'=>$board_id]);
        return new Info($stmt->fetch(\PDO::FETCH_ASSOC));
    }

    /**
     * Info create
     * @param Info $info
     */
    public function addInfo(Info $info) {
        $data = [
            'reason_content' => $info->getReasonContent(),
            'user_id' => $info->getUserId(),
            'board_id' => $info->getBoardId(),
            'date' => date('Y-m-d H:i:s') // 현재 날짜와 시간을 포맷에 맞춰 전달
        ];
        $this->insert($this->table, $data);
    }

    /**
     * crontab Info create
     * @param int $board_id
     */
    public function addInfoByBoardId($board_id){
        $data = [
            'date' => date('Y-m-d H:i:s'), // Use the current date and time
            'reason_content' => '이 게시글은 일정 시간 이상 지나서 자동 반려됩니다.',
            'board_id' => $board_id,
            'user_id' => 2
        ];
        $this->insert($this->table,$data);
    }
}
?>