<?php
/**
 * 논리적 상태 게시글 -> 매일 자정 12시에 자동 삭제
 * 크론탭
 */

include_once '/var/www/html/lib/config.php';

use repository\BoardRepository;
use repository\AttachmentRepository;

$boardRepository = new BoardRepository();
$attachmentRepository = new AttachmentRepository();

// 논리적 삭제 상태의 게시글 조회
$boards = $boardRepository->getDeleteType();

foreach ($boards as $board){
    // board 와 attachment 함께 삭제
    $boardRepository->deleteBoardById($board->getBoardId());
    $attachmentRepository->deleteAttachment($board->getBoardId());
}

?>