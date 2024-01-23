<?php

/**
 * 데이터베이스 연결 파일
 */

namespace database;

class DatabaseConnection {
    private $host = 'localhost';
    private $db   = 'seongjinDB';
    private $user = 'USERNAME';
    private $pass = 'passWORD@3';
    private $charset = 'utf8mb4';
    private $pdo;
    private static $instance;

    private function __construct() {
        $dsn = "mysql:host=$this->host;dbname=$this->db;charset=$this->charset";
        $options = [
            \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
            \PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            $this->pdo = new \PDO($dsn, $this->user, $this->pass, $options);
        } catch (\PDOException $e) {
            throw new \PDOException($e->getMessage(), (int)$e->getCode());
        }
    }

    /**
     * @return DatabaseConnection
     * 싱글톤 인스턴스
     * 사용하려는 클래스 생성자에 "$this->pdo = DatabaseConnection::getInstance()->getConnection();" 방식으로 사용해주면 좋다.
     * PDO를 생성해서 주입해주는 과정이 생략된다.
     * 결론적으로 코드가 짧아짐.
     */
    // 메모리 하나만 이용해서 계속 인스턴스 대여.
    public static function getInstance() {
        // 인스턴스가 아직 생성되지 않았을 경우
        if (self::$instance === null) {
            // DatabaseConnection 클래스의 새로운 인스턴스를 생성하여 $instance에 할당
            self::$instance = new DatabaseConnection();
        }
        // 현재 생성된 (또는 이미 존재하는) 인스턴스 반환
        return self::$instance;
    }

    // pdo 사용
    public function getConnection() {
        return $this->pdo;
    }
}
?>
