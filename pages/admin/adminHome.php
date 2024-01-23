<?php
session_start();
include '/var/www/html/lib/config.php';

use repository\UserRepository;
use service\BoardService;

$userRepository = new UserRepository();
$boardService = new BoardService();

$items_per_page = 9;
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$order = isset($_GET['order']) ? $_GET['order'] : 'newest'; // 기본값은 최신순
$offset = ($current_page - 1) * $items_per_page;

$permission = isset($_GET['permission']) ? $_GET['permission'] : null; // openclose
$searchType = isset($_GET['search_type']) ? $_GET['search_type'] : null; // title,content
$searchQuery = isset($_GET['search_query']) ? $_GET['search_query'] : null; // value

$result = $boardService->getBoardByPage($items_per_page, $order, $offset, $permission, $searchType, $searchQuery);
$total_pages = $result['total_pages'];
$boards = $result['boards'];
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset='utf-8'>
    <title>전체 게시글 조회</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://unpkg.com/ionicons@latest/dist/ionicons.js"></script>
</head>
<body>
<?php include '/var/www/html/includes/header.php'?>

<div class="container mt-5">
    <h2 class="text-center mb-5">전체 게시글 조회</h2>

    <nav class="navbar bg-body-tertiary">
        <div class="container-fluid">
            <form class="d-flex ml-auto" method="GET" action="adminHome.php">

                <ion-icon class="mr-3" name="reload" onclick="resetSearchParams()"></ion-icon>
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
                            <a href="adminBoardDetails.php?board_id=<?= $board->getBoardId(); ?>">
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
                            $content = $board->getContent();
                            $escapedContent = htmlspecialchars($content, ENT_QUOTES, 'UTF-8');
                            echo strlen($escapedContent) > 27 ? substr($escapedContent, 0, 27) . ".." : $escapedContent;
                            ?>
                        </p>
                        <p class="card-text">
                            작성자:
                            <?php
                            $user = $userRepository->getUserById($board->getUserId());
                            echo $user->getEmail(); ?>
                        </p>
                        <p class="card-text">
                            날짜: <?= date('Y-m-d', strtotime($board->getDate())); ?>
                        </p>
                        <p class="card-text">
                            열람권한: <?= $board->getOpenclose() == 0 ? '불가' : '허용'; ?>
                        </p>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="container mt-4" id="pagination-container">
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

<!--검색 후 파라미터 유지.-->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // DOM이 로드된 후 실행되는 코드
        var tabs = document.querySelectorAll('.nav-tabs .nav-link');

        tabs.forEach(function(tab) {
            tab.addEventListener('click', function(event) {
                // 탭 클릭 시 실행되는 코드
                event.preventDefault();

                // 현재 페이지 URL을 기반으로한 새로운 URL을 생성
                var url = new URL(window.location.href);
                url.searchParams.set('order', this.getAttribute('href').includes('newest') ? 'newest' : 'oldest');

                // 페이지를 리로드
                window.location.href = url.toString();
            });
        });
    });
</script>

<script>
    function resetSearchParams() {
        // 현재 페이지 URL을 기반으로한 새로운 URL을 생성
        var url = new URL(window.location.href);

        // 모든 파라미터 제거
        url.search = '';

        // 페이지를 리로드
        window.location.href = url.toString();
    }
</script>

<style>
    ion-icon {
        font-size: 40px;
        color: #1977c9;
        --ionicon-stroke-width: 45px;
    }
</style>