<?php
/**
 * 사용자 -> 홈 페이지 (로그인 후)
 */
session_start();
include '/var/www/html/lib/config.php';

use repository\UserRepository;
use service\BoardService;
use repository\CommentRepository;

$commentRepository = new CommentRepository();
$userRepository = new UserRepository();
$boardService = new BoardService();

$user_id = $_SESSION['user_id'];
$items_per_page = 9;
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($current_page - 1) * $items_per_page;
$order = isset($_GET['order']) ? $_GET['order'] : 'newest'; // 기본값은 최신순
$status = "normal";

$permission = isset($_GET['permission']) ? $_GET['permission'] : null; // openclose
$searchType = isset($_GET['search_type']) ? $_GET['search_type'] : null; // title,content
$searchQuery = isset($_GET['search_query']) ? $_GET['search_query'] : null; // value

$result = $boardService->getBoardByPage($items_per_page, $order, $offset, $permission, $searchType, $searchQuery, $user_id, $status);
$total_pages = $result['total_pages'];
$boards = $result['boards'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>게시글 조회</title>
    <?php include '/var/www/html/includes/header.php'?>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://unpkg.com/ionicons@latest/dist/ionicons.js"></script>
</head>
<body>
<!--<div class="container-fluid mt-5" style="max-width: 70%;">-->
<div class="container mt-5">
    <h2 class="text-center mb-5">사용자 게시글 조회</h2>

    <nav class="navbar bg-body-tertiary">
        <div class="container-fluid">
            <form class="d-flex ml-auto" method="GET" action="userHome.php">
                <ion-icon class="mr-3" name="reload" onclick="resetSearchParams()" style="font-size: 40px; color: #1977c9; --ionicon-stroke-width: 45px;"></ion-icon>

                <select class="form-control mr-2" name="permission" aria-label="Default select example" style="width: 100px;">
                    <option selected>-권한-</option>
                    <option value="1">허용</option>
                    <option value="0">불가</option>
                </select>

                <select class="form-control mr-2" name="search_type" aria-label="Default select example" style="width: 100px;" onchange="enableSearchInput()">
                    <option selected>-선택-</option>
                    <option value="title">제목</option>
                    <option value="content">내용</option>
                </select>

                <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search" name="search_query" id="searchQueryInput" autocomplete="off">
                <button class="btn btn-outline-primary ml-1" type="submit">Search</button>
            </form>
        </div>
    </nav>

    <ul class="nav nav-tabs mt-2 mb-4">
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
                            열람권한: <span style="color: <?= $board->getOpenclose() == 0 ? 'red' : 'blue'; ?>"><?= $board->getOpenclose() == 0 ? '불가' : '허용'; ?></span>
                        </p>
                        <p class="card-text" style="float: right;">
                            댓글 <?= $commentRepository->getCountComments($board->getBoardId()); ?> 개
                        </p>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="container mt-4" id="pagination-container">
        <nav aria-label="Page navigation example">
            <ul class="pagination justify-content-center">
                <?php if ($current_page > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?php echo ($current_page - 1); ?>" aria-label="Previous">
                            <span aria-hidden="true">&laquo;</span>
                        </a>
                    </li>
                <?php elseif($current_page<=1): ?>
                    <a class="page-link" aria-label="Previous">
                        <span aria-hidden="true">&laquo;</span>
                    </a>
                <?php endif; ?>

                <?php // 페이지 범위 설정 (첫 페이지부터 몇 개의 페이지를 보여줄 것인지)
                $page_range = 5;
                $start_page = max(1, $current_page - $page_range + 1);
                $end_page = min($total_pages, $start_page + $page_range - 1);

                for ($i = $start_page; $i <= $end_page; $i++): ?>
                    <li class="page-item <?php echo $current_page == $i ? 'active' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                    </li>
                <?php endfor; ?>

                <?php if ($current_page < $total_pages): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?php echo ($current_page + 1); ?>" aria-label="Next">
                            <span aria-hidden="true">&raquo;</span>
                        </a>
                    </li>
                <?php elseif($current_page >= $total_pages): ?>
                    <a class="page-link" aria-label="Previous">
                        <span aria-hidden="true">&raquo;</span>
                    </a>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
</div>
<script src="/assets/js/board/homeBoard.js"></script>
<link rel="stylesheet" href="/assets/css/home.css">
</body>
</html>
