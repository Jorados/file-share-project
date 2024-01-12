<?php
session_start();

include '/var/www/html/database/DatabaseConnection.php';
include '/var/www/html/repository/userRepository.php';
include '/var/access_logs/UserLogger.php';

$dbConnection = new DatabaseConnection();
$pdo = $dbConnection->getConnection();

$userRepository = new UserRepository($pdo);
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
    <?php include '/var/www/html/includes/header.php'?>
    <?php include '/var/www/html/includes/adminNavibar.php'?>
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
            foreach ($users as $user) {
                ?>
                <tr>
                    <td class="text-center"><?php echo $user['email']?></td>
                    <td class="text-center"><?php echo $user['username']?></td>
                    <td class="text-center"><?php echo $user['phone']?></td>
                    <td class="text-center"><?php echo ($user['authority'] == 1) ? '허용' : '불가'; ?></td>
                    <td class="text-center">
                        <a href="/action/updateRole.php?change_role=<?php echo $user['authority'] ?>&user_id=<?php echo $user['user_id'] ?>" class="btn btn-sm btn-primary">
                            권한 변경하기
                        </a>
                    </td>
                    <td class="text-center">
                        <!-- 정보 변경 버튼 -->
                        <a href="/admin/adminUserEdit.php?user_id=<?php echo $user['user_id']; ?>" class="btn btn-sm btn-warning">
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
    <?php include '/var/www/html/includes/footer.php'?>
</footer>
</html>
<style>
    /* 스크롤바가 추가될 부분의 스타일 */
    .scrollable-table {
        overflow-x: auto;
        max-height: 550px; /* 필요에 따라 조절 */
    }

    /* 테이블 스타일 유지 */
    .table-bordered {
        border-collapse: collapse;
        width: 100%;
    }
</style>