<?php
session_start();

include '/var/www/html/lib/config.php';

use repository\UserRepository;
use repository\BoardRepository;
use repository\AttachmentRepository;
use repository\CommentRepository;
use log\PostLogger;

$board_id = isset($_GET['board_id']) ? $_GET['board_id'] : null;

$boardRepository = new BoardRepository();
$attachmentRepository = new AttachmentRepository();
$commentRepository = new CommentRepository();
$userRepository = new UserRepository();

$board = $boardRepository->getBoardById($board_id);
$attachments = $attachmentRepository->getAttachmentsByBoardId($board_id);

// 댓글 조회
$comments = $commentRepository -> getCommentsByBoardId($board_id);

// 글 상세 조회 로그
$logger = new PostLogger();
$email = $_SESSION['email'];
$title = $board->getTitle();
$status = $board->getStatus();
$logger->readPost($_SERVER['REQUEST_URI'], $email, $status, $title);
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
<div class="container mt-5">
    <!-- 게시글 상세 정보 -->
    <div class="card mx-auto mb-5" style="max-width: 1000px;">
        <div class="card-header bg-dark text-white" style="max-height: 90px;">
            <h3 class="text-center">공지 상세 조회</h3>
        </div>
        <div class="card-body">
            <p class="card-text">제목 : <?= $board->getTitle(); ?></p>
            <p class="card-text">내용 : <?= $board->getContent(); ?></p>
            <ul>
                <?php
                $filepath = '/var/www/html/file/uploads/';
                if (empty($attachments)) {
                    echo '<li>첨부파일이 없습니다.</li>';
                } else {
                    foreach ($attachments as $attachment) {
                        if (file_exists($filepath)) {
                            echo '<li><a href="/file/download.php?file=' . urlencode($attachment->getFilename()) . '">' . $attachment->getFilename() . '</a></li>';
                        }
                    }
                }
                ?>
            </ul>
            <p class="card-text">작성일 : <?php echo date('Y-m-d', strtotime($board->getDate())); ?></p>
            <p class="card-text">열람 권한 : <?php echo '허용'; ?></p>
        </div>
    </div>


    <div class="card mx-auto mb-5" style="max-width: 1000px;">
        <div class="card-body">
            <label for="comment_content">댓글 내용</label>
            <?php foreach ($comments as $comment): ?>
                <div class="card mb-2">
                    <div class="card-body d-flex justify-content-between"> <!-- d-flex와 justify-content-between 추가 -->
                        <div>
                            <p class="card-text"><?= $comment->getContent(); ?></p>
                            <small class="text-muted">
                                작성자:
                                <?= $userRepository->getUserEmailById($comment->getUserId())->getEmail(); ?>
                            </small>
                        </div>
                        <div class="text-right"> <!-- text-right 추가 -->
                            <small class="text-muted"><?= date('Y-m-d', strtotime($comment->getDate())); ?></small>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
</body>
<footer>
    <?php include '/var/www/html/includes/footer.php'?>
</footer>
</html>