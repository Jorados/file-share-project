<?php
/**
 *  관리자 -> 사용자 계정 생성 페이지
 */
include_once  '/var/www/html/lib/config.php';
include_once  LIB_PATH . '/sessionCheck.php';
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
    <div class="card mx-auto mb-5" style="max-width: 700px;">
        <div class="card-header custom-header" style="max-height: 90px;">
            <h3 class="text-center">일반 사용자 생성</h3>
        </div>

        <div class="card-body">
            <div class="d-flex justify-content-center">
                <form action="/action/createUser.php" method="post" class="col-md-9" id="signupForm">

                    <div class="form-group">
                        <label for="email">이메일</label>
                        <input type="text" id="email" name="email" class="form-control" required>
                        <small class="form-text text-muted">유효한 이메일 주소를 입력해주세요.</small>
                    </div>

                    <div class="form-group">
                        <label for="password">비밀번호</label>
                        <input type="password" id="password" name="password" class="form-control">
                        <small class="form-text text-muted">영어와 숫자를 포함한 8자 이상의 비밀번호를 입력해주세요.</small>
                    </div>

                    <div class="form-group">
                        <label for="username">이름</label>
                        <input type="text" id="username" name="username" class="form-control" required>
                        <small class="form-text text-muted">실명을 입력해주세요.</small>
                    </div>

                    <div class="form-group mb-4">
                        <label for="phone">전화번호</label>
                        <input type="text" id="phone" name="phone" class="form-control">
                        <small class="form-text text-muted">11자리의 숫자만 입력해주세요.</small>
                    </div>

                    <button type="button" class="btn btn-primary btn-block mb-2" onclick="submitForm()">회원 가입</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="/assets/js/user/createUser.js"></script>
</body>
<footer>
    <?php include '/var/www/html/includes/footer.php'?>
</footer>
</html>
