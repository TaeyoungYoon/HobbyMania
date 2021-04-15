<?php
    /*-- 로그 아웃 / 세션 만료시 로그아웃  backend --*/
    $root = realpath($_SERVER["DOCUMENT_ROOT"]);
    require_once ($root.'/lib/common.php');
    session_start();
    session_unset(); 
    session_destroy();

    messageAlert('로그아웃 되었습니다.');
?>