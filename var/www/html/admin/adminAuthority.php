<?php
session_start();

include '/var/www/html/database/DatabaseConnection.php';
include '/var/repository/userRepository.php';

$dbConnection = new DatabaseConnection();
$pdo = $dbConnection->getConnection();

$userRepository = new UserRepository($pdo);

try {
    $users = $userRepository->getUsersByRole('user');
    $total = 1;

} catch (PDOException $e) {
    throw new PDOException($e->getMessage(), (int)$e->getCode());
}

// 사용자의 권한 변경
if (isset($_GET['change_role']) && isset($_GET['user_id'])) {
    $userId = $_GET['user_id'];
    $newRole = ($_GET['change_role'] == 1) ? 0 : 1;

    try {
        $stmt = $userRepository->updateUserRole($userId, $newRole);
        header("Location: /admin/adminAuthority.php");
        exit;
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
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
                    <a href="?change_role=<?php echo $user['authority'] ?>&user_id=<?php echo $user['user_id'] ?>" class="btn btn-sm btn-primary">
                        권한 변경하기
                    </a>
                </td>
                <td class="text-center">
                    <!-- 정보 변경 버튼 -->
                    <a href="/admin/adminUserEdit.php?user_id=<?php echo $user['user_id']; ?>" class="btn btn-sm btn-secondary">
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
</body>
<footer>
    <?php include '/var/www/html/includes/footer.php'?>
</footer>
</html>
