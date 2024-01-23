<?php
/**
 * 업로드 파일 다운로드 액션
 */

session_start();

include '/var/www/html/lib/config.php';

use log\PostLogger;

// 세션 값체크
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: /index.php");
    exit;
}

$logger = new PostLogger();

//GET 요청에서 파일 이름을 가져옵니다.
if (isset($_GET['file'])) {
    $filename = $_GET['file'];

    // 파일 이름에서 연,월,확장자를 구분하여 디렉토리를 세팅해줘야한다.
    $dot = strpos($filename, '.');
    $under = strrpos($filename, '_');

    $extension = substr($filename, $dot + 1);
    $yearMonth = substr($filename, $under + 1, 7); // 예: 2024-01

    // 디렉토리를 생성합니다.
    $baseDirectory = '/var/www/html/file/uploads/';
    $yearDirectory = $baseDirectory . substr($yearMonth, 0, 4) . '/';
    $monthDirectory = $yearDirectory . substr($yearMonth, 5, 2) . '/';
    $extensionDirectory = $monthDirectory . $extension . '/';

    // 파일의 실제 경로를 설정합니다.
    $filepath = $extensionDirectory . $filename;

    // 파일이 실제로 존재하는지 확인합니다.
    if (file_exists($filepath)) {
        // 파일 타입을 확인하여 적절한 헤더를 설정합니다.
        $filetype = mime_content_type($filepath);

        // 필요한 헤더를 설정하여 파일을 다운로드합니다.
        header('Content-Description: File Transfer');
        header('Content-Type: ' . $filetype);
        header('Content-Disposition: attachment; filename=' . basename($filename));
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filepath));

        // 파일을 읽어서 출력합니다.
        readfile($filepath);

        // 파일 다운 로그
        $email = $_SESSION['email'];
        $logger->downloadFile($_SERVER['REQUEST_URI'], $email, $filename);
        exit;
    } else {
        // 파일이 존재하지 않는 경우 에러 메시지를 출력합니다.
        die('파일을 찾을 수 없습니다.');
    }
} else {
    // 파일 이름이 제공되지 않은 경우 에러 메시지를 출력합니다.
    die('잘못된 요청입니다.');
}
?>

