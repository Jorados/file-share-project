<?php
class UserRepository
{

    public $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    // 로그인
    public function loginUser($email, $password)
    {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM user WHERE email = :email");
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {

                return $user;
            }
            return false;
        } catch (PDOException $e) {
            throw new Exception("Error: " . $e->getMessage());
        }
    }

    // 이메일을 사용하여 사용자 조회 및 비밀번호 업데이트
    public function updateUserPassword($email, $hashed_password)
    {
        try {
            $query = "UPDATE user SET password = :password WHERE email = :email";
            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(':password', $hashed_password, PDO::PARAM_STR);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->execute();
        } catch (PDOException $e) {
            throw new Exception("Error updating password: " . $e->getMessage());
        }
    }

    // available 값을 1로 업데이트
    public function updateAvailableStatus($email)
    {
        try {
            $query = "UPDATE user SET available = 1 WHERE email = :email";
            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->execute();

            return $stmt;
        } catch (PDOException $e) {
            throw new Exception("Error updating available status: " . $e->getMessage());
        }
    }

    public function getUserById($user_id)
    {
        try {
            $userQuery = "SELECT * FROM user WHERE user_id = :user_id";
            $stmt = $this->pdo->prepare($userQuery);
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch();
        } catch (PDOException $e) {
            die("Error while fetching user info: " . $e->getMessage());
        }
    }

    public function getUserByEmail($email)
    {
        try {
            $stmt = $this->pdo->prepare('SELECT available, authority FROM user WHERE email = :email');
            $stmt->execute(['email' => $email]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage(), (int)$e->getCode());
        }
    }

    public function getUserIdByEmail($email)
    {
        $query = "SELECT user_id FROM user WHERE email = :email";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['user_id'] ?? null;
    }

    public function adminCreateUser($email, $hashed_password, $username, $phone, $role)
    {
        $query = "INSERT INTO user (email, password, username, phone, role) VALUES (:email, :password, :username, :phone, :role)";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->bindParam(':password', $hashed_password, PDO::PARAM_STR);
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->bindParam(':phone', $phone, PDO::PARAM_STR);
        $stmt->bindParam(':role', $role, PDO::PARAM_STR);
        $stmt->execute();
    }

    public function getUsersByRole($role)
    {
        $stmt = $this->pdo->prepare('SELECT * FROM user WHERE role = :role');
        $stmt->execute(['role' => $role]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateUserRole($userId, $newRole)
    {
        $stmt = $this->pdo->prepare('UPDATE user SET authority = :newRole WHERE user_id = :userId');
        $stmt->execute(['newRole' => $newRole, 'userId' => $userId]);
    }

    public function updateUserDetails($user_id, $email, $hashedPassword, $username, $phone)
    {
        $updateStmt = $this->pdo->prepare('UPDATE user SET email = :email, password = :password, username = :username, phone = :phone WHERE user_id = :user_id');
        $updateStmt->execute([
            'email' => $email,
            'password' => $hashedPassword,
            'username' => $username,
            'phone' => $phone,
            'user_id' => $user_id
        ]);
    }

    public function getUserEmailById($user_id)
    {
        $query = "SELECT email FROM user WHERE user_id = :user_id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function getUserEmailByAdmin()
    {
        $query = "SELECT email FROM user WHERE role = 'admin'";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        return $stmt;
    }
}
