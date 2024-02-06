<?php
/**
 * 논리적 상태 게시글 -> 매일 자정 12시에 자동 삭제 -> 물리적 삭제
 * 실제 디렉토리 파일도 삭제.
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
    // 삭제할 attachment 조회
    $attachments = $attachmentRepository->getAttachmentsByBoardId($board->getBoardId());

    foreach ($attachments as $attachment){
        $filepath = $attachment->getFilepath();

        // 파일이 존재하는지 확인 후 삭제
        if (file_exists($filepath)) {
            unlink($filepath);
        }
    }

    // board 삭제
    $boardRepository->deleteBoardById($board->getBoardId());
    // attachment 테이블 삭제
    $attachmentRepository->deleteAttachment($board->getBoardId());
}

?>