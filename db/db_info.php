<?php
    // DB 정보 입력

    define('DB_HOST','') ;
    define('DB_USER','') ; 
    define('DB_PASS', '') ; 
    define('DB_NAME','') ;
    define('DSN','sqlsrv:Server='.DB_HOST.';database='.DB_NAME) ;

    require_once ('db_member.php') ;
    require_once ('db_connection.php') ;
    require_once ('db_board.php') ;
    require_once ('db_search.php') ;
?>
