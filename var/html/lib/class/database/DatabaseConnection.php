<?php

//싱글턴
class DatabaseConnection
{
    private $host = 'localhost';
    private $db   = 'seongjinDB';
    private $user = 'USERNAME';
    private $pass = 'passWORD@3';
    private $charset = 'utf8mb4';
    private $pdo;

    public function __construct()
    {
        $dsn = "mysql:host=$this->host;dbname=$this->db;charset=$this->charset";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            $this->pdo = new PDO($dsn, $this->user, $this->pass, $options);
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage(), (int)$e->getCode());
        }
    }

    public function connect()
    {
    }

    // pdo 사용
    public function getConnection()
    {
        return $this->pdo;
    }
}
