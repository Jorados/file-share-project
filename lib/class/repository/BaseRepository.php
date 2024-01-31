<?php
/**
 * Repository 모듈화를 위한 부모클래스
 * 기본적인 CRUD 기능을 하는 DML에 대해서만 SQL 모듈화 진행
 * 그 이상의 모듈화는 오히려 복잡성을 불러온다.
 */

namespace repository;

use database\DatabaseConnection;

abstract class BaseRepository{

    protected $pdo;
    protected $table;

    public function __construct() {
        $this->pdo = DatabaseConnection::getInstance()->getConnection();
    }

    public function setTable($table){
        $this->table=$table;
    }

    /**
     * 기본 insert sql 구현 메서드
     * @param $table
     * @param $data
     */
    protected function insert($table, $data){
        try {
            $columns = implode(', ', array_keys($data));
            $values = ':' . implode(', :', array_keys($data));
            $sql = "INSERT INTO $table ($columns) VALUES ($values)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($data);
        } catch (\PDOException $e) {
            echo '오류: ' . $e->getMessage();
        }
    }

    /**
     * 기본 update sql 구현 메서드
     * @param $table
     * @param $data
     * @param $where_data
     */
    protected function update($table, $data, $where_data){
        try {
            // SET 부분 변수
            $set = implode(', ', array_map(function ($column) {
                return "$column = :$column";
            }, array_keys($data)));

            // WHERE 부분 변수
            $where = '';
            if (!empty($where_data)) {
                $where = 'WHERE ' . implode(' AND ', array_map(function ($column) {
                        return "$column = :$column";
                    }, array_keys($where_data)));
            }

            $sql = "UPDATE $table SET {$set} {$where}";
            $params = array_merge($data, $where_data);
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
        } catch (\PDOException $e) {
            echo '오류: ' . $e->getMessage();
        }
    }

    /**
     * 기본 delete sql 구현 메서드
     * @param $table
     * @param $where_data
     */
    protected function delete($table, $where_data) {
        try {
            $where = '';
            if (!empty($where_data)) {
                $where = 'WHERE ' . implode(' AND ', array_map(function ($column) {
                        return "$column = :$column";
                    }, array_keys($where_data)));
            }

            $sql = "DELETE FROM {$table} {$where}";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($where_data);
        } catch (\PDOException $e) {
            echo '오류: ' . $e->getMessage();
        }
    }

    /**
     * 기본 select sql 구현 메서드
     * @param $table
     * @param null $read
     * @param $where_data
     * @return false|\PDOStatement
     */
    protected function select($table, $read = null, $where_data){
        try{
            $select = '';
            $where = '';

            // select
            if(empty($read) || $read == null) $select = '*';
            else $select = implode(', ', $read);

            // where
            if (!empty($where_data)) {
                $where = 'WHERE ' . implode(' AND ', array_map(function ($column) {
                        return "$column = :$column";
                    }, array_keys($where_data)));
            }

            $sql = "SELECT {$select} FROM {$table} {$where}";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($where_data);
            return $stmt;
        } catch (\PDOException $e){
            echo '오류: ' . $e->getMessage();
        }
    }
}

?>