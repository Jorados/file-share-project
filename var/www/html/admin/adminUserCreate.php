<?php
session_start();

include '/var/www/html/database/DatabaseConnection.php';

$dbConnection = new DatabaseConnection();
$pdo = $dbConnection->getConnection();

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $email = $_POST['email'];

        // 이메일 유효성 검사
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $message = "유효한 이메일 형식이 아닙니다. 올바른 이메일 주소를 입력해주세요.";
        } else {
            // 입력받은 email로 user 테이블 조회
            $query = "SELECT * FROM user WHERE email = :email";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->execute();
            $user = $stmt->fetch();

            if ($user) {
                $message = "이미 사용 중인 이메일입니다. 다른 이메일을 사용해주세요.";
            } else {
                $password = $_POST['password'];
                $username = $_POST['username'];
                $phone = $_POST['phone'];

                // 비밀번호 유효성 검사
                if (!preg_match("/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$/", $password)) {
                    $message = "비밀번호는 영어와 숫자를 모두 포함하고, 최소 8자 이상이어야 합니다.";
                } else {
                    // 비밀번호 해싱
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                    // 회원 가입
                    $query = "INSERT INTO user (email,password,username,phone) VALUES (:email, :password, :username, :phone)";
                    $stmt = $pdo->prepare($query);
                    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
                    $stmt->bindParam(':password', $hashed_password, PDO::PARAM_STR);
                    $stmt->bindParam(':username', $username, PDO::PARAM_STR);
                    $stmt->bindParam(':phone', $phone, PDO::PARAM_STR);
                    $stmt->execute();

                    $message = "회원 가입이 성공적으로 완료되었습니다.";

                    header("Location: /admin/adminHome.php");
                    exit;
                }
            }
        }
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>


<!DOCTYPE html>
<html lang="ko">
<head>
    <?php include '/var/www/html/includes/header.php'?>
    <?php include '/var/www/html/includes/adminNavibar.php'?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>회원 생성</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <!-- 게시글 상세 정보 -->
    <div class="card mx-auto mb-5" style="max-width: 600px;">
        <div class="card-header bg-dark text-white" style="max-height: 90px;">
            <h3 class="text-center">일반 사용자 생성</h3>
        </div>


        <div class="card-body">
            <?php if (!empty($message)): ?>
                <div class="alert alert-info">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <div class="d-flex justify-content-center">
                <form action="" method="post" class="col-md-9">

                    <div class="form-group">
                        <label for="email">이메일</label>
                        <input type="text" id="email" name="email" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="password">비밀번호</label>
                        <input type="password" id="password" name="password" class="form-control">
                    </div>

                    <div class="form-group">
                        <label for="username">이름</label>
                        <input type="text" id="username" name="username" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="phone">전화번호</label>
                        <input type="text" id="phone" name="phone" class="form-control">
                    </div>

                    <br> 
                    <button type="submit" class="btn btn-primary btn-block">회원 가입</button>
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
