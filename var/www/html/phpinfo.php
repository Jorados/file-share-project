<!DOCTYPE html>
<html lang="ko">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
<!--    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">-->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">
</head>

<body>
<?php include '/var/www/html/includes/nomalHeader.php'?>
<div class="container mt-5">
    <div class="container mt-5">
        <!-- 게시글 상세 정보 -->
        <div class="card mx-auto mb-5" style="max-width: 600px;">
            <div class="card-header bg-dark text-white" style="max-height: 90px;">
                <h2 class="text-center">파일공유 게시판 홈 </h2>
            </div>

            <div class="card-body">

                <div align="center" class="info-container bg-light p-3 rounded mt-4">
                    <h3>
                        <p>로그인 필수</p>
                    </h3>

                    <br>
                    <br>

                    <div class="row justify-content-center">
                        <div class="col-md-9">
                            <div class="custom-container">
                                <button class="btn btn-primary btn-block" onclick="location.href='/login/userLogin.php'">로그인</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-HwwvtgBNo3bZJJLYd8oVXjrBZt8cqVSpeBNS5n7C8IVInixGAoxmnlMuBnhbgrkm" crossorigin="anonymous"></script>
</body>

<footer>
    <?php include '/var/www/html/includes/footer.php'?>
</footer>



