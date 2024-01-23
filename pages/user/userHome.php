<?php
session_start();
include '/var/www/html/lib/config.php';

use repository\UserRepository;
use repository\BoardRepository;
use dataset\User;

$userRepository = new UserRepository();
$boardRepository = new BoardRepository();

$user = $userRepository->getUserByEmail(new User(['email'=>$_SESSION['email']]));
if (!$user) {
    header("Location: /lib/pages/user/userHome.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$items_per_page = 9;
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($current_page - 1) * $items_per_page;
$order = isset($_GET['order']) ? $_GET['order'] : 'newest'; // 기본값은 최신순

$total_items = $boardRepository->getTotalItemsByUserId(new User(['user_id'=>$user_id]));
$total_pages = ceil($total_items / $items_per_page);

// 정렬 방식에 따라 데이터를 가져오기
if ($order === 'newest') {
    $boards = $boardRepository->getBoardsByPageAndUser($user_id, $offset, $items_per_page, $order);
} elseif ($order === 'oldest') {
    $boards = $boardRepository->getBoardsByPageAndUser($user_id, $offset, $items_per_page, $order);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>게시글 조회</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include '/var/www/html/includes/header.php'?>

<div class="container mt-5">
    <h2 class="text-center mb-2">게시글 조회</h2>

    <ul class="nav nav-tabs mb-4">
        <li class="nav-item">
            <a class="nav-link <?php echo (!isset($_GET['order']) || $_GET['order'] === 'newest') ? 'active' : ''; ?>" href="?page=1&order=newest">최신순</a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo isset($_GET['order']) && $_GET['order'] === 'oldest' ? 'active' : ''; ?>" href="?page=1&order=oldest">오래된순</a>
        </li>
    </ul>

    <div class="row">
        <?php foreach ($boards as $board): ?>
            <div class="col-md-4 mb-4">
                <div class="card shadow" style="min-height: 230px; background-color: <?= $board->getOpenclose() == 1 ? '#D0E7FA' : '#FFFFFF'; ?>;">
                    <div class="card-body">
                        <h5 class="card-title">
                            <a href="userBoardDetails.php?board_id=<?= $board->getBoardId(); ?>">
                                <?php
                                $title = $board->getTitle();
                                $escapedTitle = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');
                                echo strlen($escapedTitle) > 27 ? substr($escapedTitle, 0, 27) . ".." : $escapedTitle;
                                ?>
                            </a>
                            <div style="float: right;">
                                <?php
                                // 현재 날짜와 게시글 작성일이 동일한 경우에만 "new" 문구를 추가
                                $currentDate = date('Y-m-d');
                                $boardDate = date('Y-m-d', strtotime($board->getDate()));
                                if ($currentDate == $boardDate) {
                                    echo '<span class="badge badge-pill badge-primary">new</span>';
                                }
                                ?>
                            </div>
                        </h5>
                        <p class="card-text">
                            <?php
                            $content = $board->getOpenclose() == 0 ? '볼 수 없음' : (strlen($board->getContent()) > 100 ? substr($board->getContent(), 0, 50) . ".." : $board->getContent());
                            $escapedContent = htmlspecialchars($content, ENT_QUOTES, 'UTF-8');
                            echo '내용: ' . $escapedContent;
                            ?>
                        </p>
                        <p class="card-text">
                            작성자:
                            <?= $userRepository->getUserById($board->getUserId())->getEmail(); ?>
                        </p>
                        <p class="card-text">
                            날짜: <?= $board->getOpenclose() == 0 ? '볼 수 없음' : date('Y-m-d', strtotime($board->getDate())); ?>
                        </p>
                        <p class="card-text">
                            권한:  <?= $board->getOpenclose() == 0 ? '불가' : '허용' ?>
                        </p>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="container mt-4 fixed-pagination">
        <ul class="pagination justify-content-center">
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <li class="page-item <?php echo $current_page == $i ? 'active' : ''; ?>">
                    <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                </li>
            <?php endfor; ?>
        </ul>
    </div>
</div>
<link rel="stylesheet" href="/assets/css/home.css">
</body>
</html>
