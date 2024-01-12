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
    <a class="navbar-brand" href="adminHome.php">MK seongjin</a>

    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav mr-auto">
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="adminDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    사용자 관리
                </a>
                <div class="dropdown-menu" aria-labelledby="adminDropdown">
                    <a class="dropdown-item" href="adminUserCreate.php">사용자 생성</a>
                    <a class="dropdown-item" href="adminAuthority.php">사용자 목록</a>
                </div>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="boardDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    게시글 관리
                </a>
                <div class="dropdown-menu" aria-labelledby="boardDropdown">
                    <a class="dropdown-item" href="adminHome.php">게시글 관리</a>
                    <a class="dropdown-item" href="adminNotice.php">공지글 관리</a>
                    <a class="dropdown-item" href="adminBoardCreate.php">게시글 작성</a>
                </div>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="logDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    로그 관리
                </a>
                <div class="dropdown-menu" aria-labelledby="logDropdown">
                    <a class="dropdown-item" href="logDetails.php">로그 보기</a>
                </div>
            </li>
        </ul>
    </div>
</nav>
</body>

</html>