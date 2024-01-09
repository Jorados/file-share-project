<?php
session_start();
include '/var/www/html/database/DatabaseConnection.php';
include '/var/repository/boardRepository.php';
include '/var/repository/infoRepository.php';
include '/var/repository/userRepository.php';
include '/var/repository/commentRepository.php';
include '/var/repository/attachmentRepository.php';
include '/var/www/html/mail/sendMail.php';

$dbConnection = new DatabaseConnection();
$pdo = $dbConnection->getConnection();

// 리포지토리 인스턴스 생성 시 데이터베이스 연결 주입
$boardRepository = new BoardRepository($pdo);
$infoRepository = new InfoRepository($pdo);
$userRepository = new UserRepository($pdo);
$commentRepository = new CommentRepository($pdo);
$attachmentRepository = new AttachmentRepository($pdo);
$mailSender = new SendMail($pdo);

$board_id = isset($_GET['board_id']) ? $_GET['board_id'] : null;
if (!$board_id) die("게시글 ID가 제공되지 않았습니다.");

try {
    $board = $boardRepository -> getBoardByid($board_id);
    if (!$board) die("해당 ID의 게시글을 찾을 수 없습니다.");

    $user_id = $board['user_id'];
    $user = $userRepository -> getUserById($user_id);
    if (!$user) die("해당 ID의 사용자를 찾을 수 없습니다.");
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}

// 게시글 삭제 기능
if (isset($_POST['delete_post'])) {
    try {
        $stmt = $boardRepository -> deleteBoardById($board_id);
        $stmt = $attachmentRepository -> deleteAttachment($board_id);

        header("Location: adminBoardList.php");
        exit;
    } catch (PDOException $e) {
        echo "게시글 삭제 중 오류가 발생했습니다: " . $e->getMessage();
    }


}

// 글 열람권한 변경 관련 --> 해당 작성자에게 메일도 전송 해야함.
if (isset($_POST['change_permission'])) {
    $newPermission = $_POST['change_permission'];
    $board_id = $_POST['board_id'];
    $reason_content = $_POST['reason_content']; // 사용자로부터의 입력
    $user_id = $_SESSION['user_id'];
    try {
        // 게시글 권한 업데이트
        $boardRepository->updateBoardPermission($board_id, $newPermission);
        // info 테이블에 정보 삽입
        $infoRepository->addInfo($reason_content, $user_id, $board_id);

        // 메일 전송 구현 로직 , 글 주인한테 메일 쏴야함
        // 해당 board_id가 가지고 있는 user_id를 가지는 user의 email정보를 알아야한다.
        // 그리고 그 email 정보를 이용해서 메일 전송.
        $boardUser_email = $boardRepository->getBoardUserEmail($board_id);
        $subject = '글 권한 상태가 변경되었습니다.';
        $message = '관리자 ' . $_SESSION['email'] . ' 님에 의해 게시글 상태가 변경되었습니다.  사유 : ' . $reason_content;

        if ($mailSender->sendToUser($subject, $message,$boardUser_email)) {
            echo "메일이 성공적으로 전송되었습니다.";
        } else {
            echo "메일 전송에 실패했습니다.";
        }

        header("Location: /admin/adminBoardList.php"); // 페이지 새로고침
    } catch (PDOException $e) {
        echo "오류: " . $e->getMessage();
    }
}

// 댓글 등록 기능
if (isset($_POST['submit_comment'])) {
    $content = $_POST['content'];
    $user_id = $_SESSION['user_id']; // 로그인한 사용자의 ID를 사용

    try {
        $stmt = $commentRepository -> addComment($content, $board_id, $user_id);
    } catch (PDOException $e) {
        echo "댓글 작성 중 오류가 발생했습니다: " . $e->getMessage();
    }
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
            <p class="card-text">제목 : <?php echo $board['title']; ?></p>
            <p class="card-text">내용 : <?php echo $board['content']; ?></p>
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

            <form method="post" action="" class="mt-3">
                <label for="reason_content">사유</label>
                <textarea name="reason_content" id="reason_content" rows="3" class="form-control" placeholder="열람 권한 변경의 사유를 입력하세요." required></textarea>

                <input type="hidden" name="board_id" value="<?php echo $board['board_id']; ?>">
                <?php if ($board['openclose'] == 0): ?>
                    <button type="submit" name="change_permission" value="1" class="btn btn-primary mt-3">열람 허용</button>
                <?php else: ?>
                    <button type="submit" name="change_permission" value="0" class="btn btn-primary mt-3">열람 불가</button>
                <?php endif; ?>
            </form>

            <div class="row mt-3">
                <div class="col-md-6">
                    <form method="post" action="" class="d-inline-block">
                        <input type="submit" name="delete_post" value="삭제" class="btn btn-danger" onclick="return confirm('정말로 삭제하시겠습니까?');">
                    </form>
                    <a href="boardEdit.php?id=<?php echo $board['board_id']; ?>" class="btn btn-warning">수정</a>
                </div>
            </div>
        </div>
    </div>

    <div class="card mx-auto mb-5 " style="max-width: 1000px";>
        <div class="card-body">
            <form method="post" action="">
                <div class="form-group">
                    <label for="content">댓글 내용</label>
                    <textarea name="content" id="content" rows="3" class="form-control" required></textarea>
                </div>

                <button type="submit" name="submit_comment" class="btn btn-primary">댓글 작성</button>
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