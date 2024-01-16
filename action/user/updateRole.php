<?php
session_start();
include '/var/www/html/lib/config.php';

use repository\UserRepository;
use log\UserLogger;

$userRepository = new UserRepository();
$logger = new UserLogger();

if (isset($_GET['change_role']) && isset($_GET['user_id'])) {
    $userId = $_GET['user_id'];
    $newRole = ($_GET['change_role'] == 1) ? 0 : 1;
    $adminEmail = $_SESSION['email'];

    try {
        $stmt = $userRepository->updateUserRole($userId, $newRole);
        $userEmail = $userRepository->getUserEmailById($userId);

        $logger->changeAuthority($_SERVER['REQUEST_URI'], $adminEmail, $userEmail['email'], $newRole);

        header("Location: /pages/admin/adminAuthority.php");
        exit;
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    echo "Invalid request.";
}
?>
