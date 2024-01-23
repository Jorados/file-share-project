<?php

/**
 * 파일 업로드 액션
 */

include '/var/www/html/lib/config.php';

use repository\BoardRepository;
use repository\AttachmentRepository;
use dataset\Attachment;

$attachmentRepository = new AttachmentRepository();
$boardRepository = new BoardRepository();

$uploadDirBase = '/var/www/html/file/uploads/';
$uploadDirYearMonth = $uploadDirBase . date('Y/m/'); // 년월별 디렉토리 경로
$extension = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION); // 파일 확장자 추출

// 디렉토리 생성
if (!file_exists($uploadDirYearMonth)) {
    mkdir($uploadDirYearMonth, 0777, true);
}

// 확장자별 디렉토리 경로
$uploadDirExtension = $uploadDirYearMonth . $extension . '/';

// 디렉토리 생성
if (!file_exists($uploadDirExtension)) {
    mkdir($uploadDirExtension, 0777, true);
}

// 새로운 파일 이름 생성
$newFileName = pathinfo($_FILES['file']['name'], PATHINFO_FILENAME) . '_' . date('Y-m-d H:i:s') . '.' . $extension;
$destination = $uploadDirExtension . $newFileName;

if ($_FILES['file']['error'] === 0 && $_FILES['file']['size'] <= 100 * 1024 * 1024) { // 100MB 제한
    $fileTmpPath = $_FILES['file']['tmp_name'];

    // 파일을 올바른 위치로 이동
    if (move_uploaded_file($fileTmpPath, $destination)) {
        echo json_encode([
            'error' => false,
            'totalCount' => $_POST['totalCount'],
            'status' => '2xx',
            'content' => $newFileName,
        ]);
    } else {
        echo json_encode([
            'error' => true,
            'status' => '5xx',
            'fileName' => $newFileName,
        ]);
    }
} else {
    // 업로드 에러 발생
    echo json_encode([
        'error' => true,
        'status' => '5xx',
        'fileName' => "파일 업로드 에러 : " . $_FILES['file']['name'],
    ]);
}

$board = $boardRepository->getBoardIdLimit1();

$attachment = new Attachment([
    'board_id' => $board->getBoardId(),
    'filename' => $newFileName,
    'filesize' => $_FILES['file']['size'],
    'file_type' => $_FILES['file']['type'],
    'filepath' => $destination,
]);
$attachmentRepository->setAttachment($attachment);
?>
