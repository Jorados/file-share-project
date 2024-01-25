<?php

namespace repository;

use database\DatabaseConnection;

abstract class BaseRepository{
    protected $pdo;

    public function __construct() {
        $this->pdo = DatabaseConnection::getInstance()->getConnection();
    }
}

?>