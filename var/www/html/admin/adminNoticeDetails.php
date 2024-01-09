<?php
session_start();
include '/var/www/html/database/DatabaseConnection.php';
include '/var/repository/boardRepository.php';
include '/var/repository/userRepository.php';
include '/var/repository/attachmentRepository.php';

$dbConnection = new DatabaseConnection();
$pdo = $dbConnection->getConnection();

// 게시글 ID를 URL 파라미터에서 가져옵니다.
// isset() -> 값이 할당되었는지 확인하는 함수
$board_id = isset($_GET['board_id']) ? $_GET['board_id'] : null;
if (!$board_id) die("게시글 ID가 제공되지 않았습니다.");

$boarRepository = new BoardRepository($pdo);
$userRepository = new UserRepository($pdo);
$attachmentRepository = new AttachmentRepository($pdo);

try {
    // 해당 ID의 게시글을 데이터베이스에서 가져옵니다.
    $board = $boarRepository -> getBoardById($board_id);
    if (!$board) die("해당 ID의 게시글을 찾을 수 없습니다.");

    // 사용자 정보 조회
    $user_id = $board['user_id'];
    $user = $userRepository -> getUserById($user_id);
    if (!$user) die("해당 ID의 사용자를 찾을 수 없습니다.");
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}

// 게시글 삭제 기능
if (isset($_POST['delete_post'])) {
    try {
        $stmt = $boarRepository -> deleteBoardById($board_id);
        header("Location: adminNotice.php");
        exit;
    } catch (PDOException $e) {
        echo "게시글 삭제 중 오류가 발생했습니다: " . $e->getMessage();
    }
}

$attachments = $attachmentRepository->getAttachmentsByBoardId($board_id);
?>


<!DOCTYPE html>
<html>
<head>
    <meta charset='utf-8'>
    <title>게시글 상세보기</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include '/var/www/html/includes/header.php'?>
<?php include '/var/www/html/includes/adminNavibar.php'?>
<div class="container mt-5">

    <div class="card mx-auto mb-5" style="max-width: 500px;">
        <div class="card-header bg-dark text-white" style="max-height: 90px;">
            <h3 class="text-center">공지 상세 조회</h3>
        </div>
        <div class="card-body">
            <p class="card-text">제목 : <?php echo $board['title']; ?></p>
            <p class="card-text">내용 : <?php echo $board['content']; ?></p>
            <ul>
                <?php
                $filepath = '/var/www/html/file/uploads/';
                if (empty($attachments)) {
                    echo '<li>첨부파일이 없습니다.</li>';
                } else {
                    foreach ($attachments as $attachment) {
                        if (file_exists($filepath)) {
                            echo '<li><a href="/file/download.php?file=' . urlencode($attachment['filename']) . '">' . $attachment['filename'] . '</a></li>';
                        }
                    }
                }
                ?>
            </ul>
            <p class="card-text">작성자 : <?php echo $user['username']; ?></p>
            <p class="card-text">작성자 이메일 : <?php echo $user['email']; ?></p>
            <p class="card-text">작성일 : <?php echo date('Y-m-d', strtotime($board['date'])); ?></p>

            <div class="row mt-3">
                <div class="col-md-6">
                    <form method="post" action="" class="d-inline-block">
                        <input type="submit" name="delete_post" value="삭제" class="btn btn-danger" onclick="return confirm('정말로 삭제하시겠습니까?');">
                    </form>
                    <a href="adminBoardEdit.php?id=<?php echo $board['board_id']; ?>" class="btn btn-warning">수정</a>
                </div>
            </div>

        </div>
    </div>
</div>
</body>
<footer>
    <?php include '/var/www/html/includes/footer.php'?>
</footer>
</html>