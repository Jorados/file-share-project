<?php
/**
 * 기본 홈 페이지
 */
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/index.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>로그인</title>

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
<script>
    function submitForm() {
        var formData = new FormData(document.getElementById('loginForm'));

        // 비동기적으로 createUser.php에 POST 요청을 보냄
        fetch('/action/user/loginUser.php', {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                // 서버에서 반환한 데이터를 처리
                if (data.status) {
                    alert(data.content);
                    if(data.role == 1){
                        window.location.href = '/pages/admin/adminHome.php';
                    }
                    else if(data.role == 0){
                        window.location.href = '/pages/user/userHome.php';
                    }
                } else {
                    alert(data.content);
                }
            })
            .catch(data => {
                alert(data.content);
                console.log('Error:', data.content);
            });
    }
</script>
