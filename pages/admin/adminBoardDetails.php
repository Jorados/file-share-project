<?php
session_start();
include '/var/www/html/lib/config.php';

use repository\UserRepository;
use repository\BoardRepository;
use repository\AttachmentRepository;
use repository\InfoRepository;
use repository\CommentRepository;
use log\PostLogger;
use mail\SendMail;

$boardRepository = new BoardRepository();
$infoRepository = new InfoRepository();
$userRepository = new UserRepository();
$commentRepository = new CommentRepository();
$attachmentRepository = new AttachmentRepository();
$mailSender = new SendMail();
$logger = new PostLogger();

$board_id = isset($_GET['board_id']) ? $_GET['board_id'] : null;
if (!$board_id) die("게시글 ID가 제공되지 않았습니다.");

// 글 상세 조회 로그  추가
$board = $boardRepository -> getBoardByid($board_id);
$status = $board->getStatus();
$title = $board->getTitle();
$email = $_SESSION['email'];
$logger->readPost($_SERVER['REQUEST_URI'], $email, $status, $title);

$user_id = $board->getUserId();
$user = $userRepository -> getUserById($user_id);

$comments = $commentRepository -> getCommentsByBoardId($board_id);
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
<div class="container mt-5">

    <div class="card mx-auto mb-5" style="max-width: 1000px;">
        <div class="card-header bg-dark text-white" style="max-height: 90px;">
            <h3 class="text-center">글 상세 조회</h3>
        </div>
        <div class="card-body">
            <p class="card-text">제목 : <?= htmlspecialchars($board->getTitle(), ENT_QUOTES, 'UTF-8'); ?></p>
            <p class="card-text">내용 : <?= htmlspecialchars($board->getContent(), ENT_QUOTES, 'UTF-8'); ?></p>
            <p class="card-text">파일목록
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
            </p>
            <p class="card-text">작성자 : <?= $user->getUsername(); ?></p>
            <p class="card-text">작성자 이메일 : <?= $user->getEmail(); ?></p>
            <p class="card-text">작성일 : <?= date('Y-m-d', strtotime($board->getDate())); ?></p>
            <p class="card-text">열람 권한 : <?= $board->getOpenclose() == 0 ? '불가' : '허용'; ?></p>

            <form action="/action/boardAuthorityChange.php" method="post"  class="mt-3" id="authorityForm">
                <label for="reason_content">사유</label>
                <textarea name="reason_content" id="reason_content" rows="3" class="form-control" placeholder="열람 권한 변경의 사유를 입력하세요." required></textarea>
                <input type="hidden" name="board_id" value="<?= $board->getBoardId(); ?>">

                <?php if ($board->getOpenclose() == 0): ?>
                    <input type="hidden" name="change_permission" value="1">
                    <button type="button" name="change_permission" value="1" class="btn btn-primary mt-3" onclick="submitBoardAuthority()">열람 허용</button>
                <?php else: ?>
                    <input type="hidden" name="change_permission" value="0">
                    <button type="button" name="change_permission" value="0" class="btn btn-primary mt-3" onclick="submitBoardAuthority()">열람 불가</button>
                <?php endif; ?>
            </form>

            <div class="row mt-3">
                <div class="col-md-6">
                    <form action="/action/deleteForm.php" method="post"  class="d-inline-block" id="deleteForm">
                        <input type="hidden" name="board_id" value="<?= $board->getBoardId() ?>">
                        <input type="button" name="delete_post" value="삭제" class="btn btn-danger" onclick="submitDeleteForm()">
                    </form>
<!--                    <a href="boardEdit.php?id=--><?php //echo $board['board_id']; ?><!--" class="btn btn-warning">수정</a>-->
                </div>
            </div>
        </div>
    </div>

    <div class="card mx-auto mb-5 " style="max-width: 1000px";>
        <div class="card-body">
            <form action="/action/createComment.php" method="post" id="createCommentForm">
                <div class="form-group">
                    <label for="content">댓글 내용</label>
                    <textarea name="content" id="content" rows="3" class="form-control" required></textarea>
                </div>
                <input type="hidden" name="board_id" value="<?= $board->getBoardId(); ?>">
                <button type="button" name="submit_comment" class="btn btn-primary" onclick="submitCommentForm()">댓글 작성</button>
            </form>
        </div>
    </div>

    <div class="card mx-auto mb-5" style="max-width: 1000px;">
        <div class="card-body">
            <label for="comment_content">댓글 내용</label>
            <?php foreach ($comments as $comment): ?>
                <div class="card mb-2">
                    <div class="card-body d-flex justify-content-between"> <!-- d-flex와 justify-content-between 추가 -->
                        <div>
                            <p class="card-text"><?php echo $comment->getContent(); ?></p>
                            <small class="text-muted">
                                작성자:
                                <?= $userRepository->getUserEmailById($comment->getUserId())->getEmail(); ?>
                            </small>
                        </div>
                        <div class="text-right"> <!-- text-right 추가 -->
                            <small class="text-muted"><?php echo date('Y-m-d', strtotime($comment->getDate())); ?></small>
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

<script>
    //게시글 삭제
    function submitDeleteForm() {
        var formData = new FormData(document.getElementById('deleteForm'));

        fetch('/action/board/deletePost.php', {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                // 서버에서 반환한 데이터를 처리
                if (data.status) {
                    alert(data.content);
                    window.location.href = '/pages/admin/adminHome.php';
                } else {
                    alert(data.content);
                }
            })
            .catch(data => {
                alert(data.content);
                console.log('Error:', data.content);
            });
    }

    // 댓글 생성
    function submitCommentForm() {
        var formData = new FormData(document.getElementById('createCommentForm'));

        // 비동기적으로 createUser.php에 POST 요청을 보냄
        fetch('/action/comment/createComment.php', {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                // 서버에서 반환한 데이터를 처리
                if (data.status) {
                    alert(data.content);
                    location.reload();
                } else {
                    alert(data.content);
                }
            })
            .catch(data => {
                alert(data.content);
                console.log('Error:', data.content);
            });
    }

    // 글 열람 권한 변경
    function submitBoardAuthority() {
        var formData = new FormData(document.getElementById('authorityForm'));

        fetch('/action/board/boardAuthorityChange.php', {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                // 서버에서 반환한 데이터를 처리
                console.log(data.status + " " + data.content);
                if (data.status) {
                    alert(data.content);
                    window.location.href = '/pages/admin/adminHome.php';
                } else {
                    alert(data.content);
                }
            })
            .catch(error => {
                alert("Error: " + error);
                console.log('Error:', error);
            });
    }
</script>