<?php
/**
 * 사용자 -> 게시글 상세 조회 페이지
 */
session_start();

include_once '/var/www/html/lib/config.php';

use repository\UserRepository;
use service\UserService;
use dataset\User;

$board_id = isset($_GET['board_id']) ? $_GET['board_id'] : null;
$email = $_SESSION['email'];
$userRepository = new UserRepository();
$userService = new UserService();

$result = $userService->boardDetailByUser($email,$board_id);
$board = $result['board'];
$info = $result['info'];
$user = $result['user'];
$comments = $result['comments'];
$attachments = $result['attachments'];
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset='utf-8'>
    <title>게시글 상세보기</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/index.css">
</head>
<body>
<?php include '/var/www/html/includes/header.php'?>
<div class="container mt-5">
    <!-- 게시글 상세 정보 -->
    <div class="card mx-auto mb-4" style="max-width: 1000px;">
        <div class="card-header custom-header d-flex justify-content-between align-items-center" style="max-height: 90px;">
            <?php if ($board->getStatus() == 'normal'): ?>
                <h2 class="text-center" style="color: black">글 상세 조회</h2>
            <?php elseif ($board->getStatus() == 'notification'):?>
                <h2 class="text-center" style="color: black">공지 상세 조회</h2>
            <?php endif ?>
