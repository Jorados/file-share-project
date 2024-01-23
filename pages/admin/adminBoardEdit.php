<?php
/**
 * 관리자 -> 글 수정 페이지
 * 다시 건드려야함.
 */

session_start();
include '/var/www/html/lib/config.php';

use database\DatabaseConnection;

$pdo = DatabaseConnection::getInstance() -> getConnection();

$id = isset($_GET['id']) ? $_GET['id'] : null;
try {
    // 해당 ID의 게시글을 데이터베이스에서 가져옵니다.
    $query = "SELECT * FROM board WHERE id = :id";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $post = $stmt->fetch();

    if (!$post) {
        die("해당 ID의 게시글을 찾을 수 없습니다.");
    }

    // 해당 게시글 수정하기.
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $title = $_POST['title'];
        $content = $_POST['content'];

        // 게시글 업데이트
        $updateQuery = "UPDATE board SET title = :title, content = :content WHERE id = :id";
        $stmt = $pdo->prepare($updateQuery); //쿼리 준비
        $stmt->bindParam(':title', $title, PDO::PARAM_STR);
        $stmt->bindParam(':content', $content, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        echo "<p>게시글이 성공적으로 수정되었습니다.</p>";

        header("Location: adminBoardList.php");  // boardList.php로 리다이렉션
        exit;  // 리다이렉션 후 스크립트 종료
    }

} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>게시글 수정</title>
</head>
<body>
<?php include '/var/www/html/includes/header.php'?>
<?php include '/var/www/html/includes/adminNavibar.php'?>
<h2 align="center">게시글 수정</h2>

<div align="center">
    <form action="" method="post">
        <label for="title">제목</label><br>
        <input type="text" id="title" name="title" value="<?php echo $post['title']?>" required><br>

        <br>
        <label for="content">내용</label><br>
        <textarea id="content" name="content" rows="5" required><?php echo$post['content']; ?></textarea><br>

        <br>
        <input type="submit" value="게시글 수정">
    </form>
</div>
</body>
<footer>
    <?php include '/var/www/html/includes/footer.php'?>
</footer>
</html>