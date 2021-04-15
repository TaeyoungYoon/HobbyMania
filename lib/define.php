<?php
    /*-- server --*/
    $root = realpath($_SERVER["DOCUMENT_ROOT"]);
    $urlLead = $_SERVER['REQUEST_URI'] ;

    /*-- define variable --*/
    $_ENV['TITLE'] = '취미MANIA' ;
    $_ENV['b_001'] = '농구' ;
    $_ENV['b_002'] = '배구' ;
    $_ENV['b_003'] = '축구' ;
    $_ENV['l_001'] = '뿌리' ;
    $_ENV['l_002'] = '새싹' ;
    $_ENV['l_003'] = '잡초' ;
    $_ENV['l_004'] = '관리자' ;

    /*-- session --*/
    session_start();

    if( isset($_SESSION['LAST_ACT']) && time() - $_SESSION['LAST_ACT'] > 43200){
        $msg = "세션이 종료되어 로그아웃 됩니다." ;
        echo '<script type="text/javascript">alert("' . $msg . '");</script>';
        header('Location: /join/logout.php');
    }

    $_SESSION['LAST_ACT'] = time();

?>