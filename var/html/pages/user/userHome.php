<?php
session_start();

include '/var/www/html/database/DatabaseConnection.php';
include '/var/www/html/repository/userRepository.php';
include '/var/www/html/repository/boardRepository.php';

$dbConnection = new DatabaseConnection();
$pdo = $dbConnection->getConnection();

$userRepository = new UserRepository($pdo);
$boardRepository = new BoardRepository($pdo);

try {
    $user = $userRepository->getUserByEmail($_SESSION['email']);
    if (!$user) {
        header("Location: /user/userHome.php");
        exit;
    }
    if (isset($_GET['write_post'])) {
        // authority 값 체크 및 동작
        if ($user['authority'] == 0) {
            $_SESSION['error_message'] = "권한이 없습니다.";
            header("Location: /user/userHome.php");
            exit;
        } elseif ($user['authority'] == 1) {
            header("Location: /user/userBoardCreate.php"); // 권한이 있는 경우에 해당 페이지로 리다이렉트
            exit;
        }
    }
} catch (PDOException $e) {
    throw new PDOException($e->getMessage(), (int)$e->getCode());
}

$user_id = $_SESSION['user_id'];
$items_per_page = 10;
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
        $stmt = $boardRepository->getBoardsByPage($offset, $items_per_page, $order);
    } elseif ($order === 'oldest') {
        $stmt = $boardRepository->getBoardsByPage($offset, $items_per_page, $order);
    }
} catch (PDOException $e) {
    throw new PDOException($e->getMessage(), (int)$e->getCode());
}
?>

<!DOCTYPE html>
<head>
    <?php include '/var/www/html/includes/header.php'?>
    <?php include '/var/www/html/includes/userNavibar.php'?>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

    <?php if ($user['available'] == 0): ?>
    <div class="container mt-5">
        <div class="text-center mb-4">
            <strong>개인 정보</strong>
            <div class="mt-3">
                <form action="/user/userPassword.php" method="get">
                    <p class="text-muted">최초 로그인 시 비밀번호를 변경해야 합니다.</p>
                    <button type="submit" class="btn btn-primary">비밀번호 변경</button>
                </form>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <?php if ($user['available'] == 1): ?>
        <div class="container mt-5">
            <h2 class="text-center mb-2">게시글 조회</h2>

            <ul class="nav nav-tabs mb-1">
                <li class="nav-item">
                    <a class="nav-link <?php echo isset($_GET['order']) && $_GET['order'] === 'newest' ? 'active' : ''; ?>" href="?page=1&order=newest">최신순</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo isset($_GET['order']) && $_GET['order'] === 'oldest' ? 'active' : ''; ?>" href="?page=1&order=oldest">오래된순</a>
                </li>
            </ul>

            <div class="table-responsive">
                <table class="table table-bordered table-striped fixed-table">
                    <thead class="thead-white">
                    <tr>
                        <th scope="col" width="50" class="text-center">번호</th>
                        <th scope="col" width="200" class="text-center">제목</th>
                        <th scope="col" width="200" class="text-center">내용</th>
                        <th scope="col" width="160" class="text-center">작성자</th>
                        <th scope="col" width="100" class="text-center">날짜</th>
                    </tr>
                    </thead>

                    <tbody>
                    <?php while ($row = $stmt->fetch()): ?>
                        <tr>
                            <td class="text-center"><?php echo $total; ?></td> <?php $total++; ?>
                            <td class="text-left">
                                <a href="userBoardDetails.php?board_id=<?php echo $row['board_id']; ?>">
                                    <?php
                                    $title = $row['title']; // 제목을 변수에 저장합니다.
                                    $escapedTitle = htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); // HTML 이스케이프
                                    if (strlen($escapedTitle) > 27) {
                                        echo substr($escapedTitle, 0, 27) . "..";
                                    } else {
                                        echo $escapedTitle;
                                    }
                                    ?>
                                </a>
                            </td>
                            <td class="text-left">
                                <?php
                                $content = $row['openclose'] == 0 ? '볼 수 없음' : (strlen($row['content']) > 100 ? substr($row['content'], 0, 50) . ".." : $row['content']);
                                $escapedContent = htmlspecialchars($content, ENT_QUOTES, 'UTF-8');
                                echo $escapedContent;
                                ?>
                            </td>
                            <td class="text-center"><?php echo $_SESSION['email'] ?></td>
                            <td class="text-center"><?php echo $row['openclose'] == 0 ? '볼 수 없음' : date('Y-m-d', strtotime($row['date'])); ?></td>
                        </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
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
    <?php endif; ?>
</div>
</body>
<footer>
    <?php include '/var/www/html/includes/footer.php'?>
</footer>
</html>

<style>
    .fixed-pagination {
        position: fixed;
        bottom: 7%;
        left: 50%;
        transform: translateX(-50%);
        z-index: 1000; /* 다른 요소 위에 위치하도록 z-index 값 설정 */
    }

    .fixed-table {
        width: 1110px; /* 원하는 너비로 설정하세요 */
        table-layout: fixed;
    }

    .fixed-table thead th {
        position: sticky;
        top: 0;
        background-color: #f5f5f5;
    }

    .content-cell {
        width: 200px; /* 셀의 최대 너비를 지정합니다. */
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
</style>
