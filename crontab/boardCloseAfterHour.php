<?php
/**
 * 1. 글 열람 허용 후 1일 경과 -> 글 열람 불가 상태로 변경
 * 2. 글 대기 상태 1일 경과 -> 글 열람 불가 상태로 변경 -> 보류
 * 크론탭
 */

include_once '/var/www/html/lib/config.php';

use repository\UserRepository;
use repository\BoardRepository;
use repository\InfoRepository;

$userRepository = new UserRepository();
$boardRepository = new BoardRepository();
$infoRepository = new InfoRepository();

// 허용된 글 중에서 1일 이상지난 boardId를 찾는 sql
$boardIds = $boardRepository->getOpencloseBoard();

//허용된 글 중에서 허용된 시간으로 부터 24시간 이상지난 board를 열람 불가상태로 변경하는 update sql
$boardRepository->updateOpencloseBoard();

foreach ($boardIds as $boardId) {
    // info 테이블에 데이터 삽입
    $infoRepository->addInfoByBoardId($boardId);
}

//// 대기 상태 1일 초과 -> 글 열람 불가 상태로 변경
//$boardRepository->updateOpencloseBoardToWait();

?>