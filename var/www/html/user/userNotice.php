<?php
include '/var/www/html/database/DatabaseConnection.php';
include '/var/repository/boardRepository.php';

$dbConnection = new DatabaseConnection();
$pdo = $dbConnection->getConnection();

$boardRepository = new BoardRepository($pdo);
try {
    $stmt = $boardRepository->getNotificationBoardItems();
    $total = 1; // 예시 값

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
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
<?php include '/var/www/html/includes/userNavibar.php'?>
<div class="container mt-5">
    <h2 class="text-center mb-4">공지 조회</h2>

    <table class="table table-bordered table-striped">
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
                <td width="50" align="center"><?php echo $total; ?></td> <?php $total++; ?>
                <td class="text-center"><a href="userNoticeDetails.php?board_id=<?php echo $row['board_id']; ?>"><?php echo $row['title']; ?></a></td>
                <td class="text-center"><?php echo $row['content']; ?></td>
                <td class="text-center"><?php echo date('Y-m-d', strtotime($row['date'])); ?></td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>
</body>
<footer>
    <?php include '/var/www/html/includes/footer.php'?>
</footer>
</html>