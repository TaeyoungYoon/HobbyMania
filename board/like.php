<?php
    /*-- 게시글 추천 backend --*/

    require_once ('../db/db_info.php') ;

    if ( isset($_POST['p_idx']) && $_POST['p_idx'] != ''
    && isset($_POST['user_id']) && $_POST['user_id'] != '' ) 
    {
        $p_idx = $_POST['p_idx'];
        $mem_id = $_POST['user_id'];

        $c = new db_board();

        $selectLike = $c -> selectLike($p_idx, $mem_id);

        if( isset($selectLike) && $selectLike != '' )
        {
            foreach ($selectLike as $val)
            {
                $islike = $val['islike'];
            }
        }

        if( !isset($islike) || $islike == 'N' )
        {   
            $updateLike = $c -> updateLike($p_idx, $mem_id);
        
            if ( $updateLike == 1 )
            {
                echo json_encode(array('result' => '1')); // 좋아요 성공
            }
            else
            {
                echo json_encode(array('result' => '-1'));// 좋아요  실패
            }
        }
        else
        {
            echo json_encode(array('result' => '-2'));// 이미 좋아요 누른 포스트
        }
    }
    else
    {
        echo json_encode(array('result' => '-3'));//데이터 안들어옴
    }


?>