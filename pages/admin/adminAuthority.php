<?php
session_start();
include '/var/www/html/lib/config.php';

use repository\UserRepository;
use log\UserLogger;

$userRepository = new UserRepository();
$logger = new UserLogger();

try {
    $users = $userRepository->getUsersByRole('user');
    $total = 1;
} catch (PDOException $e) {
    throw new PDOException($e->getMessage(), (int)$e->getCode());
}

?>
<!DOCTYPE html>
<html>

<head>
    <?php include '/var/www/html/includes/header.php' ?>
    <meta charset='utf-8'>
</head>

<body>
    <div class="container mt-5">
        <h2 class="text-center mb-4">사용자 목록</h2>

        <!-- .scrollable-table 클래스를 추가한 div로 감싼 테이블 -->
        <div class="scrollable-table">
            <table class="table table-bordered table-striped">
                <thead class="thead-dark">
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
                            <td class="text-center"><?= ($user->getAuthority() == 1) ? '허용' : '불가'; ?></td>
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
</body>
<footer>
    <?php include '/var/www/html/includes/footer.php' ?>
</footer>

</html>
<style>
    /* 스크롤바가 추가될 부분의 스타일 */
    .scrollable-table {
        overflow-x: auto;
        max-height: 550px;
        /* 필요에 따라 조절 */
    }

    /* 테이블 스타일 유지 */
    .table-bordered {
        border-collapse: collapse;
        width: 100%;
    }
</style>