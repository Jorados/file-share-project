<?php
/**
 * 사용자 -> 공지 조회 페이지
 */
include '/var/www/html/lib/config.php';

use repository\UserRepository;
use repository\BoardRepository;

$userRepository = new UserRepository();
$boardRepository = new BoardRepository();

$boards = $boardRepository->getNotificationBoardItems();
$total = 1;
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset='utf-8'>
    <title>공지 글 조회</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include '/var/www/html/includes/header.php'?>
<div class="container mt-5">
    <h2 class="text-center mb-4">공지 조회</h2>

    <div class="row">
        <?php foreach ($boards as $index => $board): ?>
            <div class="col-md-4 mt-5 mb-4">
                <div class="card shadow">
                    <div class="card-body">
                        <h5 class="card-title">
                            <a href="userNoticeDetails.php?board_id=<?= $board->getBoardId(); ?>">
                                <?php
                                $title = $board->getTitle();
                                if (strlen($title) > 27) {
                                    echo substr($title, 0, 27) . "..";
                                } else {
                                    echo $title;
                                }
                                ?>
                            </a>
                        </h5>
                        <p class="card-text"><?= $board->getContent(); ?></p>
                        <p class="card-text">
                            작성자:
                            <?= $user = $userRepository->getUserById($board->getUserId())->getEmail(); ?>
                        </p>
                        <p class="card-text">날짜: <?php echo date('Y-m-d', strtotime($board->getDate())); ?></p>
                        <p class="card-text">열람권한: 허용</p>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
</body>
<link rel="stylesheet" href="/assets/css/notice.css">
<footer>
    <?php include '/var/www/html/includes/footer.php'?>
</footer>
</html>

