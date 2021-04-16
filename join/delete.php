<?php
    /*-- 회원 삭제 backend --*/
    require_once ('../db/db_info.php') ;

    $mem_id = preg_replace('#[^a-zA-Z0-9]#', '', $_POST['mem_id']);

    if( isset($mem_id) && $mem_id != '' )
    {
        $c = new db_member();
        $confirm = $c -> UserDelete($mem_id) ;

        if ( $confirm == 1 ) // 삭제 완료
        {
            echo json_encode(array('result' => '1'));
            session_start();
            session_destroy();
        }
        else // 삭제 실패
        {
            echo json_encode(array('result' => '-1'));
        }
    }
?>