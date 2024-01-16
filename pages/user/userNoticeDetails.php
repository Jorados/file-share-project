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
try {
    $comments = $commentRepository -> getCommentByBoardId($board_id);
    // 각 댓글의 작성자 이메일을 가져옵니다.
    foreach ($comments as &$comment) {
        $user_id = $comment['user_id'];
        $user = $userRepository->getUserEmailById($user_id);
        $comment['user_email'] = $user->getEmail();
    }
} catch (PDOException $e) {
    echo "댓글 조회 중 오류가 발생했습니다: " . $e->getMessage();
}

// 글 상세 조회 로그
$logger = new PostLogger();
$email = $_SESSION['email'];
$title = $board['title'];
$status = $board['status'];
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


    <div class="card mx-auto mb-5" style="max-width: 1000px;">
        <div class="card-body">
            <label for="comment_content">댓글 내용</label>
            <?php foreach ($comments as $comment): ?>
                <div class="card mb-2">
                    <div class="card-body d-flex justify-content-between"> <!-- d-flex와 justify-content-between 추가 -->
                        <div>
                            <p class="card-text"><?php echo $comment['content']; ?></p>
                            <small class="text-muted">작성자: <?php echo $comment['user_email']; ?></small>
                        </div>
                        <div class="text-right"> <!-- text-right 추가 -->
                            <small class="text-muted"><?php echo date('Y-m-d', strtotime($comment['date'])); ?></small>
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