<?php
session_start();
$user_id = $_SESSION['user_id'];

include '/var/www/html/database/DatabaseConnection.php';
include '/var/repository/boardRepository.php';

$dbConnection = new DatabaseConnection();
$pdo = $dbConnection->getConnection();

$boardRepository = new BoardRepository($pdo);

$items_per_page = 10;
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($current_page - 1) * $items_per_page;

try {
    $total_items = $boardRepository->getTotalItemsByUserId($user_id);
    $total_pages = ceil($total_items / $items_per_page);

    $stmt = $boardRepository->getBoardItemsByUserId($user_id, $offset, $items_per_page);
    $total = $offset + 1;

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset='utf-8'>
    <title>전체 게시글 조회</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include '/var/www/html/includes/header.php'?>
<?php include '/var/www/html/includes/userNavibar.php'?>
<div class="container mt-5">
    <h2 class="text-center mb-4">내가 쓴 글 조회</h2>

    <div class="table-responsive">
        <table class="table table-bordered table-striped fixed-table">
            <thead class="thead-dark">
            <tr>
                <th scope="col" width="200" class="text-center">번호</th>
                <th scope="col" width="200" class="text-center">제목</th>
                <th scope="col" width="200" class="text-center">내용</th>
                <th scope="col" width="200" class="text-center">날짜</th>
            </tr>
            </thead>

            <tbody>
            <?php while ($row = $stmt->fetch()): ?>
                <tr>
                    <td class="text-center"><?php echo $total; ?></td> <?php $total++; ?>
                    <td class="text-center">
                        <a href="userBoardDetails.php?board_id=<?php echo $row['board_id']; ?>">
                            <?php
                            $title = $row['title']; // 제목을 변수에 저장합니다.
                            if (strlen($title) > 30) { // 제목의 길이가 20자 이상인 경우
                                echo substr($title, 0, 30) . ".."; // 20자까지만 표시하고 나머지는 생략 기호로 표시합니다.
                            } else {
                                echo $title; // 그렇지 않으면 전체 제목을 표시합니다.
                            }
                            ?>
                        </a>
                    </td>
                    <td class="text-center"><?php echo $row['openclose'] == 0 ? '볼 수 없음' : $row['content']; ?></td>
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