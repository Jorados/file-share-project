<?php
session_start();

include '/var/www/html/database/DatabaseConnection.php';
include '/var/www/html/repository/userRepository.php';
include '/var/access_logs/UserLogger.php';

$dbConnection = new DatabaseConnection();
$pdo = $dbConnection->getConnection();

$logger = new UserLogger();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    try {
        $userRepository = new UserRepository($pdo);
        $user = $userRepository->loginUser($email, $password);

        $user->getUser_id;

        if ($user) {
            $_SESSION['loggedin'] = true;
            $_SESSION['email'] = $email;
            $_SESSION['session_start_time'] = time(); // 세션 시작 시간 설정
            $_SESSION['available'] = $user['available'];
            $_SESSION['authority'] = $user['authority'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['user_id'] = $user['user_id'];

            // 로그인 후 리다이렉션
            if ($user['role'] == 'admin') {
                $logger->login($_SERVER['REQUEST_URI'], $email);
                header("Location: /admin/adminHome.php");

                Util::serverRedirect("/admin/adminHome.php");
            } elseif ($user['role'] == 'user') {
                $logger->login($_SERVER['REQUEST_URI'], $email);
                header("Location: /user/userHome.php");
            } else {
                echo "알 수 없는 역할입니다.";
            }
            exit;
        } else {
            $_SESSION['error_message'] = "유효하지 않은 이메일 또는 비밀번호입니다.";
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="ko">

<head>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>로그인</title>
</head>

<body>
    <?php include INCLUDE_PATH . '/nomalHeader.php' ?>
    <div class="container mt-5">
        <div class="card mx-auto mb-5" style="max-width: 600px;">
            <div class="card-header bg-dark text-white" style="max-height: 90px;">
                <h3 class="text-center">로그인</h3>
            </div>

            <div class="card-body">
                <?php if (!empty($message)) : ?>
                    <div class="alert alert-info">
                        <?= $message; ?>
                    </div>
                <?php endif; ?>

                <div class="d-flex justify-content-center">
                    <form action="" method="post" class="col-md-9">

                        <div class="form-group">
                            <label for="email">이메일</label>
                            <input type="text" id="email" name="email" class="form-control" required>
                            <small class="form-text text-muted">이메일 형식으로 입력하세요.</small>
                        </div>

                        <div class="form-group">
                            <label for="password">비밀번호</label>
                            <input type="password" id="password" name="password" class="form-control">
                            <small class="form-text text-muted">비밀번호를 입력하세요.</small>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">로그인</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

</body>
<footer>
    <?php include INCLUDE_PATH . '/footer.php' ?>
</footer>

</html>
<script>
    // PHP 세션에서 에러 메시지를 가져옴
    <?php if (isset($_SESSION['error_message'])) : ?>
        var errorMessage = "<?php echo $_SESSION['error_message']; ?>";
        alert(errorMessage); // 에러 메시지를 알림창으로 표시
        <?php unset($_SESSION['error_message']); // 세션에서 에러 메시지 삭제 
        ?>
    <?php endif; ?>
</script>