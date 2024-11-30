<?php
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


//오토로드
// /var/www/html/autoload.php
function myAutoloader($className) {
    $className = str_replace('\\', '/', $className);
    $path = CLASS_PATH . DIRECTORY_SEPARATOR . "{$className}.php";
//    echo $path;
    if (file_exists($path)) {
        require_once $path;
        return;
    }
}
// 오토로더 함수를 등록
spl_autoload_register('myAutoloader');



