<?php
/**
 * 기본 홈 페이지
 */

session_start();

include_once  '/var/www/html/lib/config.php';
use util\Util;

// 세션값이 있으면 home 페이지로 이동
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    Util::serverRedirect("/pages/home.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/index.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>로그인</title>
    <script src="/assets/js/user/loginUser.js"></script>
</head>
<body>
<?php include '/var/www/html/includes/nomalHeader.php'?>
<div class="card">
    <div class="card-header custom-header">
        <h3>로그인</h3>
    </div>
    <div class="card-body">
        <form action="/action/user/loginUser.php" method="post" id="loginForm">
            <div class="form-group">
                <label for="email">이메일</label>
                <input type="text" id="email" name="email" class="form-control" required>
                <small class="form-text text-muted">이메일 형식으로 입력하세요.</small>
            </div>
            <div class="form-group">
                <label for="password">비밀번호</label>
                <input type="password" id="password" name="password" class="form-control">
                <small class="form-text text-muted">영어와 숫자를 포함한 8자 이상의 비밀번호를 입력하세요.</small>
            </div>
            <button type="button" class="btn btn-primary btn-block" onclick="submitForm()">로그인</button>
        </form>
    </div>
</div>
<?php include '/var/www/html/includes/footer.php'?>
</body>
</html>

