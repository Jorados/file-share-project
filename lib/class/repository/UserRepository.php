<?php

namespace repository;

use database\DatabaseConnection;
use database\DatabaseController;
use dataset\User;

class UserRepository{

    public $pdo;

    public function __construct(){
        $this->pdo = DatabaseConnection::getInstance()->getConnection();
    }

    // 로그인
    public function loginUser(User $user){
        $stmt = $this->pdo->prepare("SELECT * FROM user WHERE email = :email");
        $stmt->execute([
            'email' => $user->getEmail()
        ]);
        $userData = $stmt->fetch(\PDO::FETCH_ASSOC);

        if ($userData && password_verify($user->getPassword(), $userData['password'])) return new User($userData);
        return null;
    }

    // 이메일을 사용하여 사용자 조회 및 비밀번호 업데이트
    public function updateUserPassword(User $user){
        $query = "UPDATE user SET password = :password WHERE email = :email";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([
            'password' => $user->getPassword(),
            'email' => $user->getEmail()
        ]);
    }

    // available 값을 1로 업데이트
    public function updateAvailableStatus(User $user) {
        $query = "UPDATE user SET available = 1 WHERE email = :email";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([
            'email'=> $user->getEmail()
        ]);
    }

    public function getUserById($user_id){
        $userQuery = "SELECT * FROM user WHERE user_id = :user_id";
        $stmt = $this->pdo->prepare($userQuery);
        $stmt->bindParam(':user_id', $user_id, \PDO::PARAM_INT);
        $stmt->execute();
        return new User($stmt->fetch(\PDO::FETCH_ASSOC));
    }

    public function getUserByEmail(User $user){
        $stmt = $this->pdo->prepare('SELECT available, authority FROM user WHERE email = :email');
        $stmt->execute(['email' => $user->getEmail()]);
        return new User($stmt->fetch(\PDO::FETCH_ASSOC));
    }

    public function getUsersByRole($role){
        $stmt = $this->pdo->prepare('SELECT * FROM user WHERE role = :role');
        $stmt->execute(['role' => $role]);
        return DatabaseController::arrayMapObjects(new User(), $stmt->fetchAll(\PDO::FETCH_ASSOC));
    }

    public function updateUserRole(User $user){
        $stmt = $this->pdo->prepare('UPDATE user SET authority = :newRole WHERE user_id = :userId');
        $stmt->execute(['newRole' => $user->getRole(), 'userId' => $user->getUserId()]);
    }

    public function updateUserDetails(User $user){
        $updateStmt = $this->pdo->prepare('UPDATE user SET email = :email, password = :password, username = :username, phone = :phone WHERE user_id = :user_id');
        $updateStmt->execute([
            'email' => $user->getEmail(),
            'password' => $user->getPassword(),
            'username' => $user->getUsername(),
            'phone' => $user->getPhone(),
            'user_id' => $user->getUserId()
        ]);
    }

    public function getUserEmailById(User $user){
        $query = "SELECT email FROM user WHERE user_id = :user_id";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([
            'user_id'=>$user->getUserId()
        ]);
        return new User($stmt->fetch(\PDO::FETCH_ASSOC));
    }

    public function getUserIdByEmail(User $user){
        $query = "SELECT user_id FROM user WHERE email = :email";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([
            'email'=>$user->getEmail()
        ]);
        return new User($stmt->fetch(\PDO::FETCH_ASSOC));
    }

    public function createUser(User $user){
        $query = "INSERT INTO user (email, password, username, phone) VALUES (:email, :password, :username, :phone)";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([
            'email'=>$user->getEmail(),
            'password'=>$user->getPassword(),
            'username'=>$user->getUsername(),
            'phone'=>$user->getPhone()
        ]);
    }

    public function isEmailDuplicate(User $user) {
        $query = 'SELECT COUNT(*) FROM user WHERE email = :email';
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([
            'email'=>$user->getEmail()
        ]);
        $count = $stmt->fetchColumn();

        return $count > 0;
    }
}
