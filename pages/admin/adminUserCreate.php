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
    <div class="card mx-auto mb-5" style="max-width: 600px;">
        <div class="card-header bg-dark text-white" style="max-height: 90px;">
            <h3 class="text-center">일반 사용자 생성</h3>
        </div>


        <div class="card-body">
            <div class="d-flex justify-content-center">
                <form action="/action/createUser.php" method="post" class="col-md-9" id="signupForm">

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
                    <button type="button" class="btn btn-primary btn-block" onclick="submitForm()">회원 가입</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function submitForm() {
        var formData = new FormData(document.getElementById('signupForm'));

        // 비동기적으로 createUser.php에 POST 요청을 보냄
        fetch('/action/user/createUser.php', {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                // 서버에서 반환한 데이터를 처리
                if (data.status) {
                    alert(data.content);
                    window.location.href = '/pages/admin/adminHome.php';
                } else {
                    alert(data.content);
                }
            })
            .catch(data => {
                alert(data.content);
                console.log('Error:', data.message);
            });
    }
</script>
</body>
<footer>
    <?php include '/var/www/html/includes/footer.php'?>
</footer>
</html>
