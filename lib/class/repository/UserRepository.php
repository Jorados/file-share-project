<?php
/**
 * 데이터모델 User sql 레포지토리
 */
namespace repository;

use database\DatabaseConnection;
use database\DatabaseController;
use dataset\User;

class UserRepository{

    public $pdo;

    public function __construct(){
        $this->pdo = DatabaseConnection::getInstance()->getConnection();
    }

    /**
     * 로그인
     * @param User $user
     * @return User|null
     */
    public function loginUser(User $user){
        $stmt = $this->pdo->prepare("SELECT * FROM user WHERE email = :email");
        $stmt->execute([
            'email' => $user->getEmail()
        ]);
        $userData = $stmt->fetch(\PDO::FETCH_ASSOC);

        if ($userData && password_verify($user->getPassword(), $userData['password'])) return new User($userData);
        return null;
    }

    /**
     * 이메일을 사용하여 User read 및 password update
     * @param User $user
     */
    public function updateUserPassword(User $user){
        $query = "UPDATE user SET password = :password WHERE email = :email";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([
            'password' => $user->getPassword(),
            'email' => $user->getEmail()
        ]);
    }

    /**
     * available 값을 1로 update
     * @param User $user
     */
    public function updateAvailableStatus(User $user) {
        $query = "UPDATE user SET available = 1 WHERE email = :email";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([
            'email'=> $user->getEmail()
        ]);
    }

    /**
     * User read
     * @param $user_id
     * @return User
     */
    public function getUserById($user_id){
        $userQuery = "SELECT * FROM user WHERE user_id = :user_id";
        $stmt = $this->pdo->prepare($userQuery);
        $stmt->bindParam(':user_id', $user_id, \PDO::PARAM_INT);
        $stmt->execute();
        return new User($stmt->fetch(\PDO::FETCH_ASSOC));
    }

    /**
     * role에 따른 User readAll
     * @param $role
     * @return array|\dataset\BaseModel[]
     */
    public function getUsersByRole($role){
        $stmt = $this->pdo->prepare('SELECT * FROM user WHERE role = :role');
        $stmt->execute(['role' => $role]);
        return DatabaseController::arrayMapObjects(new User(), $stmt->fetchAll(\PDO::FETCH_ASSOC));
    }

    /**
     * User role update
     * @param User $user
     */
    public function updateUserRole(User $user){
        $stmt = $this->pdo->prepare('UPDATE user SET authority = :newRole WHERE user_id = :userId');
        $stmt->execute(['newRole' => $user->getRole(), 'userId' => $user->getUserId()]);
    }

    /**
     * User update
     * @param User $user
     */
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

    /**
     * User의 id값을 이용한 email read
     * @param User $user
     * @return User
     */
    public function getUserEmailById(User $user){
        $query = "SELECT email FROM user WHERE user_id = :user_id";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([
            'user_id'=>$user->getUserId()
        ]);
        return new User($stmt->fetch(\PDO::FETCH_ASSOC));
    }

    /**
     * User의 email값을 이용한 user_id read
     * @param User $user
     * @return User
     */
    public function getUserIdByEmail(User $user){
        $query = "SELECT user_id FROM user WHERE email = :email";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([
            'email'=>$user->getEmail()
        ]);
        return new User($stmt->fetch(\PDO::FETCH_ASSOC));
    }

    /**
     * User create
     * @param User $user
     */
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

    /**
     * 중복 User cound read
     * @param User $user
     * @return bool
     */
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
