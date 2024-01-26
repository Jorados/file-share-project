<?php
/**
 * Repository 모듈화를 위한 부모클래스
 */

namespace repository;

use database\DatabaseConnection;

abstract class BaseRepository{
    protected $pdo;

    protected $table;

    public function setTable($table){
        $this->table=$table;
    }

    public function __construct($table=null) {
        $this->pdo = DatabaseConnection::getInstance()->getConnection();
        $this->table = $table;
    }

    protected function insert($table, $data){
        try {
            $columns = implode(', ', array_keys($data));
            $values = ':' . implode(', :', array_keys($data));
            $sql = "INSERT INTO $table ($columns) VALUES ($values)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($data);
        } catch (\PDOException $e) {
            // 오류 메시지를 기록하거나 출력합니다.
            echo '오류: ' . $e->getMessage();
        }
    }
}

?>