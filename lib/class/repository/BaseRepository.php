<?php
/**
 * Repository 모듈화를 위한 부모클래스
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

            $query = "UPDATE $table SET {$set} {$where}";
            $params = array_merge($data, $where_data);
            $stmt = $this->pdo->prepare($query);
            $stmt->execute($params);
        } catch (\PDOException $e) {
            echo '오류: ' . $e->getMessage();
        }
    }

    protected function delete($table, $where_data) {
        try {
            $where = '';
            if (!empty($where_data)) {
                $where = 'WHERE ' . implode(' AND ', array_map(function ($column) {
                        return "$column = :$column";
                    }, array_keys($where_data)));
            }

            $query = "DELETE FROM {$table} {$where}";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute($where_data);
        } catch (\PDOException $e) {
            echo '오류: ' . $e->getMessage();
        }
    }

    // select 하는게 sql마다 다를 수 있음 ,,,,,
    protected function select($table, $where){

    }
}

?>