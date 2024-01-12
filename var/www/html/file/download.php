<?php
//$file = isset($_GET['file']) ? urldecode($_GET['file']) : '';
//$filePath = '/var/www/html/file/uploads/' . basename($file);
//
//if (file_exists($filePath) && is_file($filePath)) {
//    header('Content-Description: File Transfer');
//    header('Content-Type: application/octet-stream');
//    header('Content-Disposition: attachment; filename="' . basename($filePath) . '"');
//    header('Expires: 0');
//    header('Cache-Control: must-revalidate');
//    header('Pragma: public');
//    header('Content-Length: ' . filesize($filePath));
//    readfile($filePath);
//    exit;
//} else {
//    die('File not found.');
//}
//?>

<?php
// download.php
session_start();
include '/var/access_logs/PostLogger.php';
$logger = new PostLogger();

//GET 요청에서 파일 이름을 가져옵니다.
if (isset($_GET['file'])) {

    error_log(E_ALL);
    ini_set("display_errors", 1);

    $filename = $_GET['file'];

    // 파일의 실제 경로를 설정합니다.
    $filepath = '/var/www/html/file/uploads/' . $filename;

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

