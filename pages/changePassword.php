<?php
/**
 * 사용자 -> 비밀번호 변경 페이지
 */

?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <?php include '/var/www/html/includes/header.php'?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>회원 생성</title>
    <link rel="stylesheet" href="/assets/css/index.css">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <!-- 게시글 상세 정보 -->
    <div class="card mx-auto mb-5" style="max-width: 600px;">
        <div class="card-header custom-header">
            <h3 class="text-center">비밀번호 변경</h3>
        </div>

        <div class="card-body">
            <div class="d-flex justify-content-center">
                <form action="/action/user/changePassword.php" method="post" class="col-md-9" id="passwordForm">
                    <div class="form-group">
                        <label for="password">비밀번호</label>
                        <input type="password" id="password" name="password" class="form-control">
                        <small class="form-text text-muted">영어와 숫자를 포함한 8자 이상의 비밀번호를 입력하세요.</small>
                    </div>
                    <div class="form-group">
                        <label for="confirmPassword">비밀번호 확인</label>
                        <input type="password" id="confirmPassword" name="confirmPassword" class="form-control">
                        <small class="form-text text-muted">동일한 비밀번호를 입력해주세요.</small>
                    </div>
                    <button type="button" class="btn btn-primary btn-block" onclick="submitForm()">변경</button>
                </form>
            </div>
        </div>
    </div>
</div>
<script src="/assets/js/user/changePassword.js"></script>
</body>

<footer>
    <?php include '/var/www/html/includes/footer.php'?>
</footer>
</html>
