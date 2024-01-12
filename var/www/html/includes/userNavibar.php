<?php
session_start();

?>

<!DOCTYPE html>
<html lang="ko">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</head>

<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <a class="navbar-brand" href="userHome.php">MK seongjin</a>

    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav mr-auto">

            <?php if ($_SESSION['available'] == 1): ?>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="noticeDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        공지 글
                    </a>
                    <div class="dropdown-menu" aria-labelledby="noticeDropdown">
                        <a class="dropdown-item" href="userNotice.php">공지글 보기</a>
                    </div>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="userBoardDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        게시 글
                    </a>
                    <div class="dropdown-menu" aria-labelledby="userBoardDropdown">
                        <a class="dropdown-item" href="userHome.php">게시글 보기</a>
                        <?php if ($_SESSION['authority'] == 1): ?>
                            <a class="dropdown-item" href="userBoardCreate.php">게시글 작성</a>
                        <?php endif; ?>
                    </div>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="userBoardDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        계정 관리
                    </a>
                    <div class="dropdown-menu" aria-labelledby="userBoardDropdown">
                        <a class="dropdown-item" href="userPassword.php">비밀번호 변경</a>
                    </div>
                </li>
            <?php endif ?>

            <?php if ($_SESSION['available'] == 0): ?>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="userBoardDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        계정 관리
                    </a>
                    <div class="dropdown-menu" aria-labelledby="userBoardDropdown">
                        <a class="dropdown-item" href="userPassword.php">비밀번호 변경</a>
                    </div>
                </li>
            <?php endif ?>

        </ul>

    </div>

</nav>
</body>
