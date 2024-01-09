<?php
session_start();

include '/var/www/html/database/DatabaseConnection.php';
include '/var/repository/boardRepository.php';
include '/var/repository/attachmentRepository.php';

// 게시글 ID를 URL 파라미터에서 가져옵니다. // isset() -> 값이 할당되었는지 확인하는 함수
$board_id = isset($_GET['board_id']) ? $_GET['board_id'] : null;
if (!$board_id) {
    die("게시글 ID가 제공되지 않았습니다.");
}

$dbConnection = new DatabaseConnection();
$pdo = $dbConnection->getConnection();

$boardRepository = new BoardRepository($pdo);
$board = $boardRepository->getBoardById($board_id);

$attachmentRepository = new AttachmentRepository($pdo);
$attachments = $attachmentRepository->getAttachmentsByBoardId($board_id);
?>


<!DOCTYPE html>
<html>
<head>
    <meta charset='utf-8'>
    <title>게시글 상세보기</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include '/var/www/html/includes/header.php'?>
<?php include '/var/www/html/includes/userNavibar.php'?>
<div class="container mt-5">
    <!-- 게시글 상세 정보 -->
    <div class="card mx-auto mb-5" style="max-width: 500px;">
        <div class="card-header bg-dark text-white" style="max-height: 90px;">
            <h3 class="text-center">공지 상세 조회</h3>
        </div>
        <div class="card-body">
            <p class="card-text">제목 : <?php echo $board['title']; ?></p>
            <p class="card-text">내용 : <?php echo $board['content']; ?></p>
            <ul>
                <?php
                $filepath = '/var/www/html/file/uploads/';
                if (empty($attachments)) {
                    echo '<li>첨부파일이 없습니다.</li>';
                } else {
                    foreach ($attachments as $attachment) {
                        if (file_exists($filepath)) {
                            echo '<li><a href="/file/download.php?file=' . urlencode($attachment['filename']) . '">' . $attachment['filename'] . '</a></li>';
                        }
                    }
                }
                ?>
            </ul>
            <p class="card-text">작성일 : <?php echo date('Y-m-d', strtotime($board['date'])); ?></p>
            <p class="card-text">열람 권한 : <?php echo '허용'; ?></p>
        </div>
    </div>
</div>
</body>
<footer>
    <?php include '/var/www/html/includes/footer.php'?>
</footer>
</html>