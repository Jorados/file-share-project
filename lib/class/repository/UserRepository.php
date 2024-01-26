<?php
/**
 * 데이터모델 User sql 레포지토리
 */
namespace repository;

use database\DatabaseController;
use dataset\User;

class UserRepository extends BaseRepository {

    /** 생성자 */
    public function __construct(){
        parent::__construct();
        $this->setTable('user');
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
     * @param string $role
     * @return array|\dataset\BaseModel[]
     */
    public function getUsersByRole($role){
        $stmt = $this->pdo->prepare('SELECT * FROM user WHERE role = :role');
        $stmt->execute(['role' => $role]);
        return DatabaseController::arrayMapObjects(new User(), $stmt->fetchAll(\PDO::FETCH_ASSOC));
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
     * 이메일을 사용하여 User read 및 password update
     * @param User $user
     */
    public function updateUserPassword(User $user){
        $data = ['password' => $user->getPassword()];
        $where = ['email' => $user->getEmail()];
        $this->update($this->table, $data, $where);
    }

    /**
     * available 값을 1로 update
     * @param User $user
     */
    public function updateAvailableStatus(User $user) {
        $data = ['available' => 1];
        $where = ['email' => $user->getEmail()];
        $this->update($this->table, $data, $where);
    }

    /**
     * User role update
     * @param User $user
     */
    public function updateUserRole(User $user){
        $data = ['authority' => $user->getRole()];
        $where = ['user_id' => $user->getUserId()];
        $this->update($this->table, $data, $where);
    }


    /**
     * User update
     * @param User $user
     */
    public function updateUserDetails(User $user){
        $data = [
            'email' => $user->getEmail(),
            'password' => $user->getPassword(),
            'username' => $user->getUsername(),
            'phone' => $user->getPhone()
        ];
        $where = [
            'user_id' => $user->getUserId()
        ];
        $this->update($this->table, $data, $where);
    }

    /**
     * User create
     * @param User $user
     */
    public function createUser(User $user){
        $data = [
            'email'=>$user->getEmail(),
            'password'=>$user->getPassword(),
            'username'=>$user->getUsername(),
            'phone'=>$user->getPhone()
        ];
        $this->insert($this->table, $data);
    }


}
