<?php
/**
 * 헤더파일 - 권한,세션,세션시간 검사 / 네이게이션 / 로그아웃
 */
session_start();

// 페이지가 처음 로드되었거나 세션 시작 시간이 설정되지 않았을 때
if (!isset($_SESSION['session_start_time'])) {
    $_SESSION['session_start_time'] = time(); // 현재 시간으로 세션 시작 시간 설정
}


// 세션값 체크 + 해당 http요청 회원의 role값 파악 후 요청 처리
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: /index.php");
    exit;
} elseif (isset($_SESSION['role'])) {
    $currentPath = $_SERVER['REQUEST_URI'];
    if ($_SESSION['role'] == 'user' && (in_array('admin', explode("/", $currentPath)))) {
        header("Location: /pages/home.php"); // user 페이지로 리디렉션
        exit;
    }
} else {
    header("Location: /index.php"); // 세션 값이 없으면 index.php로 리디렉션
    exit;
}

// available 값 체크 해서 경로 제어
if ($_SESSION['available'] == 0 && strpos($_SERVER['REQUEST_URI'], '/pages/changePassword.php') === false) {
    header("Location: /pages/changePassword.php");
    exit;
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
                <a class="navbar-brand mx-2" href="/pages/home.php"><strong>MK seongjin</strong></a>
                <nav class="navbar navbar-expand-lg navbar-light bg-light">
                    <div class="collapse navbar-collapse" id="navbarNav">
                        <ul class="navbar-nav mr-auto">

                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="adminDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    사용자 관리
                                </a>
                                <div class="dropdown-menu" aria-labelledby="adminDropdown">
                                    <a class="dropdown-item" href="/pages/admin/adminUserCreate.php">사용자 생성</a>
                                    <a class="dropdown-item" href="/pages/admin/adminUserAuthority.php">사용자 목록</a>
                                </div>
                            </li>

                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="boardDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    게시글 관리
                                </a>
                                <div class="dropdown-menu" aria-labelledby="boardDropdown">
                                    <a class="dropdown-item" href="/pages/home.php">게시글 관리</a>
                                    <a class="dropdown-item" href="/pages/notice.php">공지글 관리</a>
                                    <a class="dropdown-item" href="/pages/boardCreate.php">게시글 작성</a>
                                </div>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="editDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    계정 관리
                                </a>
                                <div class="dropdown-menu" aria-labelledby="editDropdown">
                                    <a class="dropdown-item" href="/pages/changePassword.php">비밀번호 변경</a>
                                </div>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="logDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    로그 관리
                                </a>
                                <div class="dropdown-menu" aria-labelledby="logDropdown">
                                    <a class="dropdown-item" href="/pages/admin/adminLogDetails.php">로그 보기</a>
                                </div>
                            </li>
                        </ul>
                    </div>
                </nav>
            <?php elseif ($_SESSION['role'] == 'user'): ?>
                <a class="navbar-brand mx-2" href="home.php"><strong>MK seongjin</strong></a>
                <nav class="navbar navbar-expand-lg navbar-light bg-light">
                    <div class="collapse navbar-collapse" id="navbarNav">
                        <ul class="navbar-nav mr-auto">

                            <?php if ($_SESSION['available'] == 1): ?>
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" href="#" id="noticeDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        공지 글
                                    </a>
                                    <div class="dropdown-menu" aria-labelledby="noticeDropdown">
                                        <a class="dropdown-item" href="notice.php">공지글 보기</a>
                                    </div>
                                </li>
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" href="#" id="userBoardDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        게시 글
                                    </a>
                                    <div class="dropdown-menu" aria-labelledby="userBoardDropdown">
                                        <a class="dropdown-item" href="home.php">게시글 보기</a>
                                        <?php if ($_SESSION['authority'] == 1): ?>
                                            <a class="dropdown-item" href="boardCreate.php">게시글 작성</a>
                                        <?php endif; ?>
                                    </div>
                                </li>
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" href="#" id="userBoardDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        계정 관리
                                    </a>
                                    <div class="dropdown-menu" aria-labelledby="userBoardDropdown">
                                        <a class="dropdown-item" href="/pages/changePassword.php">비밀번호 변경</a>
                                    </div>
                                </li>
                            <?php endif ?>

                            <?php if ($_SESSION['available'] == 0): ?>
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" href="#" id="userBoardDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        계정 관리
                                    </a>
                                    <div class="dropdown-menu" aria-labelledby="userBoardDropdown">
                                        <a class="dropdown-item" href="/pages/changePassword.php">비밀번호 변경</a>
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
        <p class="m-0 mr-4"><strong>환영합니다, <?= ($_SESSION['role'] == 'admin') ? '관리자 ' : '사용자 ' ?> <?= $_SESSION['email']; ?> 님</strong></p>

        <p class="m-0 mr-2">세션 남은 시간:</p>
        <p id="session_timer" class="m-0 mr-2"></p>

        <form method='post' class='m-0 mr-2' onclick="logoutAndRedirect()">
            <button type='submit' name='logout' class="btn btn-danger">로그아웃</button>
        </form>
    </div>
</div>
<script>
    const remainingTime = <?= $remaining_time ?>;
    let remainingSeconds = remainingTime;
</script>
<script src="/assets/js/header.js"></script>
</body>

