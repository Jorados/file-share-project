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
</head>
<body>
<?php include '/var/www/html/includes/header.php'?>
<div class="container mt-5">
    <!-- 게시글 상세 정보 -->
    <div class="card mx-auto mb-5" style="max-width: 1000px;">
        <div class="card-header bg-dark text-white" style="max-height: 90px;">
            <h3 class="text-center">글 상세 조회</h3>
        </div>


        <div class="card-body">
            <?php if ($board->getOpenclose() == 0 && $_SESSION['role'] == 'user'): ?>
                <p class="card-text">제목 : <?= $board->getTitle(); ?></p>
                <p class="card-text">내용 : 볼 수 없음</p>
                <p class="card-text">첨부 파일 : 볼 수 없음</p>
                <p class="card-text">작성일 : 볼 수 없음</p>
                <p class="card-text">열람 권한 : 불가</p>
            <?php else: ?>
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
                <p class="card-text">작성일 : <?= date('Y-m-d', strtotime($board->getDate())); ?></p>
                <p class="card-text">열람 권한 : <?= $board->getOpenclose() == 0 ? '불가' : '허용'; ?></p>
            <?php endif; ?>

            <?php if ($info && $_SESSION['role'] == 'user'): ?>
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
            <?php endif; ?>
        </div>
    </div>

    <?php if ($_SESSION['role'] == 'admin'): ?>
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
    <?php else: ?>
        <div class="card mx-auto mb-5" style="max-width: 1000px;">
            <div class="card-body">
                <div class="alert alert-warning" role="alert">
                    댓글 작성이 현재 불가능합니다.
                </div>
            </div>
        </div>
    <?php endif; ?>

    <div class="card mx-auto mb-5" style="max-width: 1000px;">
        <div class="card-body">
            <label for="comment_content">댓글 내용</label>
            <?php foreach ($comments as $comment): ?>
                <div class="card mb-2">
                    <div class="card-body d-flex justify-content-between">
                        <div>
                            <p class="card-text"><?= $comment->getContent();?></p>
                            <small class="text-muted">
                                작성자:
                                <?= $userRepository->getUserEmailById(new User(['user_id'=>($comment->getUserId())]))->getEmail(); ?>
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