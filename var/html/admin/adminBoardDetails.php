<?php
session_start();
include '/var/www/html/database/DatabaseConnection.php';
include '/var/www/html/repository/boardRepository.php';
include '/var/www/html/repository/infoRepository.php';
include '/var/www/html/repository/userRepository.php';
include '/var/www/html/repository/commentRepository.php';
include '/var/www/html/repository/attachmentRepository.php';
include '/var/www/html/mail/sendMail.php';
include '/var/access_logs/PostLogger.php';
include '/var/access_logs/CommentLogger.php';

$dbConnection = new DatabaseConnection();
$pdo = $dbConnection->getConnection();

// 리포지토리 인스턴스 생성 시 데이터베이스 연결 주입
$boardRepository = new BoardRepository($pdo);
$infoRepository = new InfoRepository($pdo);
$userRepository = new UserRepository($pdo);
$commentRepository = new CommentRepository($pdo);
$attachmentRepository = new AttachmentRepository($pdo);
$mailSender = new SendMail($pdo);
$logger = new PostLogger();

$board_id = isset($_GET['board_id']) ? $_GET['board_id'] : null;
if (!$board_id) die("게시글 ID가 제공되지 않았습니다.");

// 글 상세 조회 로그  추가
$board = $boardRepository -> getBoardByid($board_id);
$status = $board['status'];
$email = $_SESSION['email'];
$title = $board['title'];
$logger->readPost($_SERVER['REQUEST_URI'], $email, $status, $title);

try {
    $board = $boardRepository -> getBoardByid($board_id);
    $user_id = $board['user_id'];
    $user = $userRepository -> getUserById($user_id);
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}

// 댓글 조회
try {
    $comments = $commentRepository -> getCommentByBoardId($board_id);
    // 각 댓글의 작성자 이메일을 가져옵니다.
    foreach ($comments as &$comment) {
        $user_id = $comment['user_id'];
        $user = $userRepository->getUserEmailById($user_id);
        $comment['user_email'] = $user['email'];
    }
} catch (PDOException $e) {
    echo "댓글 조회 중 오류가 발생했습니다: " . $e->getMessage();
}
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
<?php include '/var/www/html/includes/adminNavibar.php'?>
<div class="container mt-5">

    <div class="card mx-auto mb-5" style="max-width: 1000px;">
        <div class="card-header bg-dark text-white" style="max-height: 90px;">
            <h3 class="text-center">글 상세 조회</h3>
        </div>
        <div class="card-body">
            <p class="card-text">제목 : <?php echo htmlspecialchars($board['title'], ENT_QUOTES, 'UTF-8'); ?></p>
            <p class="card-text">내용 : <?php echo htmlspecialchars($board['content'], ENT_QUOTES, 'UTF-8'); ?></p>
            <p class="card-text">파일목록
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
            </p>
            <p class="card-text">작성자 : <?php echo $user['username']; ?></p>
            <p class="card-text">작성자 이메일 : <?php echo $user['email']; ?></p>
            <p class="card-text">작성일 : <?php echo date('Y-m-d', strtotime($board['date'])); ?></p>
            <p class="card-text">열람 권한 : <?php echo $board['openclose'] == 0 ? '불가' : '허용'; ?></p>

            <form action="/action/boardAuthorityChange.php" method="post"  class="mt-3" id="authorityForm">
                <label for="reason_content">사유</label>
                <textarea name="reason_content" id="reason_content" rows="3" class="form-control" placeholder="열람 권한 변경의 사유를 입력하세요." required></textarea>
                <input type="hidden" name="board_id" value="<?php echo $board['board_id']; ?>">

                <?php if ($board['openclose'] == 0): ?>
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
                        <input type="hidden" name="board_id" value="<?php echo $board['board_id']; ?>">
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
                <input type="hidden" name="board_id" value="<?php echo $board['board_id']; ?>">
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

<script>
    function submitDeleteForm() {
        var formData = new FormData(document.getElementById('createCommentForm'));

        // 비동기적으로 createUser.php에 POST 요청을 보냄
        fetch('/action/deletePost.php', {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                // 서버에서 반환한 데이터를 처리
                if (data.status) {
                    alert(data.content);
                    window.location.href = '/admin/adminHome.php';
                } else {
                    alert(data.content);
                }
            })
            .catch(data => {
                alert(data.content);
                console.log('Error:', data.content);
            });
    }

    function submitCommentForm() {
        var formData = new FormData(document.getElementById('createCommentForm'));

        // 비동기적으로 createUser.php에 POST 요청을 보냄
        fetch('/action/createComment.php', {
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

    function submitBoardAuthority() {
        var formData = new FormData(document.getElementById('authorityForm'));

        // 비동기적으로 createUser.php에 POST 요청을 보냄
        fetch('/action/boardAuthorityChange.php', {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                // 서버에서 반환한 데이터를 처리
                console.log(data.status + " " + data.content);
                if (data.status) {
                    alert(data.content);
                    window.location.href = '/admin/adminHome.php';
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