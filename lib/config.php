<?php
//error_reporting(E_ALL);
//ini_set('display_errors', 1);

require '/var/www/html/vendor/autoload.php';  // 절대 경로로 수정

// 경로 변수 설정
define('DOCUMENT_ROOT', "/var/www/html");
define('LIB_PATH', DOCUMENT_ROOT . "/lib");
define('CLASS_PATH', LIB_PATH . "/class");
define('INCLUDE_PATH', DOCUMENT_ROOT . "/includes");
define('ASSTETS_PATH', DOCUMENT_ROOT . "/assets");
define('FILE_PATH', DOCUMENT_ROOT . "/file");
define('INCULEDS_PATH',DOCUMENT_ROOT . "/includes");
define('PAGES_PATH',DOCUMENT_ROOT . '/pages');
define('ADMIN_PATH',PAGES_PATH . '/admin');



