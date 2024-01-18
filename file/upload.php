<?php
//// 단일 파일 처리
include '/var/www/html/lib/config.php';

use repository\BoardRepository;
use repository\AttachmentRepository;
use dataset\Attachment;

$attachmentRepository = new AttachmentRepository();
$boardRepository = new BoardRepository();

$uploadDir = '/var/www/html/file/uploads/';

if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

if ($_FILES['file']['error'] === 0 && $_FILES['file']['size'] <= 100 * 1024 * 1024) { // 100MB 제한
//    $fileName = $_FILES['file']['name'];
//    $fileTmpPath = $_FILES['file']['tmp_name'];
//    $destination = $uploadDir . $fileName;

    $fileName = $_FILES['file']['name'];
    $fileTmpPath = $_FILES['file']['tmp_name'];
    $extension = pathinfo($fileName, PATHINFO_EXTENSION);  // 파일 확장자 추출

    // 새로운 파일 이름 생성 (예: 현재 파일 이름에 현재 시간을 추가)
    $newFileName = pathinfo($fileName, PATHINFO_FILENAME) . '_' . date('Y-m-d H:i:s')  . '.' . $extension;

    $destination = $uploadDir . $newFileName;

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


// << 개선해야할 점 >>

// 1. 중복파일을 저장하지 않기 때문에 추후 겹치는 파일에 대한 문제.
// 해결방법 --> 각 파일의 이름에 고유 식별숫자를 부여하여 attachment 파일에 저장할 예정 , 해당 파일을 리스트로 보여줄땐 식별 부분은 생략 예정

// 2. 로그아웃한(권한 없는) 계정에서 파일에대한 요청이 오면 다운로드가 진행되는 문제.
// 해결방법 --> 헤더에서 라우팅, 세션처리를 확실하게 하자.

$board = $boardRepository -> getBoardIdLimit1();

$attachment = new Attachment([
    'board_id'=>$board->getBoardId(),
    'filename'=>$newFileName,
    'filesize'=>$_FILES['file']['size'],
    'file_type'=>$_FILES['file']['type'],
    'filepath'=>$destination
    ]);
$attachmentRepository->setAttachment($attachment);
?>
