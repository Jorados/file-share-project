<?php
/**
 * 관리자 -> 로그 조회 페이지
 */
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>Log Details</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include '/var/www/html/includes/header.php'?>

<div class="container mt-5">
    <h2 class="mb-4">로그 보기</h2>
    <form method="post" action="logDetails.php" class="mb-4">
        <div class="mb-3">
            <label for="date" class="form-label">날짜 선택</label>
            <input type="date" id="date" name="date" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-outline-primary">로그 보기</button>
    </form>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-3">로그 상세 조회</h3>
        <form class="d-flex" role="search">
            <input class="form-control me-2" type="search" id="search" placeholder="검색어 입력" aria-label="검색어 입력">
            <button class="btn btn-outline-primary mx-1" type="button" onclick="searchLog()">search</button>
            <button class="btn btn-outline-secondary" type="button" onclick="resetSearch()">refresh</button>
        </form>
    </div>

    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $selectedDate = $_POST["date"];

        // 선택된 날짜로부터 년도와 월을 추출
        $year = date('Y', strtotime($selectedDate));
        $month = date('m', strtotime($selectedDate));

        $logFilePath = "/var/www/html/file/logs/{$year}/{$month}/{$selectedDate}.log";

        if (file_exists($logFilePath)) {
            echo "<pre class='bg-light p-3' id='logContent'>";   //--> <pre> 이 태그 안에 있는 문자들은 공백, 줄바꿈 등 그대로 유지됨.
            echo htmlspecialchars(file_get_contents($logFilePath)); // htmlspecialchars --> 자바스크립트 입력 방지
            echo "</pre>";
        } else {
            echo "<p class='text-danger'>날짜를 선택 해주세요. 해당 날짜의 로그 정보가 없습니다.</p>";
        }
    }
    ?>
    <script src="/assets/js/searchLog.js"></script>
</div>

</body>
</html>
