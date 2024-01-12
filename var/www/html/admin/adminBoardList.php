<?php
session_start();

include '/var/www/html/database/DatabaseConnection.php';
include '/var/www/html/repository/boardRepository.php';
include '/var/www/html/repository/userRepository.php';


$dbConnection = new DatabaseConnection();
$pdo = $dbConnection->getConnection();

$userRepository = new UserRepository($pdo);
$boardRepository = new BoardRepository($pdo);

$items_per_page = 10;
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($current_page - 1) * $items_per_page;

try {
    $boardRepository = new BoardRepository($pdo);
    $total_items = $boardRepository -> getTotalBoardCount();
    $total_pages = ceil($total_items / $items_per_page);

    // 각 페이지의 시작 번호를 설정
    $total = $offset + 1;
    $boards = $boardRepository->getBoardsByPage($offset, $items_per_page);
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
    <h2 class="text-center mb-4">전체 게시글 조회</h2>

    <div class="table-responsive">
        <table class="table table-bordered table-striped fixed-table">
            <thead class="thead-dark">
            <tr>
                <th scope="col" width="50" class="text-center">번호</th>
                <th scope="col" width="200" class="text-center">제목</th>
                <th scope="col" width="200" class="text-center">내용</th>
                <th scope="col" width="170" class="text-center">작성자</th>
                <th scope="col" width="130" class="text-center">날짜</th>
                <th scope="col" width="80" class="text-center">열람권한</th>
            </tr>
            </thead>

            <tbody>
            <?php while ($row = $boards->fetch()): ?>
                <tr>
                    <td class="text-center"><?php echo $total; ?></td> <?php $total++; ?>
                    <td class="text-left">
                        <a href="adminBoardDetails.php?board_id=<?php echo $row['board_id']; ?>">
                            <?php
                            $title = $row['title']; // 제목을 변수에 저장합니다.
                            if (strlen($title) > 27) { // 제목의 길이가 20자 이상인 경우
                                echo substr($title, 0, 27) . ".."; // 20자까지만 표시하고 나머지는 생략 기호로 표시합니다.
                            } else {
                                echo $title; // 그렇지 않으면 전체 제목을 표시합니다.
                            }
                            ?>
                        </a>
                    </td>
                    <td class="text-left content-cell"><?php echo $row['content']; ?></td>
                    <td class="text-center">
                        <?php
                        $user_id = $row['user_id'];
                        $user = $userRepository -> getUserById($user_id);
                        echo $user['email'];
                        ?>
                    </td>
                    <td class="text-center"><?php echo date('Y-m-d', strtotime($row['date'])); ?></td>
                    <td class="text-center"><?php echo $row['openclose'] == 0 ? '불가' : '허용'; ?></td>
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
        width: 1100px; /* 원하는 너비로 설정하세요 */
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