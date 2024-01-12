<?php
session_start();

// 페이지가 처음 로드되었거나 세션 시작 시간이 설정되지 않았을 때
if (!isset($_SESSION['session_start_time'])) {
    $_SESSION['session_start_time'] = time(); // 현재 시간으로 세션 시작 시간 설정
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

        header("Location: /login/userLogin.php");
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

<?php
// role에 따른 URL 요청 제어
if (isset($_SESSION['role'])) {
    if ($_SESSION['role'] == 'admin' && strpos($_SERVER['REQUEST_URI'], '/admin/') !== 0) {
        header("Location: /admin/adminHome.php"); // admin 페이지로 리디렉션
        exit;
    } elseif ($_SESSION['role'] == 'user' && strpos($_SERVER['REQUEST_URI'], '/user/') !== 0) {
        header("Location: /user/userHome.php"); // user 페이지로 리디렉션
        exit;
    }
}
?>

<!-- 세션값 체크 + 해당 http요청 회원의 role값 파악 후 요청 처리 -->
<?php
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: /login/userLogin.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="ko">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>로그인</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
<div class="bg-light p-3 shadow-sm rounded d-flex justify-content-between">
    <div class="d-flex align-items-center">
        <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true): ?>

            <p class="m-0 mr-3"><strong>환영합니다, <?php echo $_SESSION['email']; ?> 님</strong></p>
            <form method='post' class='m-0 mr-3'>
                <button type='submit' name='logout' class="btn btn-danger">로그아웃</button>
            </form>
            <?php
            // 로그아웃 버튼 클릭 시 세션 제거 및 리다이렉션
            if (isset($_POST['logout'])) {

                // 로그아웃 로그
                include '/var/access_logs/UserLogger.php';
                $logger = new UserLogger();
                $email = $_SESSION['email'];
                $logger->logout($_SERVER['REQUEST_URI'],$email);

                // 세션 파기
                session_unset();
                session_destroy();
                header("Location: /phpinfo.php");
                exit;
            }
            ?>
        <?php else: ?>
            <p class="m-0">로그인이 필요합니다. <a href="/phpinfo.php" class="text-primary">홈 페이지로 이동</a></p>
        <?php endif; ?>
    </div>

    <div class="d-flex align-items-center">
        <p class="m-0 mr-2">세션 남은 시간:</p>
        <p id="session_timer" class="m-0 mr-2"></p>
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
