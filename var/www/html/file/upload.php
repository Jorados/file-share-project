<?php
//// 단일 파일 처리
$uploadDir = '/var/www/html/file/uploads/';

if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

if ($_FILES['file']['error'] === 0 && $_FILES['file']['size'] <= 100 * 1024 * 1024) { // 100MB 제한
    $fileName = $_FILES['file']['name'];
    $fileTmpPath = $_FILES['file']['tmp_name'];
    $destination = $uploadDir . $fileName;

    // 파일을 올바른 위치로 이동
    if (move_uploaded_file($fileTmpPath, $destination)) {
      echo json_encode([
            'error' => false,
            'totalCount' => $_POST['totalCount'],
            'status' => '2xx',
            'content' => $fileName
        ]);
    }
    else {
        echo json_encode([
            'error' => true,
            'status' => '5xx',
            'fileName' => $fileName
        ]);
    }
} else {
    // 업로드 에러 발생
    echo json_encode([
        'error' => true,
        'status' => '5xx',
        'fileName' => "Upload error for file:" . $_FILES['file']['name']
    ]);
}

include '/var/www/html/database/DatabaseConnection.php';
include '/var/repository/boardRepository.php';
include '/var/repository/attachmentRepository.php';

//위에서 파일 처리를 한 후에, 최근 boardList LIMIT 1로 생성된 board_id를 불러와서
$dbConnection = new DatabaseConnection();
$pdo = $dbConnection->getConnection();

$boardRepository = new BoardRepository($pdo);
$board_id = $boardRepository -> getBoardIdLimit1();

$attachmentRepository = new AttachmentRepository($pdo);
$attachmentRepository->setAttachment($board_id, $_FILES['file']['name'], $_FILES['file']['size'], $_FILES['file']['type'], $destination);
?>
