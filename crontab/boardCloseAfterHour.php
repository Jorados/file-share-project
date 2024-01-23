<?php
/**
 * 글 생성 후 1일 경과 -> 글 열람 불가 상태로 변경
 * 크론탭
 */


include '/var/www/html/lib/config.php';

use repository\UserRepository;
use repository\BoardRepository;
use repository\InfoRepository;

$userRepository = new UserRepository();
$boardRepository = new BoardRepository();
$infoRepository = new InfoRepository();

// 허용된 글 중에서 1일 이상지난 boardId를 찾는 sql
$boardIds = $boardRepository->getOpencloseBoard();

//허용된 글 중에서 1일 이상지난 board를 열람 불가상태로 변경하는 sql
$boardRepository->updateOpencloseBoard();

foreach ($boardIds as $boardId) {
    // info 테이블에 데이터 삽입
    $infoRepository->addInfoByBoardId($boardId);
}
?>