<!--            <button class="btn btn-primary" onclick="goBack()">돌아가기</button>-->
            <a onclick="goBack()" class="btn btn-primary">돌아가기</a>
        </div>

        <div class="card-body">
            <?php if ($board->getOpenclose() != 'open' && $_SESSION['role'] == 'user' && $board->getStatus() == 'normal'): ?>
                <div class="form-group">
                    <label class="card-title">제목</label>
                    <textarea class="form-control mb-3" id="title" rows="1" readonly><?= $board->getTitle(); ?></textarea>
                </div>
                <div class="form-group">
                    <label for="content">내용</label>
                    <textarea class="form-control mb-3" id="content" rows="1" readonly>볼 수 없음</textarea>
                </div>
                <div class="form-group">
                    <label for="attachments">첨부 파일</label>
                    <textarea class="form-control mb-3" id="attachments" rows="1" readonly>볼 수 없음</textarea>
                </div>
                <div class="form-group">
                    <label for="date">작성일</label>
                    <textarea class="form-control mb-3" id="date" rows="1" readonly>볼 수 없음</textarea>
                </div>
                <div class="form-group">
                    <label for="openclose">열람 권한</label>
                    <textarea class="form-control mb-3" id="openclose" rows="1" readonly style="color:
                    <?php
                    if ($board->getOpenclose() == 'open') {
                        echo 'blue';
                    } elseif ($board->getOpenclose() == 'close') {
                        echo 'red';
                    } elseif ($board->getOpenclose() == 'wait') {
                        echo 'green';
                    }
                    ?>;"><?= $board->getOpenclose() == 'open' ? '허용' : ($board->getOpenclose() == 'close' ? '불가' : '대기'); ?></textarea>
                </div>
            <?php else: ?>
                <label for="title">제목</label>
                <textarea class="form-control mb-3" id="title" rows="1" readonly><?= htmlspecialchars($board->getTitle(), ENT_QUOTES, 'UTF-8'); ?></textarea>

                <label for="content">내용</label>
                <textarea class="form-control mb-3" id="content" rows="1" readonly><?= htmlspecialchars($board->getContent(), ENT_QUOTES, 'UTF-8'); ?></textarea>

                <label for="attachments">파일목록</label>
                <ul class="list-group mb-3">
                    <?php
                    $filepath = '/var/www/html/file/uploads/';
                    if (empty($attachments)) {
                        echo '<li class="list-group-item">첨부파일이 없습니다.</li>';
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

                                echo '<li class="list-group-item">
                        <a href="/file/download.php?file=' . urlencode($filename) . '">' . $displayFilename . '</a>
                      </li>';
                            }
                        }
                    }
                    ?>
                </ul>

                <label for="date">작성일</label>
                <textarea class="form-control mb-3" id="date" rows="1" readonly><?= date('Y-m-d', strtotime($board->getDate())); ?></textarea>

                <?php if ($board->getStatus() == 'normal'): ?>
                    <label for="openclose">열람 권한</label>
                    <textarea class="form-control mb-3" id="openclose" rows="1" readonly style="color:
                <?php
                if ($board->getOpenclose() == 'open') {
                    echo 'blue';
                } elseif ($board->getOpenclose() == 'close') {
                    echo 'red';
                } elseif ($board->getOpenclose() == 'wait') {
                    echo '#09de00';
                }
                ?>;"><?= $board->getOpenclose() == 'open' ? '허용' : ($board->getOpenclose() == 'close' ? '불가' : '대기'); ?></textarea>
                <?php endif; ?>

                <?php if($board->getStatus() == 'notification' && $_SESSION['role'] == 'admin'): ?>
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <form action="/action/board/deleteForm.php" method="post"  class="d-inline-block" id="deleteForm">
                                <input type="hidden" name="board_id" value="<?= $board->getBoardId() ?>">
                                <input type="button" name="delete_post" value="공지 삭제" class="btn btn-danger" onclick="submitDeleteForm()">
                            </form>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

            <?php if ($info && $_SESSION['role'] == 'user' && $board->getStatus() == 'normal'): ?>
                <div class="info-container bg-light p-3 rounded mt-4">
                    <?php if ($board->getOpenclose() == 0): ?>
                        <h5 class="font-weight-bold mb-3">반려 사유</h5>
                    <?php elseif ($board->getOpenclose() == 1): ?>
                        <h5 class="font-weight-bold mb-3">승인 사유</h5>
                    <?php endif; ?>
                    <p class="mb-0">이유 : <?php echo $info->getReasonContent() ? $info->getReasonContent() : '승인,반려 대기중'; ?></p>
                    <p class="mb-1">날짜 : <?php echo $info->getDate() ? date('Y-m-d', strtotime($info->getDate())) : '없음'; ?></p>
                    <br>

                    <h5 class="font-weight-bold mb-3">관리자</h5>
                    <p class="mb-0">이름 : <?php echo !empty($user->getUsername()) ? $user->getUsername() : '없음'; ?></p>
                    <p class="mb-0">이메일 : <?php echo !empty($user->getEmail()) ? $user->getEmail() : '없음'; ?></p>
                    <p class="mb-1">전화번호 : <?php echo !empty($user->getPhone()) ? $user->getPhone() : '없음'; ?></p>
                </div>
            <?php elseif ($_SESSION['role'] == 'admin' && $board->getStatus() == 'normal'):?>
                <form action="/action/board/boardAuthorityChange.php" method="post"  class="mt-3" id="authorityForm">
                    <label for="reason_content">사유</label>
                    <textarea name="reason_content" id="reason_content" rows="3" class="form-control" placeholder="열람 권한 변경의 사유를 입력하세요." required></textarea>
                    <input type="hidden" name="board_id" value="<?= $board->getBoardId(); ?>">

                    <?php if ($board->getOpenclose() == 'close'): ?>
                        <button type="button" class="btn btn-primary mt-3" onclick="submitBoardAuthority('open')">열람 허용</button>
                    <?php elseif ($board->getOpenclose() == 'open'): ?>
                        <button type="button" class="btn btn-warning mt-3" onclick="submitBoardAuthority('close')">열람 불가</button>
                    <?php elseif ($board->getOpenclose() == 'wait'): ?>
                        <button type="button" class="btn btn-primary mt-3" onclick="submitBoardAuthority('open')">열람 허용</button>
                        <button type="button" class="btn btn-warning mt-3" onclick="submitBoardAuthority('close')">열람 불가</button>
                    <?php endif; ?>
                </form>

                <div class="row mt-3">
                    <div class="col-md-6">
                        <form action="/action/board/deleteForm.php" method="post"  class="d-inline-block" id="deleteForm">
                            <input type="hidden" name="board_id" value="<?= $board->getBoardId() ?>">
                            <input type="button" name="delete_post" value="글 삭제" class="btn btn-danger" onclick="submitDeleteForm()">
                        </form>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php if ($_SESSION['role'] == 'admin'): ?>
        <div class="card mx-auto mb-4 " style="max-width: 1000px";>
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
<!--    --><?php //else: ?>
<!--        <div class="card mx-auto mb-4" style="max-width: 1000px;">-->
<!--            <div class="card-body p-2">-->
<!--                <div class="alert alert-warning mb-0" role="alert">-->
<!--                    댓글 작성 권한이 없습니다.-->
<!--                </div>-->
<!--            </div>-->
<!--        </div>-->
    <?php endif; ?>

    <div class="card mx-auto mb-5" style="max-width: 1000px;">
        <div class="card-body">
            <label for="comment_content mb-2">댓글 내용</label>
            <?php if (empty($comments)): ?>
                <p class="text-muted mt-2">댓글이 없습니다.</p>
            <?php else: ?>
                <ul class="list-group">
                    <?php foreach ($comments as $comment): ?>
                        <li class="list-group-item" style="padding: 1px;">
                            <div class="card-body d-flex justify-content-between">
                                <div style="max-width: 80%;">
                                    <p class="card-text mb-2" style="margin: 0;"><?= $comment->getContent(); ?></p>
                                    <small class="text-muted mr-4"> 작성일: <?= date('Y-m-d', strtotime($comment->getDate())); ?></small>
                                    <small class="text-muted ">
                                        작성자: <?= $userRepository->getUserEmailById(new User(['user_id'=>($comment->getUserId())]))->getEmail(); ?>
                                    </small>
                                </div>

                                <div class="text-right">
                                    <?php if ($_SESSION['email'] == $userRepository->getUserEmailById(new User(['user_id'=>($comment->getUserId())]))->getEmail()): ?>
                                        <form action="/action/comment/deleteComment.php" method="post" id="deleteFormComment">
                                            <input type="hidden" name="comment_id" value="<?= $comment->getCommentId() ?>">
                                            <button type="button" name="delete_comment" class="btn btn-danger" onclick="submitDeleteFormComment()">삭제</button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>
</div>
<script>
    function goBack() {
        window.history.back();
    }
</script>
<script src="/assets/js/board/changeBoardAuthority.js"></script>
<script src="/assets/js/comment/createComment.js"></script>
<script src="/assets/js/comment/deleteComment.js"></script>
<script src="/assets/js/board/deleteBoard.js"></script>
</body>
<footer>
    <?php include '/var/www/html/includes/footer.php'?>
</footer>
</html>
