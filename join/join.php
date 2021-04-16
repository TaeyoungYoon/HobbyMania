<?php
    /*-- 회원 로그인 backend --*/
    require_once ('../db/db_info.php') ;

    $mem_id = preg_replace('#[^a-zA-Z0-9]#', '', $_POST['mem_id']);
    $password = $_POST['password'];

    if( !isset($_POST['checkok']) )
    {
        $_POST['checkok'] = false;
    }

    if ( isset($mem_id) && $mem_id != '' && isset($password) && $password != '')
    {

        $c = new db_member() ;

        $user = $c -> selectUser( $mem_id, $password ) ;

        if( $user )
        {
            if (!isset($_SESSION)) // 로그인시 세션 설정
            {
                session_start();
            }
            $_SESSION['mem_id'] = $user['mem_id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['level'] = $user['level'];
            $_SESSION['email'] = $user['email'];

            if( $_POST['checkok'] == true) // 회원 정보 변경 인증 회원에게 세션 부여
            {
                $_SESSION['update'] = 'update' ;
            }


            echo json_encode(array('result' => '1')); // 로그인 설정
        }
        else
        {
            echo json_encode(array('result' => '-1'));// 로그인 실패 
        }
    }
    else
    {
        echo json_encode(array('result' => '-2')); // 데이터가 안들어왔을 경우 오류
    }

?>