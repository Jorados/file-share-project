<?php
session_start();

include '/var/www/html/database/DatabaseConnection.php';
include '/var/www/html/repository/boardRepository.php';
include '/var/www/html/repository/userRepository.php';


$dbConnection = new DatabaseConnection();
$pdo = $dbConnection->getConnection();

$userRepository = new UserRepository($pdo);
$boardRepository = new BoardRepository($pdo);

$items_per_page = 9;
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($current_page - 1) * $items_per_page;

$order = isset($_GET['order']) ? $_GET['order'] : 'newest'; // 기본값은 최신순

try {
    $boardRepository = new BoardRepository($pdo);
    $total_items = $boardRepository->getTotalBoardCount();
    $total_pages = ceil($total_items / $items_per_page);

    // 각 페이지의 시작 번호를 설정
    $total = $offset + 1;

    // 정렬 방식에 따라 데이터를 가져오기
    if ($order === 'newest') {
        $boards = $boardRepository->getBoardsByPage($offset, $items_per_page, $order);
    } elseif ($order === 'oldest') {
        $boards = $boardRepository->getBoardsByPage($offset, $items_per_page, $order);
    }
} catch (PDOException $e) {
    throw new PDOException($e->getMessage(), (int)$e->getCode());
}

?>
<!DOCTYPE html>

<html>
<head>
    <meta charset='utf-8'>
    <title>전체 게시글 조회</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include '/var/www/html/includes/header.php'?>
<?php include '/var/www/html/includes/adminNavibar.php'?>

<div class="container mt-5">
    <h2 class="text-center mb-2">전체 게시글 조회</h2>

    <ul class="nav nav-tabs mb-4">
        <li class="nav-item">
            <a class="nav-link <?php echo isset($_GET['order']) && $_GET['order'] === 'newest' ? 'active' : ''; ?>" href="?page=1&order=newest">최신순</a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo isset($_GET['order']) && $_GET['order'] === 'oldest' ? 'active' : ''; ?>" href="?page=1&order=oldest">오래된순</a>
        </li>
    </ul>

    <div class="row">
        <?php while ($row = $boards->fetch()): ?>
            <div class="col-md-4 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">
                            <a href="adminBoardDetails.php?board_id=<?php echo $row['board_id']; ?>">
                                <?php
                                $title = $row['title'];
                                $escapedTitle = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');
                                echo strlen($escapedTitle) > 27 ? substr($escapedTitle, 0, 27) . ".." : $escapedTitle;
                                ?>
                            </a>
                        </h5>
                        <p class="card-text">
                            <?php
                            $content = $row['content'];
                            $escapedContent = htmlspecialchars($content, ENT_QUOTES, 'UTF-8');
                            echo strlen($escapedTitle) > 27 ? substr($escapedTitle, 0, 27) . ".." : $escapedTitle;
                            ?>
                        </p>
                        <p class="card-text">
                            작성자: <?php echo $userRepository->getUserById($row['user_id'])['email']; ?>
                        </p>
                        <p class="card-text">
                            날짜: <?php echo date('Y-m-d', strtotime($row['date'])); ?>
                        </p>
                        <p class="card-text">
                            열람권한: <?php echo $row['openclose'] == 0 ? '불가' : '허용'; ?>
                        </p>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
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

</body>
<!--<footer>-->
<!--    --><?php //include '/var/www/html/includes/footer.php'?>
<!--</footer>-->

</html>
<style>
    /*.fixed-pagination {*/
    /*    position: fixed;*/
    /*    bottom: 7%;*/
    /*    left: 50%;*/
    /*    transform: translateX(-50%);*/
    /*    z-index: 1000; !* 다른 요소 위에 위치하도록 z-index 값 설정 *!*/
    /*}*/

    /* 추가된 스타일 */
    .card {
        min-height: 200px; /* 카드의 최소 높이 설정 */
    }

    .card-title {
        font-size: 1.25rem; /* 카드 제목 글꼴 크기 설정 */
    }
</style>