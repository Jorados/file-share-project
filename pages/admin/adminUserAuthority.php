<?php
/**
 * 관리자 -> 사용자 권한을 변경하는 페이지
 */
session_start();
include '/var/www/html/lib/config.php';

use log\UserLogger;
use service\UserService;

$userService = new UserService();
$logger = new UserLogger();

// 검색어 가져오기
$searchType = isset($_GET['search_type']) ? $_GET['search_type'] : null;
$searchQuery = isset($_GET['search_query']) ? $_GET['search_query'] : null;

// 사용자 목록 조회
$users = $userService->getUserAuthorityList('user',$searchType,$searchQuery);
$total = 1;
?>
<!DOCTYPE html>
<html>

<head>
    <?php include '/var/www/html/includes/header.php' ?>
    <meta charset='utf-8'>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://unpkg.com/ionicons@latest/dist/ionicons.js"></script>
</head>

<body>
    <div class="container mt-5">
        <h2 class="text-center mb-5">사용자 목록</h2>

        <nav class="navbar bg-body-tertiary mb-2">
            <div class="container-fluid">
                <form class="d-flex ml-auto" method="GET" action="adminUserAuthority.php">
                    <ion-icon class="mr-3" name="reload" onclick="resetSearchParams()" style="font-size: 40px; color: #1977c9; --ionicon-stroke-width: 45px;"></ion-icon>

                    <select class="form-control mr-2" name="search_type" aria-label="Default select example" style="width: 100px;">
                        <option selected>-선택-</option>
                        <option value="email">이메일</option>
                        <option value="username">이름</option>
                    </select>

                    <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search" name="search_query" id="searchQueryInput" autocomplete="off">
                    <button class="btn btn-outline-primary ml-1" type="submit">Search</button>
                </form>
            </div>
        </nav>

        <div class="scrollable-table">
            <table class="table table-bordered table-striped">
                <thead class="card-header">
                    <tr>
                        <th scope="col" width="200" class="text-center">이메일</th>
                        <th scope="col" width="200" class="text-center">이름</th>
                        <th scope="col" width="200" class="text-center">전화번호</th>
                        <th scope="col" width="200" class="text-center">권한</th>
                        <th scope="col" width="200" class="text-center">권한 변경</th>
                        <th scope="col" width="200" class="text-center">정보 변경</th>
                    </tr>
                </thead>

                <tbody>
                    <?php
                    foreach ($users as $index => $user) {
                    ?>
                        <tr>
                            <td class="text-center"><?= $user->getEmail() ?></td>
                            <td class="text-center"><?= $user->getUsername() ?></td>
                            <td class="text-center"><?= $user->getPhone() ?></td>
                            <td class="text-center" style="color: <?= ($user->getAuthority() == 1) ? 'blue' : 'red'; ?>">
                                <?= ($user->getAuthority() == 1) ? '허용' : '불가'; ?>
                            </td>
                            <td class="text-center">
                                <a href="/action/user/updateRole.php?change_role=<?= $user->getAuthority() ?>&user_id=<?= $user->getUserId() ?>" class="btn btn-sm btn-primary">
                                    권한 변경하기
                                </a>
                            </td>
                            <td class="text-center">
                                <!-- 정보 변경 버튼 -->
                                <a href="/pages/admin/adminUserEdit.php?user_id=<?= $user->getUserId(); ?>" class="btn btn-sm btn-warning">
                                    정보 변경
                                </a>
                            </td>
                        </tr>
                    <?php
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
    <link rel="stylesheet" href="/assets/css/authority.css">
</body>
<script>
    function resetSearchParams() {
        // 현재 페이지 URL을 기반으로한 새로운 URL을 생성
        var url = new URL(window.location.href);

        // 모든 파라미터 제거
        url.search = '';

        // 페이지를 리로드
        window.location.href = url.toString();
    }
</script>
<footer>
    <?php include '/var/www/html/includes/footer.php' ?>
</footer>
</html>
