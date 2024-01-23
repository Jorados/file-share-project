<?php
/**
 * 메일 처리 클래스
 */
namespace mail;

use database\DatabaseConnection;

class SendMail {
    public $pdo;

    public function __construct() {
        $this->pdo = DatabaseConnection::getInstance()->getConnection();
    }

    /**
     * @param $subject
     * @param $message
     * role = 'user' 글 작성 시 모든 관리자에게 메일 전송 (gmail 안됨)
     */
    public function sendToAdmins($subject, $message) {
        $query = "SELECT email FROM user WHERE role = 'admin'";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();

        $secret = include '/var/secret/secret.php';
        $username = $secret['username'];
        $password = $secret['password'];
        $headers = 'From: admin@example.com';

        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $to = $row['email'];
            mail($to, $subject, $message, $headers, '-f' . $username);
        }
    }

    /**
     * @param $subject
     * @param $message
     * @param $boardUser_email
     * @return bool
     * 글 열람 상태 변경 시
     * 해당 글 작성자에게 메일 전송
     */
    public function sendToUser($subject, $message,$boardUser_email) {
        $to = $boardUser_email;
        $headers = 'From: admin@example.com';

        // 가리자.
        $secret = include '/var/secret/secret.php';
        $username = $secret['username'];
        $password = $secret['password'];

        if (mail($to, $subject, $message, $headers, '-f' . $username)) {
            return true;
        } else {
            return false;
        }
    }
}

?>