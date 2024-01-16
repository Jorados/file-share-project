<?php
include '/var/www/html/lib/config.php';

use database\DatabaseConnection;
use repository\BoardRepository;
use repository\UserRepository;

$pdo = DatabaseConnection::getInstance() -> getConnection();
$userRepository = new UserRepository($pdo);

try {
    // status가 'notification'인 board 조회
    $boardRepository = new BoardRepository($pdo);
    $stmt = $boardRepository->getNotificationBoardItems();

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
<div class="container mt-5">
    <h2 class="text-center mb-4">공지 조회</h2>

    <div class="row">
        <?php while ($row = $stmt->fetch()): ?>
            <div class="col-md-4 mt-5 mb-4">
                <div class="card shadow">
                    <div class="card-body">
                        <h5 class="card-title">
                            <a href="adminBoardDetails.php?board_id=<?php echo $row['board_id']; ?>">
                                <?php
                                $title = $row['title'];
                                if (strlen($title) > 27) {
                                    echo substr($title, 0, 27) . "..";
                                } else {
                                    echo $title;
                                }
                                ?>
                            </a>
                        </h5>
                        <p class="card-text"><?php echo $row['content']; ?></p>
                        <p class="card-text">
                            작성자: <?php
                            $user_id = $row['user_id'];
                            $user = $userRepository->getUserById($user_id);
                            echo $user['email'];
                            ?>
                        </p>
                        <p class="card-text">날짜: <?php echo date('Y-m-d', strtotime($row['date'])); ?></p>
                        <p class="card-text">열람권한: 허용</p>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</div>
</body>
<footer>
    <?php include '/var/www/html/includes/footer.php'?>
</footer>
</html>
<style>
    /* 추가된 스타일 */
    .card {
        min-height: 230px; /* 카드의 최소 높이 설정 */
        transition: transform 0.3s, box-shadow 0.3s; /* 변화에 대한 애니메이션 효과 추가 */
        border-radius : 20px;
    }

    .card:hover {
        transform: scale(1.05); /* 마우스 호버 시 약간 확대 */
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.2); /* 그림자 효과 추가 */
    }

    .card-title {
        font-size: 1.25rem; /* 카드 제목 글꼴 크기 설정 */
    }
</style>
