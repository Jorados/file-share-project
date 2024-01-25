<?php
/**
 * Repository 모듈화를 위한 부모클래스
 */

namespace repository;

use database\DatabaseConnection;

abstract class BaseRepository{
    protected $pdo;

    public function __construct() {
        $this->pdo = DatabaseConnection::getInstance()->getConnection();
    }
}

?>