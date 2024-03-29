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
        $data = ['email' => $user->getEmail()];
        $stmt = $this->select($this->table,  $data);
        return ($userData = $stmt->fetch(\PDO::FETCH_ASSOC)) && password_verify($user->getPassword(), $userData['password']) ? new User($userData) : null;
    }


    /**
     * User read
     * @param $user_id
     * @return User
     */
    public function getUserById($user_id){
        $data = ['user_id'=>$user_id];
        $stmt = $this->select($this->table, $data);
        return new User($stmt->fetch(\PDO::FETCH_ASSOC));
    }

    /**
     * role에 따른 User readAll ( 이메일, 이름 검색 추가 )
     * @param string $role
     * @return array|\dataset\BaseModel[]
     */
    public function getUserAuthorityList($role, $searchType = null, $searchQuery = null) {
        $whereClause = $this->buildWhereClause($role, $searchType, $searchQuery);
        $query = "SELECT * FROM user WHERE {$whereClause['strArr']}";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute($whereClause['paramArr']);
        return DatabaseController::arrayMapObjects(new User(), $stmt->fetchAll(\PDO::FETCH_ASSOC));
    }

    private function buildWhereClause($role, $searchType = null, $searchQuery = null) {
        $whereConditions = [];
        $paramArr = [];

        if ($searchType == 'email') {
            $whereConditions[] = "email LIKE :email";
            $paramArr[':email'] = "%{$searchQuery}%";
        } else if ($searchType == 'username') {
            $whereConditions[] = "username LIKE :username";
            $paramArr[':username'] = "%{$searchQuery}%";
        }

        $whereConditions[] = "role = :role";
        $paramArr[':role'] = $role;

        $strArr = !empty($whereConditions) ? implode(" AND ", $whereConditions) : "";
        return ['paramArr'=>$paramArr, 'strArr'=>$strArr];
    }

    /**
     * 중복 User email count read
     * @param User $user
     * @return bool
     */
    public function isEmailDuplicate(User $user) {
        return $this->isDuplicate('email', $user->getEmail());
    }

    /**
     * 중복 User username count read
     * @param User $user
     * @return bool
     */
    public function isUsernameDuplicate(User $user) {
        return $this->isDuplicate('username', $user->getUsername());
    }

    private function isDuplicate($columnName, $columnValue) {
        $query = "SELECT COUNT(*) FROM user WHERE $columnName = :columnValue";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([$columnName => $columnValue]);
        $count = $stmt->fetchColumn();
        return $count > 0;
    }

    /**
     * User의 id값을 이용한 email read
     * @param User $user
     * @return User
     */
    public function getUserEmailById(User $user){
        $read = ['email','username'];
        $data = ['user_id'=>$user->getUserId()];
        $stmt = $this->select($this->table, $data, $read);
        return new User($stmt->fetch(\PDO::FETCH_ASSOC));
    }

    /**
     * User의 email값을 이용한 user_id read
     * @param User $user
     * @return User
     */
    public function getUserIdByEmail(User $user){
        $read = ['user_id'];
        $data = ['email'=>$user->getEmail()];
        $stmt = $this->select($this->table, $data, $read);
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
        $where = ['user_id' => $user->getUserId()];
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

    public function getUserByEmail(User $user){
        $data = ['email'=>$user->getEmail()];
        $stmt = $this->select($this->table, $data);
        return new User($stmt->fetch(\PDO::FETCH_ASSOC));
    }

    public function getUserByUsername(User $user){
        $data = ['username'=>$user->getUsername()];
        $stmt = $this->select($this->table, $data);
        return new User($stmt->fetch(\PDO::FETCH_ASSOC));
    }


}
