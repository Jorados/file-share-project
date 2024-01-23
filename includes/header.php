<?php

/**
 * 헤더파일 - 권한,세션,세션시간 검사 / 네이게이션 / 로그아웃
 */

session_start();
use log\UserLogger;

// 페이지가 처음 로드되었거나 세션 시작 시간이 설정되지 않았을 때
if (!isset($_SESSION['session_start_time'])) {
    $_SESSION['session_start_time'] = time(); // 현재 시간으로 세션 시작 시간 설정
}

//세션값 체크 + 해당 http요청 회원의 role값 파악 후 요청 처리
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
header("Location: /index.php");
exit;
}

// role에 따른 URL 요청 제어
if (isset($_SESSION['role'])) {
    if ($_SESSION['role'] == 'admin' && !in_array('admin', explode("/",$_SERVER['REQUEST_URI']))) {
        header("Location: /pages/admin/adminHome.php"); // admin 페이지로 리디렉션
        exit;
    } else if ($_SESSION['role'] == 'user' && !in_array('user', explode("/", $_SERVER['REQUEST_URI']))) {
        header("Location: /pages/user/userHome.php"); // user 페이지로 리디렉션
        exit;
    }
}

// 세션 시작 시간을 현재 시간으로 업데이트
$_SESSION['session_start_time'] = time();

if (isset($_SESSION['session_start_time'])) {
    $session_duration = 1800;
    $current_time = time();

    //경과시간
    $elapsed_time = $current_time - $_SESSION['session_start_time'];

    //경과시간이 30분보다 크다면 세션파기.
    if ($elapsed_time >= $session_duration) {
        $remaining_time=0;
        // 세션 파기
        session_unset();
        session_destroy();

        header("Location: /index.php");
        exit;
    }
    // 남은 시간 계산
    else {
        $remaining_time = $session_duration - $elapsed_time;
    }
} else {
    echo '세션 시작 시간이 없습니다.';
}

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
<div class="bg-light p-3 shadow-sm rounded d-flex justify-content-between">
    <div class="d-flex align-items-center">
            <?php if($_SESSION['role'] == 'admin'): ?>
                <a class="navbar-brand mx-2" href="adminHome.php"><strong>MK seongjin</strong></a>
                <nav class="navbar navbar-expand-lg navbar-light bg-light">
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
            <?php elseif ($_SESSION['role'] == 'user'): ?>
                <a class="navbar-brand mx-2" href="userHome.php"><strong>MK seongjin</strong></a>
                <nav class="navbar navbar-expand-lg navbar-light bg-light">
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
            <?php else: ?>
                <a class="navbar-brand mx-2" href="index.php"><strong>MK seongjin</strong></a>
            <?php endif; ?>
    </div>

    <div class="d-flex align-items-center">
        <p class="m-0 mr-4"><strong>환영합니다, <?php echo $_SESSION['email']; ?> 님</strong></p>

        <p class="m-0 mr-2">세션 남은 시간:</p>
        <p id="session_timer" class="m-0 mr-2"></p>

        <form method='post' class='m-0 mr-2'>
            <button type='submit' name='logout' class="btn btn-danger">로그아웃</button>
        </form>
        <?php

        // 로그아웃 버튼 클릭 시 세션 제거 및 리다이렉션
        if (isset($_POST['logout'])) {
            // 로그아웃 로그
            $logger = new UserLogger();
            $email = $_SESSION['email'];
            $logger->logout($_SERVER['REQUEST_URI'],$email);

            // 세션 파기
            session_unset();
            session_destroy();
            header("Location: /index.php");
            exit;
        }
        ?>
    </div>

</div>
</body>



<!--위에서 받아온 남은 시간을 계산하는 곳 (1초단위로 시간을 -1씩해서 분/초 형태로  표현-->
<script>
    const remainingTime = <?php echo $remaining_time; ?>;
    let remainingSeconds = remainingTime;

    // 시간 형태로 계산 후 표현
    function updateSessionTimer() {
        if (remainingSeconds > 0) {
            remainingSeconds--;
            const minutes = Math.floor(remainingSeconds / 60); // floor -> 소수점 버림 -> 몇 분 남았는지
            const seconds = remainingSeconds % 60; // 나머지 값 -> 몇 초 남았는지
            document.getElementById('session_timer').innerText = `${minutes}분 ${seconds}초`;
        } else {
            document.getElementById('session_timer').innerText = '세션 종료';
        }
    }

    // 1초 마다 updateSessionTimer 함수호출
    window.onload = function() {
        updateSessionTimer();
        setInterval(updateSessionTimer, 1000);
    };
</script>
