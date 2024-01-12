<?php
include '/var/www/html/database/DatabaseConnection.php';
include '/var/www/html/repository/boardRepository.php';
include '/var/www/html/repository/userRepository.php';

$dbConnection = new DatabaseConnection();
$pdo = $dbConnection->getConnection();

$userRepository = new UserRepository($pdo);
try {
    // status가 'notification'인 board 조회
    $boardRepository = new BoardRepository($pdo);
    $stmt = $boardRepository -> getNotificationBoardItems();

    $total = 1;
} catch (PDOException $e) {
    throw new PDOException($e->getMessage(), (int)$e->getCode());
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
<?php include '/var/www/html/includes/adminNavibar.php'?>
<div class="container mt-5">
    <h2 class="text-center mb-4">공지 조회</h2>

    <table class="table table-bordered table-striped">
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
        <?php while ($row = $stmt->fetch()): ?>
            <tr>
                <td width="50" align="center"><?php echo $total; ?></td> <?php $total++; ?>
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
                <td class="text-left"><?php echo $row['content']; ?></td>
                <td class="text-center">
                    <?php
                    $user_id = $row['user_id'];
                    $user = $userRepository -> getUserById($user_id);
                    echo $user['email'];
                    ?>
                </td>
                <td class="text-center"><?php echo date('Y-m-d', strtotime($row['date'])); ?></td>
                <td class="text-center">허용</td>
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