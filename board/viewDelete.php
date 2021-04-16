<?php

    /*-- 게시글 삭제 backend --*/
    require_once ('../db/db_info.php') ;

    $p_idx = $_POST['p_idx'];

    if( isset($p_idx) && $p_idx != '' )
    {
        $c = new db_board();
        $confirm = $c -> postDelete($p_idx) ;

        if ( $confirm == 1 )
        {
            echo json_encode(array('result' => '1')); // 삭제 성공
        }
        else
        {
            echo json_encode(array('result' => '-1'));// 삭제 실패
        }
    }
?>