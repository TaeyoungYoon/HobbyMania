<?php
    /*-- 회원 가입시 중복 아이디 및 별명 체크 backend --*/
    require_once ('../db/db_info.php') ;
    
    $mem_id = preg_replace('#[^a-zA-Z0-9]#', '', $_POST['mem_id']);
    $name = preg_replace('#[^가-힣ㄱ-ㅎㅏ-ㅣa-zA-Z0-9]#', '', $_POST['name']);
    $current_name = preg_replace('#[^가-힣ㄱ-ㅎㅏ-ㅣa-zA-Z0-9]#', '',  $_POST['current_name']);
    $m = $_GET['m'] ;
    
    if( $m == 'n' ) // 최초 가입시 체크
    {
        if ( isset($mem_id) && $mem_id != '' )
        {
            $c = new db_member();
            if ( $c->checkUserID($mem_id) )
            {
                echo json_encode(array('result' => '0')); // 회원 아이디 있음 중복 
            }
            else
            {
                echo json_encode(array('result' => '1')); // 회원 아이디 없음 사용가능  
            }
        } 
        else if( isset($name) && $name != '' )
        {
            $c = new db_member();
            if ( $c->checkUserName($name) )
            {
                echo json_encode(array('result' => '0'));; 
            }
            else
            {
                echo json_encode(array('result' => '1'));; 
            }
        } 
        else
        {
            echo json_encode(array('result' => '-2'));// 입력받은 데이터에 문제가 있을 경우
        }
    }
    else if( $m == 'c' ) // 회원 변경시 체크
    {
        if( isset($name) && $name != '' )
        {
            $c = new db_member();
            if ( $c->changeCheckUserName($name, $current_name) )
            {
                echo json_encode(array('result' => '0'));; 
            }
            else
            {
                echo json_encode(array('result' => '1'));; 
            }
        } 
        else
        {// 입력받은 데이터에 문제가 있을 경우
            echo json_encode(array('result' => '-2'));// 입력받은 데이터에 문제가 있을 경우
        }
    }

?>