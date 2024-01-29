<?php
/**
 * 관리자 -> 사용자의 글 작성 권한 변경 액션
 */

session_start();
include '/var/www/html/lib/config.php';

use service\UserService;

$userService = new UserService();

if (isset($_GET['change_role']) && isset($_GET['user_id'])) {
    $userId = $_GET['user_id'];
    $newRole = ($_GET['change_role'] == 1) ? 0 : 1;
    $adminEmail = $_SESSION['email'];

    $result = $userService->updateRole($userId,$newRole,$adminEmail);
    echo json_encode(['status'=>$result['status'], 'content'=>$result['content']]);

    header("Location: /pages/admin/adminUserAuthority.php");
    exit;
}
?>
