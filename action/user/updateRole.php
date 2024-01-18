<?php
session_start();
include '/var/www/html/lib/config.php';

use repository\UserRepository;
use log\UserLogger;
use dataset\User;

$userRepository = new UserRepository();
$logger = new UserLogger();

if (isset($_GET['change_role']) && isset($_GET['user_id'])) {
    $userId = $_GET['user_id'];
    $newRole = ($_GET['change_role'] == 1) ? 0 : 1;
    $adminEmail = $_SESSION['email'];

    $user = new User(['user_id'=>$userId , 'role'=> $newRole]);
    try {
        $stmt = $userRepository->updateUserRole($user);
        $userEmail = $userRepository->getUserEmailById($user);

        $logger->changeAuthority($_SERVER['REQUEST_URI'], $adminEmail, $userEmail->getEmail(), $user->getRole());

        header("Location: /pages/admin/adminAuthority.php");
        exit;
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    echo "Invalid request.";
}
?>
