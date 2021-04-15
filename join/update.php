<?php
    /*-- 회원 정보 변경 backend --*/
    require_once ('../db/db_info.php') ;

    if ( isset($_POST['mem_id']) && $_POST['mem_id'] != '' 
        && isset($_POST['name']) && $_POST['name']!= '' 
        && isset($_POST['password']) && $_POST['password'] != '' 
        && isset($_POST['email']) && $_POST['email'] != '' )
    {       
        $mem_id = preg_replace('#[^a-zA-Z0-9]#', '', $_POST['mem_id']);
        $name = preg_replace('#[^가-힣ㄱ-ㅎㅏ-ㅣa-zA-Z0-9]#', '', $_POST['name']);
        $password = $_POST['password'];
        $email = $_POST['email'];

        $c = new db_member() ;

        $user = $c -> UserUpdate($mem_id, $name, $password, $email ) ;

        if( $user )
        {
            if (!isset($_SESSION)) 
            {
                session_start();
            }
            
            $_SESSION['mem_id'] = $user['mem_id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['level'] = $user['level'];
            $_SESSION['email'] = $user['email'];
            
            echo json_encode(array('result' => '1')); // 회원 정보 변경 성공
        }
        else
        {
            echo json_encode(array('result' => '-1'));// 회원 정보 변경 실패
        }
    }
    else
    {
        echo json_encode(array('result' => '-2'));  // 데이터 오류 안넘어옴
    }
?>