<?php

namespace repository;

use database\DatabaseConnection;
use dataset\User;

class UserRepository{

    public $pdo;

    public function __construct(){
        $this->pdo = DatabaseConnection::getInstance()->getConnection();
    }

    // 로그인
    public function loginUser($email, $password){
        $stmt = $this->pdo->prepare("SELECT * FROM user WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $userData = $stmt->fetch(\PDO::FETCH_ASSOC);

        if ($userData && password_verify($password, $userData['password'])) return new User($userData);
        return null;
    }

    // 이메일을 사용하여 사용자 조회 및 비밀번호 업데이트
    public function updateUserPassword($email, $hashed_password){
        $query = "UPDATE user SET password = :password WHERE email = :email";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':password', $hashed_password, \PDO::PARAM_STR);
        $stmt->bindParam(':email', $email, \PDO::PARAM_STR);
        $stmt->execute();
    }

    // available 값을 1로 업데이트
    public function updateAvailableStatus($email) {
        $query = "UPDATE user SET available = 1 WHERE email = :email";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':email', $email, \PDO::PARAM_STR);
        $stmt->execute();
    }

    public function getUserById($user_id){
        $userQuery = "SELECT * FROM user WHERE user_id = :user_id";
        $stmt = $this->pdo->prepare($userQuery);
        $stmt->bindParam(':user_id', $user_id, \PDO::PARAM_INT);
        $stmt->execute();
        return new User($stmt->fetch(\PDO::FETCH_ASSOC));
    }

    public function getUserByEmail($email){
        $stmt = $this->pdo->prepare('SELECT available, authority FROM user WHERE email = :email');
        $stmt->execute(['email' => $email]);
        return new User($stmt->fetch(\PDO::FETCH_ASSOC));
    }

    public function getUsersByRole($role){
        $stmt = $this->pdo->prepare('SELECT * FROM user WHERE role = :role');
        $stmt->execute(['role' => $role]);

        // array_map : 배열의 모든 요소에 콜백함수를 적용해 새로운 배열을 반환하는 함수
        return array_map(
            function ($user) { // 여기서의 $user는 $stmt배열의 요소를 나타냄.
                return new User($user);
            },
            $stmt->fetchAll(\PDO::FETCH_ASSOC)
        );
    }

    public function updateUserRole($userId, $newRole){
        $stmt = $this->pdo->prepare('UPDATE user SET authority = :newRole WHERE user_id = :userId');
        $stmt->execute(['newRole' => $newRole, 'userId' => $userId]);
    }


    public function updateUserDetails($user_id, $email, $hashedPassword, $username, $phone){
        $updateStmt = $this->pdo->prepare('UPDATE user SET email = :email, password = :password, username = :username, phone = :phone WHERE user_id = :user_id');
        $updateStmt->execute([
            'email' => $email,
            'password' => $hashedPassword,
            'username' => $username,
            'phone' => $phone,
            'user_id' => $user_id
        ]);
    }

    public function getUserEmailById($user_id){
        $query = "SELECT email FROM user WHERE user_id = :user_id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':user_id', $user_id, \PDO::PARAM_INT);
        $stmt->execute();
        return new User($stmt->fetch(\PDO::FETCH_ASSOC));
    }

    public function getUserIdByEmail($email){
        $query = "SELECT user_id FROM user WHERE email = :email";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':email', $email, \PDO::PARAM_INT);
        $stmt->execute();
        return new User($stmt->fetch(\PDO::FETCH_ASSOC));
    }

    public function createUser($email,$hashed_password,$username,$phone){
        $query = "INSERT INTO user (email, password, username, phone) VALUES (:email, :password, :username, :phone)";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':email', $email, \PDO::PARAM_STR);
        $stmt->bindParam(':password', $hashed_password, \PDO::PARAM_STR);
        $stmt->bindParam(':username', $username, \PDO::PARAM_STR);
        $stmt->bindParam(':phone', $phone, \PDO::PARAM_STR);
        $stmt->execute();
    }

    public function isEmailDuplicate($email) {
        $query = 'SELECT COUNT(*) FROM user WHERE email = :email';
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':email', $email, \PDO::PARAM_STR);
        $stmt->execute();
        $count = $stmt->fetchColumn();

        return $count > 0;
    }

}
