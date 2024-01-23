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
                <form action="/action/user/changePassword.php" method="post" class="col-md-9" id="passwordForm">
                    <div class="form-group">
                        <label for="password">비밀번호</label>
                        <input type="password" id="password" name="password" class="form-control">
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
