<?php
/**
 * 관리자 -> 게시글 상세 조회 페이지
 */

session_start();
include_once '/var/www/html/lib/config.php';

use service\UserService;
use repository\UserRepository;
use dataset\User;

$userService = new UserService();
$userRepository = new UserRepository();

$email = $_SESSION['email'];
$board_id = isset($_GET['board_id']) ? $_GET['board_id'] : null;

$result = $userService->boardDetailByAdmin($email,$board_id);
$user = $result['user'];
$board = $result['board'];
$comments = $result['comments'];
$attachments = $result['attachments'];
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset='utf-8'>
    <title>게시글 상세보기</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="/assets/js/board/changeBoardAuthority.js"></script>
    <script src="/assets/js/board/deleteBoard.js"></script>
    <script src="/assets/js/comment/createComment.js"></script>
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
                            $filename = $attachment->getFilename();

                            $dot=0;
                            $under=0;
                            for ($i = strlen($filename) - 1; $i >= 0; $i--) {
                                if($filename[$i]=='.' && $dot == 0) $dot=$i;
                                if($filename[$i]=='_' && $under == 0) $under=$i;
                            }

                            $leftPart = substr($filename, 0, $under); // $under 이전까지의 부분
                            $rightPart = substr($filename, $dot); // $dot 이후의 부분
                            $displayFilename = $leftPart . $rightPart;
                            echo '<li><a href="/file/download.php?file=' . urlencode($filename) . '">' . $displayFilename . '</a></li>';
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

            <form action="/action/board/boardAuthorityChange.php" method="post"  class="mt-3" id="authorityForm">
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
                    <form action="/action/board/deleteForm.php" method="post"  class="d-inline-block" id="deleteForm">
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
            <form action="/action/comment/createComment.php" method="post" id="createCommentForm">
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
                                <?= $userRepository->getUserEmailById(new User(['user_id'=>($comment->getUserId())]))->getEmail(); ?>
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


