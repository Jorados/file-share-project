<?php

class SendMail {
    public $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    // role = 'user' 글 작성 시 모든 관리자에게 메일 전송 (gmail 안됨)
    public function sendToAdmins($subject, $message) {
        $query = "SELECT email FROM user WHERE role = 'admin'";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();

        $adminEmails = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $adminEmails[] = $row['email'];
        }

        $to = implode(', ', $adminEmails);
        $headers = 'From: admin@mkSeongjin.com';

        // 가리자.
        $username = 'seongjin8860@naver.com';
        $password = 'cho980625.';

        if (mail($to, $subject, $message, $headers, '-f' . $username)) {
            return true;
        } else {
            return false;
        }
    }

    // 글 열람 상태 변경 시
    // 해당 글 작성자에게 메일 전송
    public function sendToUser($subject, $message,$boardUser_email) {
        $to = $boardUser_email;
        $headers = 'From: admin@mkSeongjin.com';

        // 가리자.
        $username = 'seongjin8860@naver.com';
        $password = 'cho980625.';

        if (mail($to, $subject, $message, $headers, '-f' . $username)) {
            return true;
        } else {
            return false;
        }
    }
}

?>