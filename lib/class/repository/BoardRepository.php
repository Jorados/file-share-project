<?php
/**
 * 데이터모델 Board sql 레포지토리
 */
namespace repository;

use database\DatabaseController;
use dataset\Board;
use dataset\User;

class BoardRepository extends BaseRepository {

    /** 생성자 */
    public function __construct(){
        parent::__construct();
        $this->setTable('board');
    }

    /**
     * 특정 글 read
     * @param int $board_id
     * @return Board
     */
    public function getBoardById($board_id) {
        $data = ['board_id'=>$board_id];
        $stmt = $this->select($this->table, $data);
        return new Board($stmt->fetch(\PDO::FETCH_ASSOC));
    }

    /**
     * 가장 최신 글 1개 조회
     * @return Board
     */
    public function getBoardIdLimit1() {
        $updateQuery = "SELECT board_id FROM board ORDER BY board_id DESC LIMIT 1";
        $stmt = $this->pdo->prepare($updateQuery);
        $stmt->execute();
        return new Board($stmt->fetch(\PDO::FETCH_ASSOC));
    }

    /**
     * 논리적 삭제 상태의 게시글 조회
     * @return array|\dataset\BaseModel[]
     */
    public function getDeleteType(){
        $data = ['delete_type' => 1];
        $stmt = $this->select($this->table,$data);
        return DatabaseController::arrayMapObjects(new Board(), $stmt->fetchAll(\PDO::FETCH_ASSOC));
    }

    /**
     * 특정 글 작성한 유저의 email read
     * @param int $board_id
     * @return User
     */
    public function getBoardUserEmail($board_id) {
        $sql = "SELECT u.email 
            FROM user u 
            WHERE u.user_id IN (
                SELECT b.user_id 
                FROM board b 
                WHERE b.board_id = :board_id
            )";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':board_id', $board_id, \PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return new User($result);
    }

    /**
     * 매개변수(검색 조건) 고려하여 board의 count read
     * @param int|null $permission
     * @param String|null $searchType
     * @param String|null $searchQuery
     * @param int|null $userId
     * @param String $status
     * @return mixed
     */
    public function getTotalBoardCount($permission = null, $searchType = null, $searchQuery = null, $userId = null, $status) {
        $whereClause = $this->buildWhereClause($permission, $searchType, $searchQuery, $userId, $status);
        $query = "SELECT COUNT(*) as total FROM board WHERE {$whereClause['strArr']};";
        $stmt = $this->pdo->prepare($query);

        $stmt->execute($whereClause['paramArr']);
        $result = $stmt->fetch();
        return $result['total'];
    }

    /**
     * 매개변수(검색 조건) 고려하여 특정 페이지 글 read
     * @param int $offset
     * @param int $items_per_page
     * @param String $order
     * @param int|null $permission
     * @param String|null $searchType
     * @param String|null $searchQuery
     * @param int|null $userId
     * @param String $status
     * @return array|\dataset\BaseModel[]
     */

    public function getBoardsByPage($offset, $items_per_page, $order, $permission = null, $searchType = null, $searchQuery = null, $userId = null, $status) {
        $orderClause = ($order === 'oldest') ? 'ORDER BY date ASC' : 'ORDER BY date DESC';
        $whereClause = $this->buildWhereClause($permission, $searchType, $searchQuery, $userId, $status);
        $query = "SELECT * FROM board WHERE {$whereClause['strArr']} {$orderClause} LIMIT :offset, :items_per_page;";
        $stmt = $this->pdo->prepare($query);

        $stmt->execute(array_merge($whereClause['paramArr'],[':offset'=>$offset],[':items_per_page'=>$items_per_page]));
        return DatabaseController::arrayMapObjects(new Board(), $stmt->fetchAll(\PDO::FETCH_ASSOC));
    }

    /**
     * 검색 조건 sql 생성 + 바인딩 변수 미리 추가
     * @param int|null $permission
     * @param String|null $searchType
     * @param String|null $searchQuery
     * @param int|null $userId
     * @return array
     */

