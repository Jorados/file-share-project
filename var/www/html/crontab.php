<?php
include '/var/www/html/database/DatabaseConnection.php';

$dbConnection = new DatabaseConnection();
$pdo = $dbConnection->getConnection();

// 변경된 board_id 가져오기
$sql = "SELECT board_id FROM board WHERE openclose = 1 AND date <= DATE_SUB(NOW(), INTERVAL 1 DAY) AND status = 'normal'";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$boardIds = $stmt->fetchAll(PDO::FETCH_COLUMN);

// 현재 시간보다 1시간 이전의 게시글을 찾기 위한 SQL 쿼리
$sql = "UPDATE board SET openclose = 0 WHERE openclose = 1 AND date <= DATE_SUB(NOW(), INTERVAL 1 DAY) AND status = 'normal'";
$stmt = $pdo->prepare($sql);
$stmt->execute();

foreach ($boardIds as $boardId) {
    // info 테이블에 데이터 삽입
    $insertSql = "INSERT INTO info (date, reason_content, board_id, user_id) VALUES (NOW(), '이 게시글은 일정 시간 이상 지나서 자동 반려됩니다.', :board_id, 2)";
    $stmt = $pdo->prepare($insertSql);
    $stmt->bindParam(':board_id', $boardId, PDO::PARAM_INT);
    $stmt->execute();
}
?>