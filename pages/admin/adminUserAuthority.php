<?php
/**
 * 관리자 -> 사용자 권한을 변경하는 페이지
 */
session_start();
include '/var/www/html/lib/config.php';

use repository\UserRepository;
use log\UserLogger;

$userRepository = new UserRepository();
$logger = new UserLogger();

$users = $userRepository->getUsersByRole('user');
$total = 1;
?>
<!DOCTYPE html>
<html>

<head>
    <?php include '/var/www/html/includes/header.php' ?>
    <meta charset='utf-8'>
</head>

<body>
    <div class="container mt-5">
        <h2 class="text-center mb-5">사용자 목록</h2>

        <!-- .scrollable-table 클래스를 추가한 div로 감싼 테이블 -->
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
<footer>
    <?php include '/var/www/html/includes/footer.php' ?>
</footer>
</html>
