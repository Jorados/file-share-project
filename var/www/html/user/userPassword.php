<?php
session_start();

include '/var/www/html/database/DatabaseConnection.php';
include '/var/repository/userRepository.php';

$dbConnection = new DatabaseConnection();
$pdo = $dbConnection->getConnection();

$userRepository = new UserRepository($pdo);
try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $password = $_POST['password'];
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // 현재 세션의 이메일 가져오기
        $email = $_SESSION['email'];

        // 이메일을 사용하여 사용자 비밀번호 업데이트
        $userRepository->updateUserPassword($email, $hashed_password);

        // 사용자 available 값을 업데이트
        $userRepository->updateAvailableStatus($email);

        echo "비밀번호가 성공적으로 변경되었습니다.";
        header("Location: /phpinfo.php");
        exit;
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <?php include '/var/www/html/includes/header.php'?>
    <?php include '/var/www/html/includes/userNavibar.php'?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>회원 생성</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <!-- 게시글 상세 정보 -->
    <div class="card mx-auto mb-5" style="max-width: 500px;">
        <div class="card-header bg-dark text-white" style="max-height: 90px;">
            <h3 class="text-center">비밀번호 변경</h3>
        </div>


        <div class="card-body">
            <div class="d-flex justify-content-center">
                <form action="" method="post" class="col-md-9">
                    <div class="form-group">
                        <label for="password">비밀번호</label>
                        <input type="password" id="password" name="password" class="form-control">
                    </div>

                    <button type="submit" class="btn btn-primary btn-block" >변경</button>
                </form>
            </div>
        </div>
    </div>
</div>
</body>

<footer>
    <?php include '/var/www/html/includes/footer.php'?>
</footer>
</html>