    private function buildWhereClause($permission = null, $searchType = null, $searchQuery = null, $userId = null, $status) {
        $whereConditions = [];
        $paramArr = [];

        $whereConditions[] = "status = :status";
        $paramArr[':status'] = $status;

        if ($permission !== null) {
            $whereConditions[] = "openclose = :permission";
            $paramArr[':permission'] = $permission;
        }

        if ($userId !== null) {
            $whereConditions[] = "user_id = :userId";
            $paramArr[':userId'] = $userId;
        }

        // 복수 행 처리가 안 되어 있음.
        if ($searchType !== null && $searchQuery !== null) {
            // 검색 타입이 'username'이면 사용자 이름으로 검색
            if ($searchType === 'username') {
                // 사용자 이름으로 검색할 때는 해당 사용자의 user_id를 찾아 조건에 추가
                $userIdsByUserName = $this->getUserIdByUsername($searchQuery);

                if (!empty($userIdsByUserName)) {
                    // IN 연산자를 사용하여 여러 값을 비교
                    $inConditions = [];
                    foreach ($userIdsByUserName as $index => $userId) {
                        $inConditions[] = ":userIdByUserName{$index}";
                        $paramArr[":userIdByUserName{$index}"] = $userId->getUserId();
                    }
                    $whereConditions[] = "user_id IN (" . implode(", ", $inConditions) . ")";
                } else {
                    // 사용자를 찾지 못한 경우에는 빈 결과를 반환
                    return ['paramArr' => [], 'strArr' => '1 = 0'];
                }
            } else {
                // 다른 검색 타입인 경우 (예: title, content 등)는 LIKE 검색을 수행
                $whereConditions[] = "$searchType LIKE :search_query";
                $paramArr[':search_query'] = "%{$searchQuery}%";
            }
        }


        $whereConditions[] = "delete_type = :delete_type";
        $paramArr[':delete_type'] = 0;

        $strArr = !empty($whereConditions) ? implode(" AND ", $whereConditions) : "";
        return ['paramArr' => $paramArr, 'strArr' => $strArr];
    }

    /**
     * 특정 글 delete
     * @param int $board_id
     * @return bool
     */
    public function deleteBoardById($board_id) {
        $data = ['board_id'=>$board_id];
        $this->delete($this->table,$data);
    }

    /**
     * 글 create
     * @param Board $board
     */
    public function addBoard(Board $board) {
        $data = [
            'title'=>$board->getTitle(),
            'content'=>$board->getContent(),
            'date'=>$board->getDate(),
            'user_id'=>$board->getUserId()
        ];
        $this->insert($this->table, $data);
    }

    /**
     * 글 update
     * @param Board $board
     */
    public function updateBoardPermission(Board $board) {
        $set = [
            'openclose' => $board->getOpenclose(),
            'openclose_time' => date('Y-m-d H:i:s')
        ];
        $where = ['board_id' => $board->getBoardId()];

        $this->update($this->table, $set, $where);
    }

    /**
     * 관리자 글 생성
     * @param Board $board
     */
    public function adminCreateBoard(Board $board) {
        $data = [
            'title'=>$board->getTitle(),
            'content'=>$board->getContent(),
            'date'=>$board->getDate(),
            'user_id'=>$board->getUserId(),
            'status'=>$board->getStatus()
        ];
        $this->insert($this->table, $data);
    }

    /**
     * 논리적 글 삭제
     * @param $board_id
     */
    public function updateDeleteType($board_id){
        $set  = ['delete_type' => 1];
        $where = ['board_id' => $board_id];
        $this->update($this->table, $set, $where);
    }

    /**
     * 허용된 글 중에서 1일 이상지난 board read
     * @return array|\dataset\BaseModel[]
     */
    public function getOpencloseBoard(){
        $sql = "SELECT * FROM board WHERE openclose = 'open' AND openclose_time <= DATE_SUB(NOW(), INTERVAL 1 DAY) AND status = 'normal'";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return DatabaseController::arrayMapObjects(new Board(), $stmt->fetchAll(\PDO::FETCH_ASSOC));
    }

    /**
     * 허용된 글 중에서 허용된 시점을 기반으로 1일 이상지난 board를 열람 불가상태로 update
     */
    public function updateOpencloseBoard() {
        $sql = "UPDATE board SET openclose = 'close' WHERE openclose = 'open' AND openclose_time <= DATE_SUB(NOW(), INTERVAL 1 DAY) AND status = 'normal'";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
    }


    public function getUserIdByUsername($username) {
        $query = "SELECT user_id FROM user WHERE username LIKE :username";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([':username' => "%{$username}%"]);
        return DatabaseController::arrayMapObjects(new User(), $stmt->fetchAll(\PDO::FETCH_ASSOC));
    }

    /**
     *  대기상태에서 하루 이상 경과 시 자동 반려 -> 보류
     */
//    public function updateOpencloseBoardToWait() {
//        $sql = "UPDATE board SET openclose = 'close' WHERE openclose = 'wait' AND date <= DATE_SUB(NOW(), INTERVAL 1 DAY) AND status = 'normal'";
//        $stmt = $this->pdo->prepare($sql);
//        $stmt->execute();
//    }
}
?>